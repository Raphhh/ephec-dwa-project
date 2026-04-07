<?php

require_once __DIR__ . '/../src/database.php';

function updateItemIntoBasket(array $basket, int $productId, int $quantity): array
{
    $basket[$productId] = $quantity;
    if ($basket[$productId] <= 0) {
        unset($basket[$productId]);
    }
    return $basket;
}

function extendBasket(PDO $pdo, array $basket): array
{
    $order = [
        'items' => [],
        'total' => [
            'count' => 0,
            'htva' => 0,
            'tvac' => 0,
        ],
        'is_valid' => false,
    ];

    foreach ($basket as $productId => $quantity) {
        /**
         * Ce code devrait être optimisé.
         * En effet, on réalise autant de query SQL qu'il existe de produits
         * Alors qu'une seule serait nécessaire.
         */
        $product = retrieveProductById($pdo, $productId);
        if (!$product) {
            continue;
        }

        $htva = $product['price_htva'] * $quantity;

        $order['items'][] = [
            'product' => $product,
            'quantity' => $quantity,
            'total_htva' => $htva,
            'validity' => validateProductOrder($product, $quantity),
        ];
        $order['total']['count']++;
        $order['total']['htva'] += $htva;
        $order['total']['tvac'] += addTva($htva);
    }

    $order['is_valid'] = validateFullBasket($order);

    return $order;
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

function validateFullBasket(array $fullBasket): bool
{
    foreach ($fullBasket['items'] as $item) {
        if (!$item['validity']['is_valid']) {
            return false;
        }
    }
    return true;
}
