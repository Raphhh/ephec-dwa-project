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

    $product['price_tvac'] = formatPrice(addTva($product['price_htva']));
    $product['price_htva'] = formatPrice($product['price_htva']);
    $product['image_url'] = formatProductImageUrl($product['img_file_path']);

    return $product;
}
