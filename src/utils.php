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
    $product['url'] = formatProductUrl($product['id']);

    return $product;
}