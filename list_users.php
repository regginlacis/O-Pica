<?php
require_once 'config.php';
echo "USER LIST\n\n";
$result = $conn->query("SELECT user_id, username, email, role, created_at FROM users ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
    echo "Total users: " . $result->num_rows . "\n\n";
    while ($user = $result->fetch_assoc()) {
        echo "ID: {$user['user_id']} | {$user['username']} | {$user['email']} | {$user['role']}\n";
    }
} else {
    echo "No users in database\n\n";
    echo "Register: http://localhost/opica/register.php\n";
}
$conn->close();
?>
