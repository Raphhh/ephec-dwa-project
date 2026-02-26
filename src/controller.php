<?php
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/utils.php';


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


