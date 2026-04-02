# Sprint 5

## Objectif du sprint

Ajout de produits au panier.

## Fonctionnalités

### Inclusion de scripts JS dans les pages HTML

Solution simple pour inclure des fichiers JavaScript uniquement sur les pages qui en ont besoin.

Code dans `public/product.php` :

```php
$jsScriptPathList = [
        'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
        'resources/js/product.js',
];
```

Code dans `templates/footer.php` :

```php
<?php if (isset($jsScriptPathList)) { ?>
    <?php foreach ($jsScriptPathList as $jsScriptPath) { ?>
        <script src="<?php echo $jsScriptPath; ?>"></script>
    <?php } ?>
<?php } ?>
```

#### Objectif de cette solution

L'objectif du code dans `templates/footer.php` est de permettre à chaque page PHP de définir sa propre liste de scripts JavaScript,
sans devoir modifier le template global à chaque fois.

Par exemple, la page du panier (`public/product.php`) peut définir une variable `$jsScriptPathList` avant d'inclure le footer.
Le footer parcourt alors cette liste et génère automatiquement les balises `<script>`.

En l'occurrence, le code ajouté dans la page du produit comprend à la fois un script dédié à la page, et la librairie Axios.

#### Avantage de cette solution

- Il évite de charger du JavaScript inutile sur toutes les pages.
- Il garde le template `footer.php` réutilisable.
- Il centralise l'affichage des balises `<script>` à un seul endroit.
- Il permet d'ajouter facilement plusieurs scripts pour une même page.


### Appel AJAX pour ajouter le produit au panier

Cette solution permet d'ajouter un produit au panier sans recharger la page.

#### 1. Appel de la fonction `updateBasket()` dans le HTML

<form class="add-to-basket" action="#" method="post">

    <button type="button" class="btn-primary" onclick="updateBasket()">
        Ajouter au panier
    </button>

</form>
```

Code dans `public/resources/js/product.js` :

```js
function updateBasket() {
   
    ...
    
}
```

##### Objectif

Avec le type `button`, le bouton ne déclenche plus la soumission du formulaire, contrairement au type `submit`. 
La page n'est plus rafraîchie, ce qui est indispensable pour réaliser l'appel AJAX.
À la place, grâce à l'attribut `onclick="updateBasket()"`, 
un clic sur le bouton appelle la fonction JavaScript `updateBasket()`, laquelle va gérer l'appel AJAX.
On court-circuite le fonctionnement habituel du formulaire pour ne pas rafraîchir la page: 
un clic sur le bouton se concrétise par un appel à une fonction JS.


#### 2. Récupération des données à envoyer

Code dans `public/product.php` :

```php
    
<input type="hidden" id="product-id" name="product-id" value="<?php echo $product['id']; ?>">

<select id="quantity" name="quantity">
    <?php for ($i = 1; $i <= $product['stock']; $i++) { ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
    <?php } ?>
</select>

```

Code dans `public/resources/js/product.js` :

```js
const productIdInput = document.getElementById('product-id')
const quantityInput = document.getElementById('quantity')

function updateBasket() {
    const productId = productIdInput.value
    const quantity = quantityInput.value
    
    ...
    
}
```

##### Objectif

Le code JS récupère dans la page HTML les deux informations nécessaires pour mettre à jour le panier :
- l'identifiant du produit ;
- la quantité sélectionnée par l'utilisateur.

Ces valeurs sont lues depuis les champs du formulaire grâce à `document.getElementById(...)`, puis via l'attribut `Element.value`.
Attention: la lecture de la quantité doit se faire systématiquement lors de l'appel de la fonction 
puisqu'elle peut être modifiée entre chaque clic sur le bouton d'ajout au panier.

A noter les particularités du champ `hidden`: un champ `<input type="hidden">` est un champ de formulaire invisible pour l'utilisateur. 
Il n'est pas visible sur la page, mais il est bien présent dans le DOM et sa valeur peut être envoyée lors d'une soumission de formulaire ou lue en JavaScript comme ici. 
Ce champ sert donc à transporter une information utile au traitement, sans demander à l'utilisateur de la saisir.
En général, ce type de champ est souvent utilisé pour transmettre un identifiant, un token, une information de contexte, ou encore une valeur déjà connue côté serveur.
Dans notre code, il permet de garder l'identifiant du produit dans la page afin que JavaScript puisse le récupérer avec `document.getElementById('product-id').value` puis l'envoyer à l'API lors de l'ajout au panier. 
Cela évite de l'afficher dans l'interface tout en conservant l'information nécessaire pour savoir quel produit ajouter.

#### 3. Appel AJAX et affichage des messages

Code dans `public/resources/js/product.js`, fonction `updateBasket()` :

```js
axios.postForm('api/basket/update.php', {
    product_id: productId,
    quantity: quantity
})
.then(response => {
    displayMessage(true)
})
.catch(error => {
    displayMessage(false)
});
```

```js
const validMessageElement = document.getElementById('basket-message-added')
const errorMessageElement = document.getElementById('basket-message-error')

function displayMessage(isValid)
{
    validMessageElement.style.display = isValid ? 'block' : 'none'
    errorMessageElement.style.display = isValid ? 'none' : 'block'
}
```

##### Objectif

Le corps de la fonction `updateBasket()` contient un appel AJAX qui envoie les données à l'API `api/basket/update.php` 
sans recharger la page.

Une API ("Application Programming Interface") est un point d'entrée prévu pour qu'un programme puisse communiquer avec un autre programme.
En pratique, une API reçoit des données, exécute un traitement, puis renvoie une réponse. 
Dans notre cas, l'API `api/basket/update.php` va recevoir l'identifiant du produit et la quantité, mettre à jour le panier côté serveur, 
puis renvoyer une réponse à JavaScript.

A noter la particularité de la méthode `Àxios.postForm`: elle envoie les données avec un format `Content-Type` 
identique à celui d'un formulaire HTML, 
c'est-à-dire `application/x-www-form-urlencoded` (alors qu'en général, une API fonctionne souvent avec du JSON). 
Cela facilitera la récupération des données dans notre code backend, car PHP lit ce type de données via `$_POST`.

Si l'appel réussit, alors la fonction dans `then()` est appelée.
Si l'appel échoue, alors la fonction dans `catch()` est appelée.

Le rôle de la fonction `displayMessage()` est de montrer à l'utilisateur si l'action s'est bien passée ou non.
Le fichier CSS `public/resources/css/product.css` complète cette logique en cachant les messages par défaut,
puis en les laissant apparaître uniquement quand JavaScript change leur propriété `display`.

### Ajout d'un produit en session via API

Cette solution permet de recevoir la requête AJAX, de modifier le panier stocké en session, puis de renvoyer une réponse JSON.

#### 1. Point d'entrée de l'API

Code dans `public/api/basket/update.php` :

```php
<?php

require_once __DIR__ . '/../../../src/controller.php';

updateBasket();
```

##### Objectif

Ce fichier joue le rôle de point d'entrée HTTP pour l'appel AJAX.
Quand JavaScript envoie une requête vers `api/basket/update.php`, ce fichier charge le contrôleur puis exécute la fonction `updateBasket()`.

Cela permet d'avoir une URL dédiée pour l'API du panier.

#### 2. Lecture des données envoyées par JavaScript

Code dans `src/controller.php` :

```php
function updateBasket(): void
{
    $productId = filter_var($_POST['product_id'] ?? 0, FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'] ?? 0, FILTER_VALIDATE_INT);

    ...
}
```

##### Objectif

Cette partie récupère les données envoyées par la requête AJAX.

Les valeurs `product_id` et `quantity` arrivent dans `$_POST`, puis sont validées comme entiers avec `filter_var(...)`.
Le but est de s'assurer que PHP travaille avec des données fiables avant de modifier le panier et éviter d'éventuel problème de sécurité.

Il est à noter que les valeurs sont récupérables par la superglobale `$_POST`,
car la méthode `axios.postForm()` utilisée lors de l'appel AJAX
envoie les données dans une requête HTTP de type POST dont le `content-type` est `x-www-form-urlencoded`,
c'est-à-dire un format équivalent à celui d'un envoi de formulaire HTML par défaut.

#### 3. Mise à jour du panier en session

Code dans `src/controller.php` :

```php
$originalBasket = retrieveBasketFromSession();
$modifiedBasket = updateItemIntoBasket($originalBasket, $productId, $quantity);
saveBasketIntoSession($modifiedBasket);
```

Code dans `src/session.php` :

```php
function retrieveBasketFromSession(): array
{
    if (empty($_SESSION['basket'])) {
        saveBasketIntoSession([]);
    }
    return $_SESSION['basket'];
}

function saveBasketIntoSession(array $basket): void
{
    $_SESSION['basket'] = $basket;
}
```

Code dans `src/basket.php` :

```php
function updateItemIntoBasket(array $basket, int $productId, int $quantity): array
{
    $basket[$productId] = $quantity;
    if ($basket[$productId] <= 0) {
        unset($basket[$productId]);
    }
    return $basket;
}
```

##### Objectif

Le panier est stocké en session dans `$_SESSION['basket']`.
La session permet de conserver des données propres à un visiteur entre plusieurs requêtes HTTP, même si chaque page PHP est exécutée séparément.
Ici, c'est utile parce que le panier doit rester disponible pendant toute la navigation de l'utilisateur, même s'il change de page avant de confirmer sa commande.
La clé `basket` sert à ranger ces données à un endroit précis dans `$_SESSION`.
Cela évite de mélanger le panier avec d'autres informations de session, comme un token CSRF ou d'autres données utilisateur.
Le nom de clé rend aussi le code plus lisible, car on comprend immédiatement que `$_SESSION['basket']` contient le panier.

Le code commence donc par récupérer le panier actuel, puis il appelle `updateItemIntoBasket(...)` pour modifier la quantité du produit demandé.
Si la quantité est inférieure ou égale à zéro, le produit est supprimé du panier à l'aide de la fonction `unset()`.
Sinon, sa quantité est ajoutée ou remplacée dans le tableau.
Enfin, le panier modifié est sauvegardé à nouveau dans la session.

#### 4. Réponse renvoyée à JavaScript

Code dans `src/controller.php` :

```php
header('content-type: application/json');
echo json_encode([
    'basket' => retrieveBasketFromSession(),
]);
```

##### Objectif

Après la mise à jour du panier, l'API renvoie une réponse au format JSON.

En PHP, la fonction `header(...)` permet d'envoyer un en-tête HTTP dans la réponse.
Ici, elle indique au navigateur et à JavaScript que le contenu renvoyé est du JSON et non du HTML.
JavaScript peut ainsi récupérer l'état actuel du panier après la modification.
Le header `content-type: application/json` indique donc explicitement le type de données renvoyé par l'API.

La fonction `json_encode(...)` sert ensuite à transformer une structure de données PHP, 
par exemple dans notre cas un tableau, en chaîne JSON exploitable par JavaScript.

#### Avantage de cette solution

- Le panier peut être modifié sans rechargement de page.
- Le code est séparé entre API, contrôleur, logique métier et session.
- La session permet de conserver le panier entre plusieurs requêtes HTTP.
- La réponse JSON pourra être réutilisée plus tard pour mettre à jour l'interface du panier dynamiquement.
