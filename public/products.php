<?php

require_once __DIR__ . '/../src/controller.php';

$products = retrieveBuyableDisplayableProducts();


$title = 'Guitares et amplis - Rock Station';
$description = 'Découvrez notre sélection de guitares électriques disponibles chez Rock Station.';
$specificCssFilePath = 'resources/css/products.css';
include __DIR__ . ' /../templates/header.php';
?>
    
    <section class="toolbar">

        <h1>Guitares et amplis</h1>

        <form action="#" method="get" class="toolbar-form">
    
            <!-- Dropdown Catégories (multi-checkbox) -->
            <div class="dropdown">
    
                <button type="button" class="dropdown-toggle">
                    Catégories
                </button>
    
                <div class="dropdown-menu">
    
                    <label class="dropdown-item">
                        <input type="checkbox" name="categories[]" value="guitares">
                        Guitares
                    </label>
    
                    <label class="dropdown-item">
                        <input type="checkbox" name="categories[]" value="amplis">
                        Amplis
                    </label>
    
                    <label class="dropdown-item">
                        <input type="checkbox" name="categories[]" value="accessoires">
                        Accessoires
                    </label>
    
                </div>
            </div>
    
    
            <!-- Dropdown Ordre de prix -->
            <div class="toolbar-select">
                <label for="order" class="visually-hidden">Ordre de prix</label>
                <select name="order" id="order">
                    <option value="default">Popularité</option>
                    <option value="price_asc">Prix croissant</option>
                    <option value="price_desc">Prix décroissant</option>
                </select>
            </div>
    
    
            <!-- Bouton neutre -->
            <button type="submit" class="btn-secondary">
                Appliquer
            </button>
    
        </form>
    
    </section>
    
    <section class="products-grid">

        <?php foreach ($products as $product) { ?>

            <!-- Produit <?php echo $product['id']; ?> -->
            <article class="product-card">
                <a href="<?php echo $product['url']; ?>">
                    <img src="resources/products/<?php echo $product['img_file_path']; ?>"
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

    </section>

    
<?php
include __DIR__ . ' /../templates/footer.php';
?>
