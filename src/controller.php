<?php
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/utils.php';
require_once __DIR__ . '/../src/basket.php';
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

    $modifiedBasket = updateItemIntoBasket(retrieveBasketFromSession(), $productId, $quantity);
    saveBasketIntoSession($modifiedBasket);

    header('content-type: application/json');
    echo json_encode([
        'basket' => retrieveBasketFromSession(),
    ]);
}

function retrieveCurrentBasket(): array
{
    $pdo = getDatabaseConnection();

    $basket = extendBasket($pdo, retrieveBasketFromSession());
    return formatDisplayableFullBasket($basket);
}
