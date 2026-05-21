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

function retrieveBuyableProducts(PDO $pdo, array $categoryIds = [], string $order = ''): array
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

    $orderClause = 'display_priority ASC';
    if ($order === 'price_asc') {
        $orderClause = 'price_htva ASC, ' . $orderClause;
    } elseif ($order === 'price_desc') {
        $orderClause = 'price_htva DESC, ' . $orderClause;
    }

    $query = "SELECT *
                FROM products
                WHERE is_available = 1
                $categoryClause
                ORDER BY $orderClause";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($categoryParams);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

function retrieveCustomerIdByEmail(PDO $pdo, string $email): int
{
    $stmt = $pdo->prepare('SELECT id FROM customers WHERE email = :email');
    $stmt->execute(['email' => $email]);
    return (int) ($stmt->fetchColumn() ?: 0);
}

function createCustomer(PDO $pdo, string $email, string $firstName, string $lastName): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO customers (email, first_name, last_name)
         VALUES (:email, :first_name, :last_name)'
    );
    $stmt->execute([
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName,
    ]);
    return (int) $pdo->lastInsertId();
}

function createAddress(
    PDO $pdo,
    int $customerId,
    string $street,
    string $zipCode,
    string $city,
    string $country
): int {
    $stmt = $pdo->prepare(
        'INSERT INTO addresses (customer_id, street, zip_code, city, country)
         VALUES (:customer_id, :street, :zip_code, :city, :country)'
    );
    $stmt->execute([
        'customer_id' => $customerId,
        'street' => $street,
        'zip_code' => $zipCode,
        'city' => $city,
        'country' => $country,
    ]);
    return (int) $pdo->lastInsertId();
}

function createOrder(PDO $pdo, int $customerId, int $deliveryAddressId, float $totalHtva, float $totalTvac): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO orders (customer_id, delivery_address_id, total_htva, total_tvac)
         VALUES (:customer_id, :delivery_address_id, :total_htva, :total_tvac)'
    );
    $stmt->execute([
        'customer_id' => $customerId,
        'delivery_address_id' => $deliveryAddressId,
        'total_htva' => $totalHtva,
        'total_tvac' => $totalTvac,
    ]);
    return (int) $pdo->lastInsertId();
}

function createOrderLine(
    PDO $pdo,
    int $orderId,
    int $productId,
    int $quantity,
    float $unitPriceHtva,
    float $lineTotalHtva
): void {
    $stmt = $pdo->prepare(
        'INSERT INTO order_lines (order_id, product_id, quantity, unit_price_htva, line_total_htva)
         VALUES (:order_id, :product_id, :quantity, :unit_price_htva, :line_total_htva)'
    );
    $stmt->execute([
        'order_id' => $orderId,
        'product_id' => $productId,
        'quantity' => $quantity,
        'unit_price_htva' => $unitPriceHtva,
        'line_total_htva' => $lineTotalHtva,
    ]);
}

function decreaseProductStock(PDO $pdo, int $productId, int $quantity): void
{
    $stmt = $pdo->prepare(
        'UPDATE products
         SET stock = stock - :quantity
         WHERE id = :id'
    );
    $stmt->execute([
        'id' => $productId,
        'quantity' => $quantity,
    ]);
}
