<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Tema saglabāšana
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $theme = isset($data['theme']) ? $data['theme'] : 'light';
    
    // Identifier pēc IP adreses
    $user_id = md5($_SERVER['REMOTE_ADDR']);
    
    $themes_file = 'data/themes.json';
    
    // Pārbaudām vai direktorija pastāv
    if (!is_dir('data')) {
        mkdir('data', 0755);
    }
    
    $themes = [];
    if (file_exists($themes_file)) {
        $themes = json_decode(file_get_contents($themes_file), true);
    }
    
    $themes[$user_id] = $theme;
    
    if (file_put_contents($themes_file, json_encode($themes))) {
        echo json_encode(['status' => 'success', 'message' => 'Tēma saglabāta']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Nevar saglabāt tēmu']);
    }
    exit();
}

// Tema ielāde
if ($action === 'load' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = md5($_SERVER['REMOTE_ADDR']);
    $themes_file = 'data/themes.json';
    $theme = 'light';
    
    if (file_exists($themes_file)) {
        $themes = json_decode(file_get_contents($themes_file), true);
        if (isset($themes[$user_id])) {
            $theme = $themes[$user_id];
        }
    }
    
    echo json_encode(['status' => 'success', 'theme' => $theme]);
    exit();
}

// Noklusējuma atbilde
echo json_encode(['status' => 'ok', 'message' => 'API darbojas']);
?>
