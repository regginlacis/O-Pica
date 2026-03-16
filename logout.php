<?php
/**
 * LOGOUT.PHP - Iziet no Sistēmas
 */

session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;

// Iegrāmatoji lietotāja logout
if ($user_id) {
    log_action('Logout', $user_id, ['message' => 'User logged out']);
}

// Nodzēši sesiju
session_destroy();

// Novirzīti uz sākumu
header('Location: index.php?success=logged_out');
exit();
?>
