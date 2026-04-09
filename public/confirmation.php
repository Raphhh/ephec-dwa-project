<?php

require_once __DIR__ . '/../src/controller.php';

$orderId = manageConfirmation();

$title = 'Confirmation de commande - Rock Station';
$description = 'Confirmation de votre commande Rock Station.';
$specificCssFilePath = 'resources/css/confirmation.css';

include __DIR__ . ' /../templates/header.php';
?>

    <section class="confirmation">

        <p class="confirmation-message">
            Merci pour votre commande !
        </p>

        <p class="confirmation-details">
            Votre numéro de commande est :
            <strong><?php echo $orderId; ?></strong>
        </p>

        <p class="confirmation-status">
            Votre commande est actuellement en cours de traitement.
        </p>

        <div class="confirmation-actions">
            <a href="products.php" class="btn-primary">
                Continuer vos achats
            </a>
        </div>

    </section>

<?php
include __DIR__ . ' /../templates/footer.php';
?>