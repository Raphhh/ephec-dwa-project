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
        'resources/js/basket.js'
];
```

##### Objectif

La page `basket.php` déclare ici un fichier JavaScript spécifique à son comportement.
Comme pour les autres pages du projet, cette variable est ensuite exploitée par le template commun pour inclure automatiquement le script dans le HTML.

Cela permet de ne charger le JavaScript du panier que sur la page qui en a réellement besoin.

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

#### 3. Utilisation des attributs `data-*` pour stocker des informations utiles

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

La fonction `parseInt(...)` permet de convertir ces valeurs en entiers utilisables dans les calculs JavaScript.

#### 4. Initialisation du comportement JavaScript sur chaque widget

Code dans `public/resources/js/basket.js` :

```js
let elements = document.getElementsByClassName('basket-quantity-widget');
for (let i = 0; i < elements.length; i++) {
    manageWidget(elements[i])
}
```

##### Objectif

Le script commence par récupérer tous les éléments HTML ayant la classe `basket-quantity-widget`.
Il parcourt ensuite cette collection pour appeler `manageWidget(...)` sur chacun d'eux.

Le but est d'appliquer le même comportement interactif à toutes les lignes du panier.
Chaque widget devient ainsi autonome et géré individuellement.

#### 5. Récupération des sous-éléments et des valeurs utiles

Code dans `public/resources/js/basket.js` :

```js
function manageWidget(widget) {

    let quantityElement = widget.querySelector('.basket-quantity-widget-quantity')
    let addButton = widget.querySelector('.basket-quantity-widget-add-button')
    let removeButton = widget.querySelector('.basket-quantity-widget-remove-button')

    let quantity = parseInt(quantityElement.innerText)
    let stock = parseInt(widget.dataset.productStock)

    ...
}
```

##### Objectif

La fonction `manageWidget(...)` récupère d'abord les trois éléments utiles à l'intérieur du widget :
- la zone qui affiche la quantité ;
- le bouton `+` ;
- le bouton `-`.

Elle lit ensuite :
- la quantité actuelle à partir du texte affiché ;
- le stock maximum à partir de `data-product-stock`.

La fonction `parseInt(...)` permet de convertir ces valeurs en entiers utilisables dans les calculs JavaScript.

#### 6. Gestion du clic sur les boutons `+` et `-`

Code dans `public/resources/js/basket.js` :

```js
addButton.onclick = function () {
    quantity++
    if (quantity > stock) {
        quantity = stock
    }
    quantityElement.innerText = quantity
}

removeButton.onclick = function () {
    quantity--
    if (quantity < 0) {
        quantity = 0
    }
    quantityElement.innerText = quantity
}
```

##### Objectif

Quand l'utilisateur clique sur `+`, la quantité augmente.
Quand il clique sur `-`, la quantité diminue.

Le code encadre toutefois cette modification avec deux limites :
- la quantité ne peut pas dépasser le stock disponible ;
- la quantité ne peut pas descendre sous `0`.

Après chaque modification, le texte affiché dans le widget est mis à jour via `quantityElement.innerText`.

#### 7. Désactivation automatique des boutons aux limites autorisées

Code dans `public/resources/js/basket.js` :

```js
function checkButtons(addButton, removeButton, quantity, max) {
    addButton.disabled = quantity >= max
    removeButton.disabled = quantity <= 0
}
```

##### Objectif

Cette fonction évite à l'utilisateur de continuer à cliquer sur un bouton alors qu'il a déjà atteint une limite logique.

Si la quantité est égale au stock maximum, le bouton `+` est désactivé.
Si la quantité est égale à `0`, le bouton `-` est désactivé.

L'interface devient ainsi plus cohérente :
elle ne se contente pas de corriger la valeur après coup, elle bloque aussi les actions inutiles.

#### Avantage de cette solution

- L'utilisateur peut modifier la quantité directement depuis le panier.
- Chaque ligne du panier possède un comportement JavaScript autonome.
- Le widget respecte les limites de stock côté interface.
