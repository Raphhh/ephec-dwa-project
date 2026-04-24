# Sprint 7

## Objectif du sprint

Modification des quantités du panier.

## Fonctionnalités

### Affichage d'un widget de quantité dans le panier

Cette solution permet à l'utilisateur de modifier visuellement la quantité d'un produit directement depuis la page panier, 
sans devoir revenir sur la fiche produit.

#### 1. Chargement d'un script JavaScript dédié à la page panier

Code dans `public/basket.php` :

```php
$jsScriptPathList = [
        'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
        'resources/js/basket.js'
];
```

##### Objectif

La page `basket.php` déclare ici un fichier JavaScript spécifique à son comportement.
Comme pour les autres pages du projet, cette variable est ensuite exploitée par le template commun pour inclure automatiquement le script dans le HTML.

La librairie Axios doit être chargée dans la page avant l'exécution du script métier du panier.
Cela permet au code JavaScript du panier d'effectuer facilement un appel HTTP vers l'API.

#### 2. Remplacement de l'affichage statique de la quantité

Code dans `public/basket.php` :

```php
<td>
    <span
            class="basket-quantity-widget"
            data-product-id="<?php echo $product['id']; ?>"
            data-product-stock="<?php echo $product['stock']; ?>"
    >
        <button class="basket-quantity-widget-remove-button">-</button>
        <span class="basket-quantity-widget-quantity">
            <?php echo $item['quantity']; ?>
        </span>
        <button class="basket-quantity-widget-add-button">+</button>
    </span>
</td>
```

##### Objectif

La quantité n'est plus affichée comme un simple texte.
Elle est maintenant entourée d'un petit composant HTML contenant :
- un bouton pour diminuer la quantité ;
- la quantité actuelle ;
- un bouton pour augmenter la quantité.

La classe `basket-quantity-widget` sert de point d'entrée au JavaScript et au CSS.
Les attributs `data-product-id` et `data-product-stock` permettent aussi d'attacher des informations utiles directement à l'élément HTML.

#### 3. Initialisation du comportement JavaScript sur chaque widget

Code dans `public/resources/js/basket.js` :

```js
let elements = document.getElementsByClassName('basket-quantity-widget');
for (let i = 0; i < elements.length; i++) {
    manageWidget(elements[i])
}
```

##### Objectif

Le script commence par récupérer tous les éléments HTML ayant la classe `basket-quantity-widget`.
On n'utilise pas ici `getElementById(...)` car cette méthode ne permet de récupérer qu'un seul élément, identifié de manière unique dans la page.
Or, dans le panier, il peut y avoir plusieurs lignes de produits, donc plusieurs widgets de quantité à modifier.
`getElementsByClassName(...)` est donc plus adapté, car il récupère tous les éléments qui partagent la même classe.
La différence importante est que `getElementById(...)` renvoie un seul élément, tandis que `getElementsByClassName(...)` renvoie une collection, c'est-à-dire ici une liste d'éléments qu'il faut ensuite parcourir.
Il existe aussi une autre solution alternative pour faire cela : `querySelectorAll(...)`, qui permet également de sélectionner plusieurs éléments à partir d'un sélecteur CSS.
Il parcourt ensuite cette collection pour appeler `manageWidget(...)` sur chacun d'eux.

Le but est d'appliquer le même comportement interactif à toutes les lignes du panier.
Chaque widget devient ainsi autonome et géré individuellement.

#### 4. Récupération des sous-éléments et des valeurs utiles

Code dans `public/resources/js/basket.js` :

```js
function manageWidget(widget) {

    let quantityElement = widget.querySelector('.basket-quantity-widget-quantity')
    let addButton = widget.querySelector('.basket-quantity-widget-add-button')
    let removeButton = widget.querySelector('.basket-quantity-widget-remove-button')

    let quantity = parseInt(quantityElement.innerText)
    let stock = parseInt(widget.dataset.productStock)
    let productId = parseInt(widget.dataset.productId)

    ...
}
```

##### Objectif

La fonction `manageWidget(...)` récupère d'abord les trois éléments utiles à l'intérieur du widget :
- la zone qui affiche la quantité ;
- le bouton `+` ;
- le bouton `-`.

Ici, on utilise `querySelector(...)` car cette méthode renvoie seulement le premier élément qui correspond au sélecteur CSS donné.
À l'inverse, `querySelectorAll(...)` renvoie tous les éléments correspondants sous forme de liste.
Dans ce cas précis, on cherche à récupérer un seul élément pour chaque rôle dans le widget : un affichage de quantité, un bouton `+` et un bouton `-`.
`querySelector(...)` est donc la méthode la plus simple et la plus adaptée, car `querySelectorAll(...)` serait inutilement plus lourd à manipuler.

Il faut aussi noter que ces deux méthodes existent sur `document`, mais aussi sur chaque élément HTML.
Quand on écrit `document.querySelector(...)`, la recherche se fait dans toute la page.
Quand on écrit `widget.querySelector(...)`, la recherche se fait uniquement à l'intérieur de cet élément `widget`.
Les éléments récupérés sont donc relatifs au widget courant, ce qui permet de gérer chaque ligne du panier indépendamment des autres.

Elle lit ensuite :
- la quantité actuelle à partir du texte affiché ;
- le stock maximum à partir de `data-product-stock`.

La fonction `parseInt(...)` permet de convertir ces valeurs en entiers utilisables dans les calculs JavaScript.
Pour information, il existe aussi `parseFloat(...)`, qui permet de convertir une valeur en nombre décimal.

##### 4.1. Utilisation des attributs `data-*` pour stocker des informations utiles

Code dans `public/basket.php` :

```php
data-product-id="<?php echo $product['id']; ?>"
data-product-stock="<?php echo $product['stock']; ?>"
```

Code dans `public/resources/js/basket.js` :

```js
let stock = parseInt(element.dataset.productId)
let stock = parseInt(element.dataset.productStock)
```

##### Objectif

Les attributs `data-*` permettent de stocker des données personnalisées dans le HTML.
Ici :
- `data-product-id` contient l'identifiant du produit ;
- `data-product-stock` contient son stock disponible.

Ces informations sont ensuite lisibles en JavaScript via `Element.dataset...`.
Le code JS peut ainsi réagir selon le produit concerné et selon la limite de stock,
sans devoir recalculer ou rechercher ces données ailleurs.

Attention toutefois, on remarque le changement d'écriture.
Par exemple, le HTML `data-product-id` devient le JS `dataset.productId`. 
Par ailleurs, évidemment, ce système ne fonctionne que pour les attributs de type `data-*`. 

La fonction `parseInt(...)` permet de convertir ces valeurs en entiers utilisables dans les calculs JavaScript.
Pour information, il existe aussi `parseFloat(...)`, qui permet de convertir une valeur en nombre décimal.

#### 5. Gestion du clic sur les boutons `+` et `-`

Code dans `public/resources/js/basket.js` :

```js
addButton.onclick = function () {
    quantity++
    if (quantity > stock) {
        quantity = stock
    }
    quantityElement.innerText = quantity
    ...
}

removeButton.onclick = function () {
    quantity--
    if (quantity < 0) {
        quantity = 0
    }
    quantityElement.innerText = quantity
    ...
}
```

##### Objectif

Quand l'utilisateur clique sur `+`, la quantité augmente.
Quand il clique sur `-`, la quantité diminue.

Le code encadre toutefois cette modification avec deux limites :
- la quantité ne peut pas dépasser le stock disponible ;
- la quantité ne peut pas descendre sous `0`.

Après chaque modification, le texte affiché dans le widget est mis à jour via `quantityElement.innerText`.

La syntaxe `addButton.onclick = function () { ... }` signifie que l'on assigne une fonction à exécuter automatiquement 
quand l'utilisateur clique sur cet élément.
Autrement dit, `onclick` est ici une propriété de l'élément HTML, à laquelle on donne comme valeur une fonction.

Il existe d'autres façons de faire la même chose.
On peut par exemple utiliser `addEventListener('click', function () { ... })`, 
qui est une méthode plus générale du JavaScript moderne.
On peut aussi rencontrer un attribut HTML comme `onclick="..."`, directement écrit dans la balise, 
même si cette solution est généralement moins propre.

Ici, la forme `onclick = function () { ... }` est utilisée car elle reste simple, 
directe et très lisible pour un cas où chaque bouton n'a qu'un seul comportement de clic à gérer.
Elle convient donc bien dans ce contexte pédagogique, sans ajouter de complexité inutile.

#### 6. Désactivation automatique des boutons aux limites autorisées

Code dans `public/resources/js/basket.js` :

```js
function checkButtons(addButton, removeButton, quantity, max) {
    addButton.disabled = quantity >= max
    removeButton.disabled = quantity <= 0
}
```

##### Objectif

Cette fonction évite à l'utilisateur de continuer à cliquer sur un bouton alors qu'il a déjà atteint une limite logique.
Elle est appelée une première fois lors de l'initialisation du widget, afin de mettre immédiatement les boutons dans le bon état selon la quantité de départ.
Elle est ensuite rappelée après chaque clic sur `+` ou sur `-`, une fois la quantité mise à jour.

Si la quantité est égale au stock maximum, le bouton `+` est désactivé.
Si la quantité est égale à `0`, le bouton `-` est désactivé.

L'interface devient ainsi plus cohérente :
elle ne se contente pas de corriger la valeur après coup, elle bloque aussi les actions inutiles.

#### 7. Envoi de la requête AJAX à l'API du panier

Code dans `public/resources/js/basket.js` :

```js
function updateRemote(productId, quantity) {
    axios.postForm('api/basket/update.php', {
        product_id: productId,
        quantity: quantity
    })
    
    ...
}
```

##### Objectif

La fonction `updateRemote(...)` envoie une requête AJAX POST vers `api/basket/update.php`.
Elle transmet les deux données attendues par l'API :
- `product_id` ;
- `quantity`.

La méthode `axios.postForm(...)` envoie ces valeurs dans un format équivalent à celui d'un formulaire HTML classique.
Cela reste cohérent avec l'API du sprint précédent, qui lit déjà `$_POST['product_id']` et `$_POST['quantity']`.

#### Avantage de cette solution

- L'utilisateur peut modifier la quantité directement depuis le panier.
- Chaque ligne du panier possède un comportement JavaScript autonome.
- Le widget respecte les limites de stock côté interface.
- Le widget de quantité envoie une demande de modification du panier au serveur.


### Mise à jour dynamique de l'affichage du panier

Cette solution permet de répercuter immédiatement dans la page les nouvelles valeurs renvoyées par l'API après modification d'une quantité.
Le panier ne se contente donc plus d'envoyer une requête AJAX : il met aussi à jour ses sous-totaux et ses totaux à l'écran.


#### 1. Retour d'un panier enrichi dans la réponse JSON

Code dans `src/controller.php` :

```php
echo json_encode([
    'basket' => formatDisplayableFullBasket(
        extendBasket($pdo, retrieveBasketFromSession())
    ),
]);
```

##### Objectif

L'API ne renvoie plus simplement le panier brut stocké en session.
Elle renvoie maintenant un panier enrichi puis formaté, avec :
- les produits complets ;
- les sous-totaux formatés ;
- les totaux formatés.

Cela rend la réponse JSON directement exploitable côté JavaScript pour l'affichage.
Le front-end peut ainsi réutiliser les valeurs renvoyées par le serveur pour mettre à jour l'affichage sans recalculer lui-même les montants.

#### 2. Ajout d'identifiants et de classes pour cibler les éléments HTML

Code dans `public/basket.php` :

```php
<tr id="item-<?php echo $product['id']; ?>">
```

```php
<td class="item-product-price-htva"><?php echo $product['price_htva']; ?></td>
<td class="item-quantity">
    ...
</td>
<td class="item-total-htva"><?php echo $item['total_htva']; ?></td>
```

```php
<td id="basket-total-count"><?php echo $basket['total']['count']; ?></td>
<td id="basket-total-htva"><?php echo $basket['total']['htva']; ?></td>
<td id="basket-total-tvac"><?php echo $basket['total']['tvac']; ?></td>
```

##### Objectif

Le HTML ajoute maintenant des identifiants et des classes sur les zones qui devront être mises à jour en JavaScript.

Cela permet de cibler précisément :
- la ligne d'un produit donné ;
- le sous-total HTVA de cette ligne ;
- les trois totaux globaux du panier.

Sans ces repères dans le DOM, le script ne pourrait pas modifier facilement les bonnes zones après la réponse de l'API.

#### 3. Mise à jour de la ligne modifiée après la réponse AJAX

Code dans `public/resources/js/basket.js` :

```js
.then(response => {
    console.log('panier mis à jour', response.data)
    updateBasketItemDisplay(
        findBasketItemByProductId(response.data.basket.items, productId)
    )
    updateBasketTotalDisplay(response.data.basket.total)
})
```

Code dans `public/resources/js/basket.js` :

```js
function findBasketItemByProductId(items, productId) {
    for (let i = 0; i < items.length; i++) {
        if (items[i].product.id == productId) {
            return items[i]
        }
    }
    return {
        product: {
            id: productId
        },
        total_htva: '0 €'
    }
}
```

##### Objectif

Après la réponse de l'API, le script recherche d'abord l'item correspondant au produit modifié.

La réponse JSON contient la liste complète des items du panier.
Le script doit donc retrouver l'item correspondant au produit qui vient d'être modifié.

La fonction `findBasketItemByProductId(...)` parcourt cette liste et renvoie l'item trouvé.
Si aucun item ne correspond, elle renvoie un objet minimal avec un total à `0 €`.

Ce cas de secours permet de continuer à mettre à jour l'affichage même si le produit a disparu du panier, par exemple si sa quantité a été ramenée à `0`.

Le script met ensuite à jour le sous-total HTVA de la ligne concernée ainsi que le total du panier.

#### 4. Mise à jour du total de la ligne

```js
function updateBasketItemDisplay(item) {
    console.log('item update', item)
    let rowElement = document.getElementById('item-' + item.product.id)
    rowElement.querySelector('.item-total-htva').innerText = item.total_htva
}
```

##### Objectif

Le code s'appuie sur l'identifiant de la ligne HTML, construit sous la forme `item-<id>`.
Le DOM est ainsi modifié localement, sans rechargement complet de la page.

#### 5. Mise à jour des totaux globaux du panier

Code dans `public/resources/js/basket.js` :

```js
function updateBasketTotalDisplay(total) {
    console.log('total update', total)
    document.getElementById('basket-total-count').innerText = total.count
    document.getElementById('basket-total-htva').innerText = total.htva
    document.getElementById('basket-total-tvac').innerText = total.tvac
}
```

##### Objectif

Cette fonction met à jour les trois totaux affichés dans le pied du tableau :
- le nombre total d'articles ;
- le total HTVA ;
- le total TVAC.

Le panier reste ainsi cohérent après chaque modification de quantité.
L'utilisateur voit immédiatement l'impact de son action sur l'ensemble de la commande.


#### Avantage de cette solution

- Les montants affichés sont mis à jour sans rechargement de page.
- L'interface reste synchronisée avec les valeurs calculées côté serveur.
- Le JavaScript réutilise directement la réponse JSON de l'API.
- Le panier devient plus interactif et plus proche d'un comportement applicatif complet.
