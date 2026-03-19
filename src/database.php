<?php

require_once __DIR__ . '/../config.php';

function getDatabaseConnection(): PDO
{
	$dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset=' . DB_CHARSET;
    return new PDO($dsn, DB_USER, DB_PWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}

function retrieveProductById(PDO $pdo, $id): array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

function retrieveBuyableProducts(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE is_available = 1 ORDER BY display_priority');
    $stmt->execute();

    $products = [];
    while($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[] = $product;
    }
    return $products;
}