<?php
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/utils.php';


function retrieveDisplayableProduct(): array
{
    $id = (int) ($_GET['id'] ?? 0);

    $pdo = getDatabaseConnection();
    $product = retrieveProductById($pdo, $id);

    $product['price_tvac'] = formatPrice(addTva($product['price_htva']));
    $product['price_htva'] = formatPrice($product['price_htva']);
    $product['image_url'] = formatProductImageUrl($product['img_file_path']);

    return $product;
}
