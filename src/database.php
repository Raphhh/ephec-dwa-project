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

function retrieveBuyableProducts(PDO $pdo, array $categoryIds = []): array
{
    $categoryClause = '';
    $categoryParams = [];

    if ($categoryIds) {

        foreach ($categoryIds as $index => $categoryId) {
            $categoryParams[':cat' . $index] = $categoryId;
        }

        $categoryClause = 'AND id IN (
            SELECT DISTINCT product_id
            FROM product_category
            WHERE category_id IN (' . implode(',', array_keys($categoryParams)) . ')
        )';

    }

    $query = "SELECT *
                FROM products
                WHERE is_available = 1
                $categoryClause
                ORDER BY display_priority";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($categoryParams);

    $products = [];
    while($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[] = $product;
    }
    return $products;
}

function retrieveEffectiveCategories(PDO $pdo): array
{
    $stmt = $pdo->prepare(
        'SELECT * 
            FROM categories 
            WHERE id IN (
                SELECT 
                    DISTINCT category_id 
                    FROM product_category
                    JOIN products ON product_category.product_id = products.id
                    WHERE products.is_available = 1
            )
            ORDER BY categories.name'
    );
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
