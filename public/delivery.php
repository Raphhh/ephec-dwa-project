<?php

require_once __DIR__ . '/../src/controller.php';

$data = manageDelivery();
$form = $data['form'];
$isPost = $data['is_post'];
unset($data);

$title = 'Adresse de livraison - Rock Station';
$description = 'Adresse de livraison pour votre commande Rock Station.';
$specificCssFilePath = 'resources/css/delivery.css';
include __DIR__ . ' /../templates/header.php';
?>

    <section class="toolbar">
        <h1>Informations de livraison</h1>
    </section>

    <section class="delivery-form-section">

        <?php if ($isPost) { ?>
            <p class="message-error">Une erreur est survenue dans le traitement du formulaire.</p>
        <?php } ?>

        <form action="#" method="post" class="delivery-form">
            <input type="hidden" name="token" value="<?php echo $form['token']; ?>">

            <section class="delivery-form-col">

                <fieldset>
                    <legend>Commanditaire</legend>

                    <div class="form-group">
                        <label for="email">Email de contact</label>
                        <input
                                type="email"
                                id="email"
                                name="email"
                                maxlength="255"
                                required
                                value="<?php echo $form['email']; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="lastname">Nom</label>
                        <input
                                type="text"
                                id="lastname"
                                name="lastname"
                                maxlength="150"
                                required
                                value="<?php echo $form['lastname']; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="firstname">Prénom</label>
                        <input
                                type="text"
                                id="firstname"
                                name="firstname"
                                maxlength="150"
                                required
                                value="<?php echo $form['firstname']; ?>"
                        >
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Adresse de livraison</legend>

                    <div class="form-group">
                        <label for="street">Rue</label>
                        <input
                                type="text"
                                id="street"
                                name="street"
                                maxlength="255"
                                required
                                value="<?php echo $form['street']; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="postal">Code postal</label>
                        <input
                                type="text"
                                id="postal"
                                name="postal"
                                maxlength="20"
                                required
                                value="<?php echo $form['postal']; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="city">Ville</label>
                        <input
                                type="text"
                                id="city"
                                name="city"
                                maxlength="150"
                                required
                                value="<?php echo $form['city']; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="country">Pays</label>
                        <input
                                type="text"
                                id="country"
                                name="country"
                                maxlength="150"
                                required
                                value="<?php echo $form['country']; ?>"
                        >
                    </div>
                </fieldset>

            </section>

            <div class="delivery-actions btn-list">
                <a href="basket.php" class="btn-neutral">
                    Retour au panier
                </a>
                <button type="submit" class="btn-primary">
                    Confirmer la commande
                </button>
            </div>

        </form>

    </section>

<?php
include __DIR__ . ' /../templates/footer.php';
?>