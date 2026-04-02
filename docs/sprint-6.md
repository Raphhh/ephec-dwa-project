# Sprint 6

## Objectif du sprint

Dynamisation du panier.

## Fonctionnalités

### Affichage dynamique du panier

Cette solution permet d'afficher un panier dynamique en HTML.
La page récupère le contenu réel du panier stocké en session,
enrichi avec les données des produits et les totaux calculés.

#### 1. Appel du contrôleur dans `basket.php`

Code dans `public/basket.php` :

```php
require_once __DIR__ . '/../src/controller.php';
$basket = retrieveCurrentBasket();
```

##### Objectif

La page `basket.php` charge d'abord le contrôleur, puis appelle `retrieveCurrentBasket()`
pour récupérer toutes les informations nécessaires à l'affichage.

La variable `$basket` contient ensuite :
- la liste des produits présents dans le panier ;
- les quantités choisies ;
- les sous-totaux par ligne ;
- les totaux globaux du panier.

#### 2. Construction d'un panier enrichi côté métier

Code dans `src/controller.php` :

```php
function retrieveCurrentBasket(): array
{
    $pdo = getDatabaseConnection();
    $basket = extendBasket($pdo, retrieveBasketFromSession());
    
    ...
}
```

Code dans `src/basket.php` :

```php
function extendBasket(PDO $pdo, array $basket): array
{
    $order = [
        'items' => [],
        'total' => [
            'count' => 0,
            'htva' => 0,
            'tvac' => 0,
        ]
    ];

    foreach ($basket as $productId => $quantity) {
        $product = retrieveProductById($pdo, $productId);
        if (!$product) {
            continue;
        }

        $htva = $product['price_htva'] * $quantity;

        $order['items'][] = [
            'product' => $product,
            'quantity' => $quantity,
            'total_htva' => $htva,
        ];
        $order['total']['count']++;
        $order['total']['htva'] += $htva;
        $order['total']['tvac'] += addTva($htva);
    }

    return $order;
}
```

##### Objectif

Le panier stocké en session ne contient au départ qu'une structure simple du type :
- identifiant produit ;
- quantité.

La fonction `extendBasket(...)` transforme cette structure minimale en un panier plus complet, directement exploitable par la vue.
Pour chaque produit présent en session, elle :
- récupère la fiche produit depuis la base de données ;
- calcule le total HTVA de la ligne ;
- ajoute une entrée dans `items` ;
- met à jour les totaux globaux.

Le tableau retourné contient donc deux parties :
- `items`, pour les lignes du panier ;
- `total`, pour les totaux généraux.

##### 2.1. Définition de la structure du tableau (extendBasket)

Code dans `src/basket.php` :

```php
$order = [
    'items' => [],
    'total' => [
        'count' => 0,
        'htva' => 0,
        'tvac' => 0,
    ]
];
```

###### Objectif

La fonction `extendBasket(...)` commence par initialiser la structure du tableau qu'elle va retourner.
Ce tableau est préparé à l'avance pour que la boucle puisse ensuite y ajouter progressivement les données du panier.

La clé `items` est un tableau vide qui servira à stocker chaque ligne du panier.
La clé `total` contient déjà les totaux généraux, initialisés à `0` avant les calculs.

Cette structure garantit que la fonction renvoie toujours un tableau cohérent, même si le panier est vide.

##### 2.2. Gestion des produits inexistants dans le panier (extendBasket)

Code dans `src/basket.php` :

```php
$product = retrieveProductById($pdo, $productId);
if (!$product) {
    continue;
}
```

###### Objectif

Cette vérification protège le panier contre un cas incohérent :
un produit pourrait encore être présent en session alors qu'il n'existe plus en base de données.

Dans ce cas, la boucle passe simplement à l'élément suivant grâce à `continue`.
La page évite ainsi d'essayer d'afficher ou de calculer un produit introuvable.

##### 2.3. Définition des valeurs d'un item (extendBasket)

Code dans `src/basket.php` :

```php
$htva = $product['price_htva'] * $quantity;

$order['items'][] = [
    'product' => $product,
    'quantity' => $quantity,
    'total_htva' => $htva,
];
```

###### Objectif

Pour chaque produit trouvé en base, la fonction calcule d'abord le total HTVA de la ligne en multipliant le prix unitaire par la quantité.

Elle construit ensuite un nouvel item dans le tableau `items`.
Chaque item contient :
- le produit complet dans `product` ;
- la quantité commandée dans `quantity` ;
- le sous-total HTVA de la ligne dans `total_htva`.

Cette structure permet à la vue d'afficher directement chaque ligne du panier sans devoir refaire ces calculs dans le HTML.

##### 2.4. Définition des totaux (extendBasket)

Code dans `src/basket.php` :

```php
$order['total']['count']++;
$order['total']['htva'] += $htva;
$order['total']['tvac'] += addTva($htva);
```

Code dans `src/utils.php` :

```php
function addTva($price): float
{
    return $price * (1 + TVA);
}
```

###### Objectif

Après avoir ajouté un item, la fonction met à jour les totaux généraux du panier.

La valeur `count` est incrémentée à chaque produit ajouté dans `items`.
Dans le code actuel, cela correspond donc au nombre de lignes du panier, et non à la somme de toutes les quantités.

La valeur `htva` additionne les sous-totaux HTVA de chaque ligne.
La valeur `tvac` additionne ces mêmes montants après application de la TVA via `addTva(...)`.

On obtient ainsi des totaux globaux prêts à être formatés puis affichés dans le récapitulatif du panier.

#### 3. Formatage du panier pour l'affichage

Code dans `src/controller.php` :

```php
return formatDisplayableFullBasket($basket);
```

Code dans `src/utils.php` :

```php
function formatDisplayableFullBasket(array $fullBasket): array
{
    foreach ($fullBasket['items'] as $index => $item) {
        $fullBasket['items'][$index]['product'] = formatDisplayableProduct($item['product']);
        $fullBasket['items'][$index]['total_htva'] = formatPrice($item['total_htva']);
    }

    $fullBasket['total']['htva'] = formatPrice($fullBasket['total']['htva']);
    $fullBasket['total']['tvac'] = formatPrice($fullBasket['total']['tvac']);

    return $fullBasket;
}
```

##### Objectif

Les données calculées dans `extendBasket(...)` sont encore brutes.
La fonction `formatDisplayableFullBasket(...)` applique donc les transformations nécessaires avant l'affichage.

Elle :
- formate chaque produit avec `formatDisplayableProduct(...)` ;
- formate le total HTVA de chaque ligne ;
- formate les totaux HTVA et TVAC du panier.

Cela permet à la vue de recevoir des données directement prêtes à être affichées, sans logique de formatage supplémentaire dans le HTML.

#### 4. Boucle d'affichage des lignes du panier dans le HTML

Code dans `public/basket.php` :

```php
<?php foreach ($basket['items'] as $item) { ?>
    <?php $product = $item['product']; ?>
    <tr>
        <!-- Produit <?php echo $product['id']; ?> -->
        <td>
            <a href="<?php echo $product['url']; ?>">
                <img src="<?php echo $product['image_url']; ?>"
                     alt="<?php echo $product['short_description']; ?>"
                     title="<?php echo $product['name']; ?>"
                     class="basket-img">
                <?php echo $product['name']; ?>
            </a>
        </td>
        <td><?php echo $product['price_htva']; ?></td>
        <td><?php echo $item['quantity'] ?></td>
        <td><?php echo $item['total_htva']; ?></td>
    </tr>
<?php } ?>
```

##### Objectif

La page utilise maintenant une boucle `foreach` pour afficher toutes les lignes du panier.
Le HTML n'écrit donc plus plusieurs `<tr>` fixes à la main.

Pour chaque ligne :
- le nom du produit est affiché ;
- l'image et le lien vers la fiche produit sont réutilisés ;
- le prix unitaire HTVA est affiché ;
- la quantité choisie est affichée ;
- le total HTVA de la ligne est affiché.

La même structure HTML peut ainsi afficher n'importe quel panier, quel que soit son contenu réel.

#### 5. Affichage dynamique des totaux dans le tableau

Code dans `public/basket.php` :

```php
<tr>
    <th colspan="3">Nombre total d’articles</th>
    <td><?php echo $basket['total']['count']; ?></td>
</tr>

<tr>
    <th colspan="3">Total HTVA</th>
    <td><?php echo $basket['total']['htva']; ?></td>
</tr>

<tr>
    <th colspan="3">Total TVAC (<?php echo fomatTva();?>)</th>
    <td><?php echo $basket['total']['tvac']; ?></td>
</tr>
```

##### Objectif

Le pied du tableau utilise maintenant les valeurs calculées dans `$basket['total']`.
Le nombre d'articles, le total HTVA et le total TVAC ne sont plus écrits en dur.

La fonction `fomatTva()` est aussi réutilisée pour afficher le taux de TVA.
Cela garde le tableau cohérent avec la configuration du projet.

#### Avantage de cette solution

- Le panier affiché correspond maintenant au contenu réel de la session.
- Les lignes du panier sont générées automatiquement à partir des produits présents.
- Les totaux sont calculés dynamiquement.
- La logique est répartie entre contrôleur, fonctions métier, utilitaires et vue.
- Le code HTML du panier devient réutilisable et beaucoup plus souple.
