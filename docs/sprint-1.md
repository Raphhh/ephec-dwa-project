# Sprint 1

## Objectif du sprint

Pages produits statiques.

## Fonctionnalités

## Templates statiques

Ajout des templates:
 - `public/products.php`: catalogue des produits
 - `public/product.php`: fiche d'un produit


## Réunion des templates communs

Cette solution permet d'éviter de répéter le même code HTML dans plusieurs pages.

### 1. Inclusion d'un template header

Code dans `public/product.php` :

```php
<?php
$title = 'Gibson Les Paul Standard - Rock Station';
$description = 'Gibson Les Paul Standard 60s - Guitare électrique rock avec table érable et micros Burstbucker.';
$specificCssFilePath = 'resources/css/product.css';
include __DIR__ . ' /../templates/header.php';
?>
```

Code dans `public/products.php` :

```php
<?php
$title = 'Guitares et amplis - Rock Station';
$description = 'Découvrez notre sélection de guitares électriques disponibles chez Rock Station.';
$specificCssFilePath = 'resources/css/products.css';
include __DIR__ . ' /../templates/header.php';
?>
```

Code dans `templates/header.php` :

```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $description; ?>">
    <link rel="stylesheet" href="resources/css/main.css">
    <?php if (isset($specificCssFilePath)) { ?>
        <link rel="stylesheet" href="<?php echo $specificCssFilePath; ?>">
    <?php } ?>
</head>
<body>
    <header class="site-header">
        <div class="container header-flex">
            <p class="logo"><a href="products.php">Rock Station</a></p>
            <nav class="user-nav">
                <a href="#" class="basket-link" title="Voir le panier">🛒</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
```

#### Objectif

Chaque page définit d'abord les informations dont le template commun a besoin :
- le titre de la page ;
- la description SEO ;
- le fichier CSS spécifique à cette page.

Ces variables sont définies avant l'inclusion du header pour que `templates/header.php` puisse les utiliser directement.
Ce fichier regroupe tout le début du document HTML commun à plusieurs pages :
- la structure HTML de base ;
- les balises `<head>` ;
- le chargement du CSS principal ;
- le chargement éventuel d'un CSS spécifique ;
- le header.

A noter le test `isset($specificCssFilePath)` permet de charger un fichier CSS supplémentaire seulement si la page en a besoin.


### 2. Inclusion d'un template footer

Code dans `public/product.php` :

```php
<?php
include __DIR__ . ' /../templates/footer.php';
?>
```

Code dans `public/products.php` :

```php
<?php
include __DIR__ . ' /../templates/footer.php';
?>
```

Code dans `templates/footer.php` :

```php
</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; 2026 RockStation - Tous droits réservés</p>
    </div>
</footer>

</body>
</html>
```

#### Objectif

Ce fichier contient la fin commune du document HTML :
- la fermeture de la balise `<main>` ;
- le pied de page ;
- la fermeture de `body` et `html`.

Comme pour le header, le but est d'écrire ce code une seule fois, puis de le réutiliser dans plusieurs pages.
