<?php
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/utils.php';


function retrieveDisplayableProduct(): array
{
    $id = (int) ($_GET['id'] ?? 0);

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
