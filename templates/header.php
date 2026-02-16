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
        <!-- <nav class="main-nav">
            <a href="#">Guitares</a>
            <a href="#">Amplis</a>     
        </nav> -->
        <nav class="user-nav">
            <a href="#" class="basket-link" title="Voir le panier">🛒</a>
        </nav>
    </div>
</header>

<main class="container">