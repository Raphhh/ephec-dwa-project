<?php
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/utils.php';
require_once __DIR__ . '/../src/basket.php';
require_once __DIR__ . '/../src/order.php';
require_once __DIR__ . '/../src/session.php';


function retrieveDisplayableProduct(): array
{
    if (empty($_GET['id'])) {
        return [];
    }
    
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    $pdo = getDatabaseConnection();
    $product = retrieveProductById($pdo, $id);
    if ($product) {
        return formatDisplayableProduct($product);
    }
    return $product;
}

function retrieveBuyableDisplayableProducts(): array
{
    $checkedCategories = $_GET['categories'] ?? [];
    foreach ($checkedCategories as $index => $id) {
        $checkedCategories[$index] = filter_var($id, FILTER_VALIDATE_INT);
    }

    $order = $_GET['order'] ?? '';

    $pdo = getDatabaseConnection();
    $products = retrieveBuyableProducts($pdo, $checkedCategories, $order);

    foreach ($products as $index => $product) {
        $products[$index] = formatDisplayableProduct($product);
    }

    $categories = retrieveEffectiveCategories($pdo);
    foreach ($categories as $index => $category) {
        $categories[$index]['is_checked'] = in_array(
            $category['id'], 
            $checkedCategories
        );
    }

    return [
        'products' => $products,
        'categories' => $categories,
        'order' => $order,
    ];
}

function updateBasket(): void
{
    $productId = filter_var($_POST['product_id'] ?? 0, FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'] ?? 0, FILTER_VALIDATE_INT);

    $pdo = getDatabaseConnection();

    $modifiedBasket = updateItemIntoBasket(retrieveBasketFromSession(), $productId, $quantity);
    saveBasketIntoSession($modifiedBasket);

    header('content-type: application/json');
    echo json_encode([
        'basket' => formatDisplayableFullBasket(
            extendBasket($pdo, retrieveBasketFromSession())
        ),
    ]);
}

function retrieveCurrentBasket(): array
{
    $pdo = getDatabaseConnection();

    $basket = extendBasket($pdo, retrieveBasketFromSession());
    return formatDisplayableFullBasket($basket);
}

function manageDelivery()
{
    $pdo = getDatabaseConnection();

    $basket = extendBasket($pdo, retrieveBasketFromSession());
    if (!$basket['is_valid']) {
        header('Location: ./basket.php');
        exit();
    }

    $isPost = !empty($_POST['token']);

    $form = [];
    $form['token'] = $_POST['token'] ?? '';
    $form['email'] = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $form['lastname'] = $_POST['lastname'] ?? '';
    $form['firstname'] = $_POST['firstname'] ?? '';
    $form['street'] = $_POST['street'] ?? '';
    $form['postal'] = $_POST['postal'] ?? '';
    $form['city'] = $_POST['city'] ?? '';
    $form['country'] = $_POST['country'] ?? '';

    if ($form['token'] == retrieveCSRFToken() && validateDeliveryForm($form)) {
        $orderId = processToOrder($pdo, $form, $basket);
        if ($orderId) {
            saveBasketIntoSession([]);
            header("Location: ./confirmation.php?order=$orderId");
            exit();
        }
    }

    $form['token'] = renewCSRFToken();
    return [
        'form' => $form,
        'is_post' => $isPost,
    ];
}
