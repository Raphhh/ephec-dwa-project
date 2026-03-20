<?php
session_start();

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
        $product = formatDisplayableProduct($product);
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

function addProductToBasket(): void
{
    $data = retrieveInputJson();
    $data['product_id'] = filter_var($data['product_id'], FILTER_VALIDATE_INT);
    $data['quantity'] = filter_var($data['quantity'], FILTER_VALIDATE_INT);

    $pdo = getDatabaseConnection();

    $modifiedBasket = addItemToBasket(retrieveBasketFromSession(), $data);
    $modifiedItem = getBasketItem($modifiedBasket, $data);
    $validation = validateBasketItem($pdo, $modifiedItem);
    if ($validation['is_valid']) {
        saveBasketIntoSession($modifiedBasket);
        $status = 200;
    } else {
        http_response_code(400);
        $status = 400;
    }

    header('content-type: application/json');
    echo json_encode([
        'status' => $status,
        'basket' => retrieveBasketFromSession(),
        'item' => $modifiedItem,
        'validation' => $validation,
    ]);
}
