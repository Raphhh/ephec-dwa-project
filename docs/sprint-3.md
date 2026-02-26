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
