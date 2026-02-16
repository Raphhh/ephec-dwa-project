<?php

require_once __DIR__ . '/../src/controller.php';

$product = retrieveDisplayableProduct();

if ($product) {
    $title = $product['name'] . ' - Rock Station';
    $description = $product['short_description'];
} else {
    $title = 'Produit introuvable  - Rock Station';
    $description = '';
}
$specificCssFilePath = 'resources/css/product.css';
include __DIR__ . ' /../templates/header.php';
?>

<?php if ($product) { ?>

    <article class="product">

        <section class="product-layout">

            <div class="product-gallery">
                <img src="<?php echo $product['image_url']; ?>"
                     alt="<?php echo $product['short_description']; ?>"
                     title="<?php echo $product['name']; ?>"
                     class="main-image">
            </div>

            <div class="product-info">
                <h1 class="product-title"><?php echo $product['name']; ?></h1>

                <p class="short-description"><?php echo $product['short_description']; ?></p>

                <div class="price-block">
                    <p class="price-ht">Prix HTVA : <strong><?php echo $product['price_htva']; ?></strong></p>
                    <p class="price-tva">Prix TVAC (<?php echo fomatTva();?>) : <strong><?php echo $product['price_tvac']; ?></strong></p>
                </div>

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


            </div>

        </section>

        <section class="product-description">
            <h2>Description détaillée</h2>
            <p>
                <?php echo $product['long_description']; ?>
            </p>
        </section>

    </article>

<?php } else { ?>
    <section class="toolbar">

        <h1>Produit introuvable</h1>

    </section>
<?php } ?>

<?php
include __DIR__ . ' /../templates/footer.php';
?>
