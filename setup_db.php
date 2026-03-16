<?php
/**
 * Database Setup Script
 */
require_once 'config.php';

echo "<h2>Setting up O! Pica Database...</h2>";
echo "<hr>";

// Create users table
$users_table = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    theme VARCHAR(10) DEFAULT 'light',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($users_table) === TRUE) {
    echo "OK - Users table is ready<br>";
} else {
    echo "ERROR - Error creating users table: " . $conn->error . "<br>";
}

// Create pizzas table
$pizzas_table = "CREATE TABLE IF NOT EXISTS pizzas (
    pizza_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_filename VARCHAR(255),
    allergens TEXT,
    category VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($pizzas_table) === TRUE) {
    echo "OK - Pizzas table is ready<br>";
} else {
    echo "ERROR - Error creating pizzas table: " . $conn->error . "<br>";
}

// Create orders table
$orders_table = "CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    payment_method VARCHAR(50),
    delivery_method VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
)";

if ($conn->query($orders_table) === TRUE) {
    echo "OK - Orders table is ready<br>";
} else {
    echo "ERROR - Error creating orders table: " . $conn->error . "<br>";
}

// Create order items table
$order_items_table = "CREATE TABLE IF NOT EXISTS order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    pizza_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (pizza_id) REFERENCES pizzas(pizza_id) ON DELETE RESTRICT
)";

if ($conn->query($order_items_table) === TRUE) {
    echo "OK - Order items table is ready<br>";
} else {
    echo "ERROR - Error creating order items table: " . $conn->error . "<br>";
}

// Create support requests table
$support_table = "CREATE TABLE IF NOT EXISTS support_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($support_table) === TRUE) {
    echo "OK - Support requests table is ready<br>";
} else {
    echo "ERROR - Error creating support requests table: " . $conn->error . "<br>";
}

echo "<hr>";
echo "<h3>Database setup complete!</h3>";
echo "<p><a href='login.php'>Try Login</a> | <a href='register.php'>Register</a></p>";
?>
