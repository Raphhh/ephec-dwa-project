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

