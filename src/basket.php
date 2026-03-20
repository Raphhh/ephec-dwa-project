<?php

require_once __DIR__ . '/../src/database.php';

function getBasketItem(array $basket, array $item): ?array
{
    $itemIndex = getBasketItemIndex($basket, $item);
    if ($itemIndex !== null) {
        return $basket[$itemIndex];
    }
    return null;
}

function getBasketItemIndex(array $basket, array $search): ?int
{
    foreach ($basket as $index => $item) {
        if ($item['product_id'] == $search['product_id']) {
            return $index;
        }
    }
    return null;
}

function addItemToBasket(array $basket, array $new): array
{
    $itemIndex = getBasketItemIndex($basket, $new);
    if ($itemIndex !== null) {
        $basket[$itemIndex]['quantity'] += $new['quantity'];
    } else {
        $basket[] = $new;
    }
    return $basket;
}

function validateBasketItem(PDO $pdo, array $item): array
{
    $product = retrieveProductById($pdo, $item['product_id']);
    if (!$product) {
        return generateProductOrderValidation(false, false);
    }
    return validateProductOrder($product, $item['quantity']);
}

function validateProductOrder(array $product, $quantity): array
{
    return generateProductOrderValidation(
        (bool) $product['is_available'],
        $quantity <= $product['stock']
    );
}

function generateProductOrderValidation(bool $isAvailableProduct, bool $isAvailableStock): array
{
    $result = [
        'is_valid' => true,
        'is_available_product' => $isAvailableProduct,
        'is_available_stock' => $isAvailableStock,
    ];

    foreach ($result as $validation) {
        if (!$validation) {
            $result['is_valid'] = false;
            break;
        }
    }
    return $result;
}
