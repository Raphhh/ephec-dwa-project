<?php
$title = 'Gibson Les Paul Standard - Rock Station';
$description = 'Gibson Les Paul Standard 60s - Guitare électrique rock avec table érable et micros Burstbucker.';
$specificCssFilePath = 'resources/css/product.css';
include __DIR__ . ' /../templates/header.php';
?>

    <article class="product">

        <section class="product-layout">

            <div class="product-gallery">
                <img src="resources\products\gibson-les-paul-standard-60s-honey-amber_1_GIT0062796-002.webp"
                     alt="Gibson Les Paul Standard 60s finition Cherry Sunburst"
                     title="Gibson Les Paul Standard 60s"
                     class="main-image">
            </div>

            <div class="product-info">
                <h1 class="product-title">Gibson Les Paul Standard 60s</h1>

                <p class="short-description">
                    Guitare électrique iconique du rock, équipée de micros Burstbucker,
                    corps en acajou et table en érable.
                </p>

                <div class="price-block">
                    <p class="price-ht">Prix HTVA : <strong>2 000 €</strong></p>
                    <p class="price-tva">Prix TVAC (21%) : <strong>2 420 €</strong></p>
                </div>

                <p class="stock in-stock">
                    ✔ En stock (3 disponibles)
                </p>

                <p class="stock out-of-stock">
                    ✘ Rupture de stock
                </p>


            </div>

        </section>

        <section class="product-description">
            <h2>Description détaillée</h2>
            <p>
                La Les Paul Standard 60s reprend les caractéristiques classiques
                du modèle original : manche Slim Taper, table en érable AA,
                micros Burstbucker 61, chevalet Tune-O-Matic.
            </p>
            <p>
                Son sustain exceptionnel et sa puissance en font une référence
                incontournable pour les styles rock, hard rock et blues.
            </p>
        </section>

    </article>

<?php
include __DIR__ . ' /../templates/footer.php';
?>
