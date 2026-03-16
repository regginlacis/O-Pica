<?php
/**
 * Save theme preference to session
 */
session_start();

if (isset($_GET['theme'])) {
    $theme = ($_GET['theme'] === 'dark' || $_GET['theme'] === 'light') ? $_GET['theme'] : 'light';
    $_SESSION['theme'] = $theme;
    setcookie('theme', $theme, time() + (86400 * 365), '/'); // 1 year
}

echo json_encode(['success' => true]);
?>
