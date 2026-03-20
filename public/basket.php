<?php

require_once __DIR__ . '/../src/controller.php';
$basket = retrieveCurrentBasket();

$title = 'Panier - Rock Station';
$description = 'Résumé de votre commande chez Rock Station.';
$specificCssFilePath = 'resources/css/basket.css';
include __DIR__ . ' /../templates/header.php';
?>

    <section class="toolbar">
        <h1>Votre panier</h1>
    </section>

    <section class="basket">

    <?php if (!empty($basket['items'])) { ?>

        <table class="basket-table">

            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix unitaire HTVA</th>
                    <th>Quantité</th>
                    <th>Total HTVA</th>
                </tr>
            </thead>

            <tbody>

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

            </tbody>

            <tfoot>

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

            </tfoot>

        </table>

    <?php } else { ?>
        <p class="basket-notification">Votre panier est actuellement vide.</p>
    <?php } ?>

    </section>

<?php
include __DIR__ . ' /../templates/footer.php';
?>