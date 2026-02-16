# Sprint 2

## Objectif du sprint

Pages produits dynamiques.

## Fonctionnalités

## Ajout de `config.php`

Cette solution permet de centraliser le chargement de la configuration du projet.

### 1. Création d'un fichier `config.php`

Code dans `config.php` :

```php
<?php

require_once __DIR__ . '/env.php';
```

#### Objectif

Le fichier `config.php` sert de point d'entrée unique pour la configuration de l'application.
Ce fichier a pour objectif de regrouper en un même endroit la définition de constantes qui seront nécessaires partout dans le projet.
Il devra être inclus par les autres fichiers du projet.

Le fichier `config.php` inclut `env.php` qui n'est pas versionné.
Le fichier `env.php` contient les paramètres d'environnement locaux, qui diffèrent selon l'environnement.
Par exemple :
- le nom de la base de données ;
- l'hôte MySQL ;
- l'utilisateur ;
- le mot de passe ;
- l'encodage utilisé.

Le fichier `env.php` n'est pas versionné car il contient des données propres à chaque machine de travail.
Par exemple, un environnement local de dev peut utiliser `root` sans mot de passe,
alors que ce ne sera jamais le cas en environnement de prod.

Il est préférable de ne pas mettre ce type d'information dans le dépôt Git :
- pour éviter d'imposer une configuration unique à tout le monde ;
- pour éviter de publier des informations sensibles ;
- pour permettre à chaque développeur d'adapter facilement son environnement local.

Le dépôt versionne donc `config.php`, qui est commun au projet, mais pas `env.php`, qui dépend de l'environnement local.

### Avantage de cette solution

- La configuration commune est centralisée dans `config.php`.
- Les paramètres locaux restent séparés dans `env.php`.
- Le projet est plus portable d'une machine à l'autre.
- Les informations sensibles ou spécifiques à une machine ne sont pas envoyées dans Git.


## Dynamisation de `product.php`

Cette solution permet de transformer une page produit statique en page dynamique alimentée par la base de données.

### 1. Utilisation d'un contrôleur dans `product.php`

Code dans `public/product.php` :

```php
require_once __DIR__ . '/../src/controller.php';

$product = retrieveDisplayableProduct();

...
```

#### Objectif

La page `product.php` ne contient plus directement les données du produit en dur.
Elle charge d'abord le contrôleur, puis appelle `retrieveDisplayableProduct()` pour récupérer les informations utiles.

Le controlleur permet de séparer la logique de récupération des données du code HTML d'affichage.

### 2. Lecture de l'identifiant du produit dans l'URL

Code dans `src/controller.php` :

```php
function retrieveDisplayableProduct(): array
{
    if (empty($_GET['id'])) {
        return [];
    }
    
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    ...
}
```

#### Objectif

Cette partie récupère l'identifiant du produit dans l'URL, par exemple avec une adresse comme `product.php?id=5`.

La superglobale `$_GET` contient les paramètres passés dans l'URL.
La fonction `empty(...)` permet ici de vérifier rapidement si le paramètre `id` est absent ou considéré comme vide.
Dans ce cas, la fonction retourne immédiatement un tableau vide, ce qui évite d'aller plus loin avec une valeur inutilisable.

Il faut toutefois noter qu'en PHP, `empty(0)` et `empty('0')` renvoient aussi `true`.
Autrement dit, si l'URL contient `id=0`, la condition considérera cette valeur comme vide.
Ici, ce n'est pas un problème, car un identifiant de clé primaire auto-incrémentée en MySQL ne vaut jamais `0` pour un enregistrement valide.
Traiter `0` comme une valeur invalide est donc cohérent avec le fonctionnement attendu de la base de données. 
On évite une requête inutile potentielle.

La fonction `filter_var(..., FILTER_VALIDATE_INT)` permet ensuite de s'assurer que la valeur reçue est bien un entier.
Le but est d'éviter de travailler avec une valeur invalide avant d'interroger la base de données.

### 3. Récupération du produit depuis la base de données

Code dans `src/controller.php` :

```php
$pdo = getDatabaseConnection();
$product = retrieveProductById($pdo, $id);
```

Code dans `src/database.php` :

```php
function getDatabaseConnection(): PDO
{
	$dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset=' . DB_CHARSET;
    return new PDO($dsn, DB_USER, DB_PWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}

function retrieveProductById(PDO $pdo, $id): array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}
```

#### Objectif

Le contrôleur commence par ouvrir une connexion à la base de données avec `getDatabaseConnection()`. 
Les constantes nécessaires à la connexion doivent être définies dans `env.php`.

Le contrôleur appelle ensuite `retrieveProductById(...)`, qui exécute une requête SQL pour récupérer 
le produit correspondant à l'identifiant demandé.

L'utilisation d'une requête préparée avec `:id` permet de transmettre la valeur proprement à SQL.
Le fetch retourne un tableau associatif. Les clés du tableau sont les colonnes de la requête SQL.
Si le produit n'est pas trouvé en DB, la fonction retourne un tableau vide (plutôt que `false` comme `PDO::fetch()`).

A noter que la syntaxe `return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];` est un raccourci pour le code suivant :

```php
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if ($product) {
    return $product;
}
return [];
```

### 4. Mise en forme des données pour l'affichage

Code dans `src/controller.php` :

```php
if ($product) {
    return formatDisplayableProduct($product);
}
return $product;
```

Code dans `src/utils.php` :

```php
function addTva($price): float
{
    return $price * (1 + TVA);
}

function fomatTva(): string
{
    return (TVA * 100) . '%';
}

function formatPrice($price): string
{
    return number_format($price, 0, ',', ' ') . ' €';
}

function formatProductImageUrl($imagePath): string
{
    return 'resources/products/' . $imagePath;
}

function formatProductUrl($id): string
{
    return 'product.php?id=' . $id;
}

function formatDisplayableProduct(array $product): array
{
    $product['price_tvac'] = formatPrice(addTva($product['price_htva']));
    $product['price_htva'] = formatPrice($product['price_htva']);
    $product['image_url'] = formatProductImageUrl($product['img_file_path']);
    unset($product['img_file_path']);
    $product['url'] = formatProductUrl($product['id']);

    return $product;
}
```

Code dans `config.php` :

```php
define ('TVA', 0.21);
```

#### Objectif

Les données brutes venant de la base ne sont pas toujours prêtes à être affichées directement.
Le contrôleur applique donc quelques transformations, notamment :
- calcul du prix TVAC à partir du prix HTVA ;
- formatage des prix pour l'affichage ;
- construction du chemin de l'image produit.

A noter que le contrôleur vérifie que le produit existe bien avant d'essayer de le formater.
Si la requête SQL ne retourne rien, la fonction renvoie simplement un tableau vide.
Cela évite d'appeler `formatDisplayableProduct(...)` sur une valeur vide, ce qui provoquerait des erreurs.

La constante `TVA` a été ajoutée dans `config.php` pour centraliser cette valeur dans la configuration du projet.
Si le taux change un jour, il suffira de le modifier à un seul endroit.


### 5. Adaptation du titre et de la description de la page

Code dans `public/product.php` :

```php
if ($product) {
    $title = $product['name'] . ' - Rock Station';
    $description = $product['short_description'];
} else {
    $title = 'Produit introuvable  - Rock Station';
    $description = '';
}
```

```php
<?php if ($product) { ?>

    <article class="product">
        ...
    </article>

<?php } else { ?>
    <section class="toolbar">
        <h1>Produit introuvable</h1>
    </section>
<?php } ?>
```

#### Objectif

La page adapte maintenant ses métadonnées selon le résultat obtenu.
Si le produit existe, elle utilise son nom et sa description.
Sinon, elle définit un titre générique indiquant que le produit est introuvable.

De même, la page affiche soit la fiche normale, soit un message simple indiquant que le produit n'a pas été trouvé.

Cela permet d'avoir une page cohérente, même quand aucun produit valide n'a été trouvé.
Cette structure protège la page contre les accès avec un identifiant inexistant dans l'URL.

### Avantage de cette solution

- La page ne casse pas si l'identifiant demandé n'existe pas.
- L'utilisateur reçoit un message clair au lieu d'une erreur technique.

### 6. Affichage dynamique du produit

Code dans `public/product.php` :

```php
<img src="<?php echo $product['image_url']; ?>"
     alt="<?php echo $product['short_description']; ?>"
     title="<?php echo $product['name']; ?>"
     class="main-image">

<h1 class="product-title"><?php echo $product['name']; ?></h1>

<p class="short-description"><?php echo $product['short_description']; ?></p>

<p class="price-ht">Prix HTVA : <strong><?php echo $product['price_htva']; ?></strong></p>
<p class="price-tva">Prix TVAC (<?php echo fomatTva();?>) : <strong><?php echo $product['price_tvac']; ?></strong></p>
```

#### Objectif

Le HTML n'affiche plus des textes écrits en dur.
Il affiche maintenant les valeurs contenues dans le tableau `$product`, récupéré depuis la base de données.

La même page peut ainsi servir à afficher n'importe quel produit, selon l'identifiant reçu dans l'URL.

### 7. Affichage dynamique de l'état du stock

Code dans `public/product.php` :

```php
<?php if (!$product['is_available']) { ?>
    <p class="stock out-of-stock">
        ✘ Plus disponible
    </p>
<?php } elseif ($product['stock'] > 0) { ?>
    <p class="stock in-stock">
        ✔ En stock (<?php echo $product['stock']; ?> disponibles)
    </p>
<?php } else { ?>
    <p class="stock out-of-stock">
        ✘ Rupture de stock
    </p>
<?php } ?>
```

#### Objectif

Cette partie adapte l'affichage selon l'état réel du produit :
- s'il n'est plus vendu ;
- s'il est disponible en stock ;
- ou s'il est en rupture.

Cela permet à l'utilisateur de voir immédiatement la situation du produit affiché.

### Avantage de cette solution

- La page produit peut afficher n'importe quel produit de la base.
- Les données ne sont plus dupliquées dans le HTML.
- La logique est séparée entre vue, contrôleur, accès base de données et fonctions utilitaires.
- Le formatage des données est centralisé et réutilisable.


## Dynamisation de `products.php`

Cette solution permet de transformer le catalogue statique en liste dynamique de produits venant de la base de données.

### 1. Appel du contrôleur dans `products.php`

Code dans `public/products.php` :

```php
require_once __DIR__ . '/../src/controller.php';

$products = retrieveBuyableDisplayableProducts();
```

#### Objectif

La page du catalogue ne contient plus une série de produits écrits en dur dans le HTML.
Elle charge le contrôleur, puis récupère la liste des produits à afficher dans la variable `$products`.

Cela permet d'afficher automatiquement le contenu réel de la base de données.

### 2. Requête SQL pour récupérer les produits vendables

Code dans `src/controller.php` :

```php
function retrieveBuyableDisplayableProducts(): array
{
    $pdo = getDatabaseConnection();
    $products = retrieveBuyableProducts($pdo);

    ...
}
```

Code dans `src/database.php` :

```php
function retrieveBuyableProducts(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE is_available = 1 ORDER BY display_priority');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

#### Objectif

Cette opération vise à récupérer tous les produits disponibles à la vente.
La condition `is_available = 1` permet d'exclure les produits qui ne doivent pas apparaître dans le catalogue.
Le tri `ORDER BY display_priority` permet de maîtriser l'ordre d'affichage dans la page.

### 3. Formatage des données à afficher

Code dans `src/controller.php` :

```php
    foreach ($products as $index => $product) {
        $products[$index] = formatDisplayableProduct($product);
    }
    return $products;
```

#### Objectif

Le contrôleur joue ici le rôle d'intermédiaire entre la vue et la base de données.
Il récupère les produits bruts avec `retrieveBuyableProducts(...)`, puis il applique `formatDisplayableProduct(...)` à chacun d'eux.

Cela permet à la page HTML de recevoir directement des données prêtes à être affichées.
On comprend tout l'intérêt des fonctions:
le fait d'avoir centralisé le formatage évite de recopier la même logique dans plusieurs pages ou plusieurs fonctions du contrôleur.

### 4. Boucle d'affichage dans le HTML

Code dans `public/products.php` :

```php
<?php foreach ($products as $product) { ?>

    <article class="product-card">
        <a href="<?php echo $product['url']; ?>">
            <img src="<?php echo $product['image_url']; ?>"
                    alt="<?php echo $product['short_description']; ?>"
                    title="<?php echo $product['name']; ?>">
        </a>
        <h2>
            <a href="<?php echo $product['url']; ?>"><?php echo $product['name']; ?></a>
        </h2>
        <p class="product-card-description"><?php echo $product['short_description']; ?></p>
        <p class="product-card-price"><?php echo $product['price_tvac']; ?> TVAC</p>
        <a href="<?php echo $product['url']; ?>" class="btn-primary">Voir le produit</a>
    </article>

<?php } ?>
```

#### Objectif

Le HTML utilise maintenant une boucle `foreach` pour parcourir tous les produits récupérés.
