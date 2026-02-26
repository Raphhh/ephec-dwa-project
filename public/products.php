<?php

require_once __DIR__ . '/../src/controller.php';

$data = retrieveBuyableDisplayableProducts();
$products = $data['products'];
$categories = $data['categories'];
unset($data);


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

                    <?php foreach ($categories as $category) { ?>

                        <label class="dropdown-item">
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="<?php echo $category['id']; ?>"
                                <?php if ($category['is_checked']) { ?>checked<?php } ?>
                                >
                            <?php echo $category['name']; ?>
                        </label>

                    <?php } ?>

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

    </section>

    
<?php
include __DIR__ . ' /../templates/footer.php';
?>
