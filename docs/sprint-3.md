# Sprint 3

## Objectif du sprint

Affichage dynamique des catégories dans le catalogue produits.

## Fonctionnalités

### Dynamisation de l'affichage des catégories

Cette solution permet de ne plus écrire les catégories en dur dans le HTML du catalogue.

#### 1. Récupération des catégories utiles depuis la base de données

Code dans `src/database.php` :

```php
function retrieveEffectiveCategories(PDO $pdo): array
{
    $stmt = $pdo->prepare(
        'SELECT * 
            FROM categories 
            WHERE id IN (
                SELECT 
                    DISTINCT category_id 
                    FROM product_category
                    JOIN products ON product_category.product_id = products.id
                    WHERE products.is_available = 1
            )
            ORDER BY categories.name'
    );
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

##### Objectif

Cette requête récupère uniquement les catégories réellement utilisées par des produits disponibles à la vente.
Le but n'est donc pas de charger toutes les catégories de la base, mais seulement celles qui ont du sens dans le catalogue visible par l'utilisateur.

La sous-requête :
- lit la table de liaison `product_category`: Le fait de lire la table de liaison plutôt que la table `categories` permet de ne récupérer que les catégories effectivement liées à des produits. ;
- conserve uniquement les produits pour lesquels `is_available = 1` (liaison à `products)`;

La requête principale récupère ensuite les informations complètes de ces catégories dans la table `categories` afin de connaître l'ensemble des données propres aux catégories (en particulier leurs noms).
Le tri `ORDER BY categories.name` permet d'afficher les catégories dans l'ordre alphabétique.

#### 2. Passage des catégories au template

Code dans `src/controller.php` :

```php
function retrieveBuyableDisplayableProducts(): array
{
  
    $pdo = getDatabaseConnection();
    $categories = retrieveEffectiveCategories($pdo);
    
    ...
    
    return [
        'products' => $products,
        'categories' => $categories,
    ];
}
```

Code dans `public/products.php` :

```php
require_once __DIR__ . '/../src/controller.php';

$data = retrieveBuyableDisplayableProducts();
$products = $data['products'];
$categories = $data['categories'];
unset($data);
```

##### Objectif

La page `products.php` ne récupère plus seulement la liste des produits.
Elle appelle maintenant `retrieveBuyableDisplayableProducts()` pour recevoir un tableau contenant :
- les produits à afficher (comme avant);
- les catégories à afficher dans le formulaire (nouveau).


#### 3. Génération dynamique des cases à cocher dans le HTML

Code dans `public/products.php` :

```php
<div class="dropdown-menu">

    <?php foreach ($categories as $category) { ?>

        <label class="dropdown-item">
            <input
                type="checkbox"
                name="categories[]"
                value="<?php echo $category['id']; ?>"
                >
            <?php echo $category['name']; ?>
        </label>

    <?php } ?>

</div>
```

##### Objectif

Le HTML n'écrit plus manuellement des catégories fixes comme "Guitares", "Amplis" ou "Accessoires".
Il parcourt maintenant le tableau `$categories` avec une boucle `foreach`.

Pour chaque catégorie :
- la valeur de la case à cocher correspond à son identifiant en base de données ;
- le libellé affiché correspond à son nom ;
- l'attribut `checked` est ajouté automatiquement si `is_checked` vaut `true`.

Ainsi, le formulaire reflète directement le contenu réel de la base de données.
Si une catégorie est ajoutée, supprimée ou renommée en base, l'affichage s'adapte sans devoir modifier le HTML.

#### 4. Récupération des catégories demandées dans l'URL

Code dans `public/products.php` : 

```php
<form action="#" method="get" class="toolbar-form">
    <input type="checkbox" name="categories[]" ... >
    ...
</form>
```

Code dans `src/controller.php` :

```php
$checkedCategories = $_GET['categories'] ?? [];
```

##### Objectif

Le contrôleur lit ici le paramètre `categories` envoyé par le formulaire selon la méthode HTTP GET.

Le choix de `GET` est adapté ici, car le formulaire sert à consulter et filtrer des données, 
pas à modifier l'état de l'application ou de la base de données.
Avec `GET`, les filtres restent visibles dans l'URL, ce qui permet :
- de comprendre immédiatement quels critères sont appliqués ;
- de recharger la page en conservant les filtres ;
- de copier ou partager facilement le lien exact vers le catalogue filtré.

Avec ce formulaire, l'URL peut par exemple ressembler à :

```txt
products.php?categories[]=1&categories[]=3
```

(Attention, les URL sont normalement encodées selon un format particulier ("URL encoding" ou "Percent-encoding"), 
et l'exemple suivant peut apparaître sous la forme `products.php?categories%5B%5D=1&categories%5B%5D=3`.)

Comme les cases à cocher utilisent le nom `categories[]`, 
PHP regroupe automatiquement les valeurs sélectionnées sous forme d'un tableau indexé dans `$_GET['categories']`.
Il s'agit ici d'une particularité de PHP : 
le fait de rajouter `[]` à la fin du "name" des champs force PHP à créer un tableau indexé listant toutes les valeurs transmises.

L'expression `$_GET['categories'] ?? []` signifie :
- si le paramètre existe, on utilise sa valeur (un tableau indexé) ;
- sinon, on utilise un tableau vide.

Cela permet d'avoir un comportement prévisible, même lorsque aucune catégorie n'a encore été cochée.

#### 5. Présélection des catégories demandées à l'affichage

Code dans `src/controller.php` :

```php
foreach ($categories as $index => $category) {
    $categories[$index]['is_checked'] = in_array(
        $category['id'],
        $checkedCategories
    );
}
```

Code dans `public/products.php` :

```php
<input
        ...
        <?php if ($category['is_checked']) { ?>checked<?php } ?>
>
```

##### Objectif

Le contrôleur enrichit ici chaque catégorie avec une information supplémentaire : `is_checked`.
Cette clé n'existe pas dans la base de données.
Elle est ajoutée uniquement pour faciliter l'affichage dans la vue.

La fonction `in_array(...)` vérifie si l'identifiant de la catégorie courante se trouve dans le tableau des catégories cochées reçu via l'URL.
Si c'est le cas, `is_checked` vaut `true`.
Sinon, cette valeur vaut `false`.

Cette étape illustre bien le rôle du contrôleur :
il récupère des données brutes depuis la base, puis il les prépare pour qu'elles soient directement exploitables par le HTML.

#### Avantage de cette solution

- Les catégories ne sont plus dupliquées en dur dans la vue.
- Le formulaire s'adapte automatiquement aux catégories réellement utilisées.
- L'état des cases cochées est conservé après l'envoi du formulaire.
- Le contrôleur prépare des données prêtes à être affichées, ce qui garde la vue plus simple.


### Filtrage des produits par catégorie

Cette solution permet d'utiliser réellement les catégories sélectionnées dans le formulaire pour limiter les produits affichés dans le catalogue.

#### 1. Validation des identifiants reçus dans le contrôleur

Code dans `src/controller.php` :

```php
$checkedCategories = $_GET['categories'] ?? [];
foreach ($checkedCategories as $index => $id) {
    $checkedCategories[$index] = filter_var($id, FILTER_VALIDATE_INT);
}
```

##### Objectif

Le contrôleur parcourt d'abord les identifiants de catégories reçus via l'URL avant de les transmettre à la couche d'accès aux données.
La fonction `filter_var(..., FILTER_VALIDATE_INT)` permet de vérifier que chaque valeur correspond bien à un entier valide.

Cette étape permet d'éviter de manipuler des valeurs incohérentes ou mal formées.
Le contrôleur prépare ainsi des données plus propres avant la construction de la requête SQL.

#### 2. Transmission des catégories sélectionnées à la fonction d'accès aux produits

Code dans `src/controller.php` :

```php
$products = retrieveBuyableProducts($pdo, $checkedCategories);
```

##### Objectif

La fonction `retrieveBuyableProducts(...)` reçoit maintenant un deuxième argument contenant la liste des catégories sélectionnées.
Le contrôleur ne se contente donc plus de demander tous les produits vendables :
il transmet aussi le filtre choisi par l'utilisateur.

Cela permet à la logique de filtrage d'être gérée au bon endroit, c'est-à-dire dans la requête SQL qui récupère les produits.

#### 3. Ajout d'un paramètre facultatif dans `retrieveBuyableProducts(...)`

Code dans `src/database.php` :

```php
function retrieveBuyableProducts(PDO $pdo, array $categoryIds = []): array
{
    ...
}
```

##### Objectif

La fonction accepte maintenant un tableau de catégories, tout en gardant un comportement par défaut si aucun filtre n'est fourni.
Le paramètre `array $categoryIds = []` signifie que :
- la fonction peut être appelée sans filtre ;
- elle continue alors à retourner tous les produits vendables ;
- elle peut aussi recevoir une liste de catégories à utiliser pour limiter les résultats.

Cette signature rend la fonction plus flexible sans casser son usage précédent.

#### 4. Construction dynamique de la clause SQL de filtre

Code dans `src/database.php` :

```php
$categoryClause = '';
$categoryParams = [];

if ($categoryIds) {

    foreach ($categoryIds as $index => $categoryId) {
        $categoryParams[':cat' . $index] = $categoryId;
    }

    $categoryClause = 'AND id IN (
        SELECT DISTINCT product_id
        FROM product_category
        WHERE category_id IN (' . implode(',', array_keys($categoryParams)) . ')
    )';

}
```

##### Objectif

Cette partie prépare la portion de requête SQL nécessaire uniquement si des catégories ont été cochées.
Si le tableau `$categoryIds` est vide, la variable `$categoryClause` reste vide et aucun filtre supplémentaire n'est appliqué.

Si des catégories sont présentes, la fonction :
- crée un paramètre nommé pour chaque identifiant, comme `:cat0`, `:cat1`, etc. ;
- stocke les valeurs correspondantes dans le tableau `$categoryParams` ;
- construit une clause SQL `IN (...)` qui réutilise ces paramètres.

Le but est d'adapter la requête au nombre de catégories sélectionnées, sans écrire une requête différente à la main pour chaque cas.

#### 5. Filtrage des produits via la table de liaison

Code dans `src/database.php` :

```php
$query = "SELECT *
            FROM products
            WHERE is_available = 1
            $categoryClause
            ORDER BY display_priority";

$stmt = $pdo->prepare($query);
$stmt->execute($categoryParams);
return $stmt->fetchAll(PDO::FETCH_ASSOC);
```

##### Objectif

La requête SQL conserve toujours la condition `is_available = 1` pour n'afficher que les produits vendables.
Lorsque des catégories sont sélectionnées, elle ajoute en plus la clause `AND id IN (...)`.

Cette clause s'appuie sur la table `product_category`, qui fait le lien entre les produits et les catégories.
La sous-requête retourne les `product_id` associés à au moins une des catégories demandées.

En pratique, cela signifie que le catalogue n'affiche plus tous les produits :
il n'affiche que ceux qui appartiennent à l'une des catégories cochées par l'utilisateur.

Par exemple, si aucune catégorie n'est sélectionnée, la variable `$query` contiendra une requête de ce type :

```sql
SELECT *
FROM products
WHERE is_available = 1
ORDER BY display_priority
```

Si l'URL vaut par exemple :

```txt
products.php?categories[]=1&categories[]=3
```

alors la variable `$query` contiendra une requête de ce type :

```sql
SELECT *
FROM products
WHERE is_available = 1
AND id IN (
    SELECT DISTINCT product_id
    FROM product_category
    WHERE category_id IN (:cat0,:cat1)
)
ORDER BY display_priority
```

et la variable `$categoryParams` contiendra la valeur `['cat0' => 1, 'cat1' => 3]`.

L'utilisation de `prepare(...)` puis de `execute($categoryParams)` permet enfin de transmettre les valeurs proprement à PDO.
On garde donc une requête préparée, même si le nombre de catégories varie selon le formulaire.

#### Avantage de cette solution

- Le filtre par catégorie est maintenant réellement appliqué aux produits affichés.
- La logique de filtrage est gérée dans la couche d'accès aux données.
- La requête reste adaptable, même avec un nombre variable de catégories sélectionnées.
- Les paramètres sont transmis proprement à PDO.


### Gestion de l'ordre d'affichage des produits

Cette solution permet de trier le catalogue selon l'ordre demandé dans le formulaire, tout en conservant l'option sélectionnée à l'affichage.

#### 1. Récupération du paramètre `order` dans le contrôleur

Code dans `src/controller.php` :

```php
$order = $_GET['order'] ?? '';
```

##### Objectif

Le contrôleur lit ici le paramètre `order` transmis par le formulaire.
Comme pour les catégories, la méthode `GET` permet de retrouver facilement la valeur choisie dans l'URL.

L'expression `$_GET['order'] ?? ''` signifie :
- si le paramètre existe, on utilise sa valeur ;
- sinon, on utilise une chaîne vide.

Cela permet de définir un comportement par défaut lorsque l'utilisateur n'a pas encore demandé de tri particulier.

#### 2. Transmission de l'ordre demandé aux différentes couches

Code dans `src/controller.php` :

```php
$products = retrieveBuyableProducts($pdo, $checkedCategories, $order);

return [
    'products' => $products,
    'categories' => $categories,
    'order' => $order,
];
```

Code dans `public/products.php` :

```php
$data = retrieveBuyableDisplayableProducts();
$products = $data['products'];
$categories = $data['categories'];
$order = $data['order'];
unset($data);
```

##### Objectif

Le contrôleur transmet maintenant la valeur du tri à la fonction `retrieveBuyableProducts(...)`.
Il renvoie aussi cette même valeur à la vue dans la clé `order`.

La page `products.php` peut ainsi utiliser cette information à deux endroits :
- pour demander les produits dans le bon ordre ;
- pour réafficher l'option choisie dans le champ `<select>`.

#### 3. Conservation de l'option sélectionnée dans le formulaire

Code dans `public/products.php` :

```php
<select name="order" id="order">
    <option value="default">Popularité</option>
    <option 
        value="price_asc" 
        <?php if ($order === 'price_asc') { ?>selected<?php } ?>
        >Prix croissant</option>
    <option 
        value="price_desc"
        <?php if ($order === 'price_desc') { ?>selected<?php } ?>
        >Prix décroissant</option>
</select>
```

##### Objectif

La vue compare la valeur de `$order` avec chaque option du champ `<select>`.
Si la valeur correspond à `price_asc` ou `price_desc`, l'attribut `selected` est ajouté sur l'option concernée.

Cela permet de conserver visuellement le tri choisi après l'envoi du formulaire.
L'utilisateur voit donc immédiatement quel ordre est actuellement appliqué au catalogue.

#### 4. Ajout d'un paramètre facultatif dans `retrieveBuyableProducts(...)`

Code dans `src/database.php` :

```php
function retrieveBuyableProducts(PDO $pdo, array $categoryIds = [], string $order = ''): array
{
    ...
}
```

##### Objectif

La fonction d'accès aux produits accepte maintenant un troisième paramètre facultatif : l'ordre demandé.
Ce paramètre reste optionnel pour conserver un comportement par défaut si aucun tri particulier n'est demandé.

La signature devient donc plus complète :
- les catégories filtrent les produits à récupérer ;
- la valeur `order` détermine dans quel ordre ces produits doivent être triés.

#### 5. Construction dynamique de la clause `ORDER BY`

Code dans `src/database.php` :

```php
$orderClause = 'display_priority ASC';
if ($order === 'price_asc') {
    $orderClause = 'price_htva ASC, ' . $orderClause;
} elseif ($order === 'price_desc') {
    $orderClause = 'price_htva DESC, ' . $orderClause;
}
```

##### Objectif

La variable `$orderClause` contient la partie de la requête SQL qui définit le tri.
Par défaut, les produits restent triés par `display_priority ASC`.

Si l'utilisateur demande `price_asc`, la requête trie d'abord par prix croissant.
Si l'utilisateur demande `price_desc`, la requête trie d'abord par prix décroissant.

Dans les deux cas, `display_priority ASC` est conservé à la fin comme critère secondaire.
Cela permet de garder un ordre stable entre les produits qui ont le même prix.

#### 6. Intégration du tri dans la requête SQL

Code dans `src/database.php` :

```php
$query = "SELECT *
            FROM products
            WHERE is_available = 1
            $categoryClause
            ORDER BY $orderClause";
```

##### Objectif

La requête SQL utilise maintenant la variable `$orderClause` dans le `ORDER BY`.
Le tri dépend donc directement de la valeur choisie dans le formulaire.

Par exemple, si aucun ordre particulier n'est demandé, la requête ressemble à :

```sql
SELECT *
FROM products
WHERE is_available = 1
ORDER BY display_priority ASC
```

Si l'URL vaut par exemple :

```txt
products.php?order=price_asc
```

alors la requête ressemble à :

```sql
SELECT *
FROM products
WHERE is_available = 1
ORDER BY price_htva ASC, display_priority ASC
```

Si l'URL vaut par exemple :

```txt
products.php?categories[]=1&categories[]=3&order=price_desc
```

alors la requête ressemble à :

```sql
SELECT *
FROM products
WHERE is_available = 1
AND id IN (
    SELECT DISTINCT product_id
    FROM product_category
    WHERE category_id IN (:cat0,:cat1)
)
ORDER BY price_htva DESC, display_priority ASC
```

Le tri peut donc se combiner avec le filtre par catégorie déjà mis en place.

#### Avantage de cette solution

- L'utilisateur peut choisir l'ordre d'affichage du catalogue.
- Le choix du tri est conservé dans le formulaire après rechargement.
- Le tri est géré directement dans la requête SQL.
- Le tri par prix reste compatible avec le filtre par catégorie.
