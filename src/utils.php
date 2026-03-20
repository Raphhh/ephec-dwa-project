<?php

function addTva($price): float
{
    return $price * (1 + TVA);
}

function fomatTva(): string
{
    return (TVA * 100) . '%';
}

function formatPrice($price): string
{
    return number_format($price, 0, ',', ' ') . ' €';
}

function formatProductImageUrl($imagePath): string 
{
    return 'resources/products/' . $imagePath;
}

function formatProductUrl($id): string
{
    return 'product.php?id=' . $id;
}

function formatDisplayableProduct(array $product): array
{
    $product['price_tvac'] = formatPrice(addTva($product['price_htva']));
    $product['price_htva'] = formatPrice($product['price_htva']);
    $product['image_url'] = formatProductImageUrl($product['img_file_path']);
    unset($product['img_file_path']);
    $product['url'] = formatProductUrl($product['id']);

    return $product;
}

function formatDisplayableFullBasket(array $fullBasket): array
{
    foreach ($fullBasket['items'] as $index => $item) {
        $fullBasket['items'][$index]['product'] = formatDisplayableProduct($item['product']);
        $fullBasket['items'][$index]['total_htva'] = formatPrice($item['total_htva']);
    }

    $fullBasket['total']['htva'] = formatPrice($fullBasket['total']['htva']);
    $fullBasket['total']['tvac'] = formatPrice($fullBasket['total']['tvac']);

    return $fullBasket;
}

function retrieveInputJson(): array
{
    $json = file_get_contents('php://input');
    return (array) json_decode($json, true, 512, JSON_THROW_ON_ERROR);
}
