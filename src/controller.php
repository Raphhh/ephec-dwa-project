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
    return formatDisplayableProduct($product);
}

function retrieveBuyableDisplayableProducts(): array 
{
    $pdo = getDatabaseConnection();
    $products = retrieveBuyableProducts($pdo);

    foreach ($products as $index => $product) {
        $products[$index] = formatDisplayableProduct($product);
    }
    return $products;
}
