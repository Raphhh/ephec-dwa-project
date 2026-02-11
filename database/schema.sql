
/* =========================================================
   PRODUCTS
========================================================= */

CREATE TABLE products (
    id MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    name VARCHAR(255) NOT NULL,
    short_description VARCHAR(500) NOT NULL,
    long_description TEXT NOT NULL,
    price_htva DECIMAL(10,2) NOT NULL,
    is_available BOOLEAN NOT NULL DEFAULT FALSE,
    stock SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    display_priority MEDIUMINT NOT NULL DEFAULT 0,
    img_file_path VARCHAR(255) NOT NULL,

    INDEX idx_available (is_available),
    INDEX idx_priority (display_priority),
    INDEX idx_price (price_htva)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   CATEGORIES
========================================================= */

CREATE TABLE categories (
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    name VARCHAR(255) NOT NULL UNIQUE,
    description VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE product_category (
    product_id MEDIUMINT UNSIGNED NOT NULL,
    category_id SMALLINT UNSIGNED NOT NULL,

    PRIMARY KEY (product_id, category_id),

    FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE,

    FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   CUSTOMERS
========================================================= */

CREATE TABLE customers (
    id MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(255) NOT NULL UNIQUE,
    first_name VARCHAR(150) NOT NULL,
    last_name VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE addresses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    customer_id MEDIUMINT UNSIGNED NOT NULL,
    street VARCHAR(255) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    city VARCHAR(150) NOT NULL,
    country VARCHAR(150) NOT NULL,

    FOREIGN KEY (customer_id)
        REFERENCES customers(id)
        ON DELETE CASCADE,

    INDEX idx_customer (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   ORDERS
========================================================= */

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    confirmed_at TIMESTAMP NULL DEFAULT NULL,
    customer_id MEDIUMINT UNSIGNED NOT NULL,
    delivery_address_id INT UNSIGNED NOT NULL,
    total_htva DECIMAL(10,2) NOT NULL,
    total_tvac DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (customer_id)
        REFERENCES customers(id)
        ON DELETE RESTRICT,

    FOREIGN KEY (delivery_address_id)
        REFERENCES addresses(id)
        ON DELETE RESTRICT,

    INDEX idx_customer (customer_id),
    INDEX idx_confirmed (confirmed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE order_lines (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    order_id INT UNSIGNED NOT NULL,
    product_id MEDIUMINT UNSIGNED NOT NULL,
    quantity SMALLINT UNSIGNED NOT NULL,
    unit_price_htva DECIMAL(10,2) NOT NULL,
    line_total_htva DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE RESTRICT,

    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

