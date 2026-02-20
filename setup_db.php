<?php
// Datubāzes izveidošana un tabulu setup
require_once 'config.php';

// Pārbaudām vai jau pastāv users tabula
$users_table = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    theme VARCHAR(10) DEFAULT 'light',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Pārbaudām vai jau pastāv user_preferences tabula
$preferences_table = "CREATE TABLE IF NOT EXISTS user_preferences (
    pref_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    theme VARCHAR(10) DEFAULT 'light',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";

// Izveidojam tabulas
if ($conn->query($users_table) === TRUE) {
    echo "✓ Users tabula ir gatava<br>";
} else {
    echo "✗ Kļūda users tabulā: " . $conn->error . "<br>";
}

if ($conn->query($preferences_table) === TRUE) {
    echo "✓ User preferences tabula ir gatava<br>";
} else {
    echo "✗ Kļūda user_preferences tabulā: " . $conn->error . "<br>";
}

echo "<br><strong>Datubāse ir inicializēta!</strong><br>";
echo "Tagad varat dzēst šo failu (setup_db.php)";
?>
