<?php

$title = 'Panier - Rock Station';
$description = 'Résumé de votre commande chez Rock Station.';
$specificCssFilePath = 'resources/css/basket.css';
include __DIR__ . ' /../templates/header.php';
?>

    <section class="toolbar">
        <h1>Votre panier</h1>
    </section>

    <section class="basket">

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

                <tr>
                    <td>
                        <a href="product.php">
                            <img src="resources/products/gibson-les-paul-standard-60s-honey-amber_1_GIT0062796-002.webp"
                                    alt="Gibson Les Paul Standard 60s"
                                    class="basket-img">
                            Gibson Les Paul Standard 60s
                        </a>
                    </td>
                    <td>2 000 €</td>
                    <td>1</td>
                    <td>2 000 €</td>
                </tr>

                <tr>
                    <td>
                        <a href="product.php">
                            <img src="resources/products/fender-american-ultra-ii-stratocaster-eb-texas-tea_1_GIT0061889-003.webp"
                                    alt="Fender Stratocaster"
                                    class="basket-img">
                            Fender Stratocaster
                        </a>
                    </td>
                    <td>1 487 €</td>
                    <td>2</td>
                    <td>2 974 €</td>
                </tr>

                <tr>
                    <td>
                        <a href="product.php">
                            <img src="resources/products/fender-65-twin-reverb-combo-_1_GIT0000081-000.webp"
                                    alt="Fender Twin Reverb"
                                    class="basket-img">
                            Fender Twin Reverb
                        </a>    
                    </td>
                    <td>1 941 €</td>
                    <td>1</td>
                    <td>1 941 €</td>
                </tr>

                <tr>
                    <td>
                        <a href="product.php">
                            <img src="resources/products/vox-ac30-c2-combo-_1_GIT0018374-000.webp"
                                    alt="Vox AC30"
                                    class="basket-img">
                            Vox AC30
                        </a>
                    </td>
                    <td>1 115 €</td>
                    <td>1</td>
                    <td>1 115 €</td>
                </tr>

            </tbody>

            <tfoot>

                <tr>
                    <th colspan="3">Nombre total d’articles</th>
                    <td>5</td>
                </tr>

                <tr>
                    <th colspan="3">Total HTVA</th>
                    <td>8 030 €</td>
                </tr>

                <tr>
                    <th colspan="3">Total TVAC (21%)</th>
                    <td>9 716,30 €</td>
                </tr>

            </tfoot>

        </table>


    </section>
    
<?php
include __DIR__ . ' /../templates/footer.php';
?>