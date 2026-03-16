<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$orders_file = 'data/orders.json';

// Pārbaudām vai direktorija pastāv
if (!is_dir('data')) {
    mkdir('data', 0755);
}

// Pasūtījuma izveide
if ($action === 'create_order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['orderId']) || !isset($data['items']) || !isset($data['total'])) {
        echo json_encode(['success' => false, 'message' => 'Nepieciešami dati']);
        exit();
    }
    
    $orders = [];
    if (file_exists($orders_file)) {
        $orders = json_decode(file_get_contents($orders_file), true);
    }
    
    $new_order = [
        'id' => $data['orderId'],
        'items' => $data['items'],
        'total' => $data['total'],
        'timestamp' => date('Y-m-d H:i:s'),
        'status' => 'pending'
    ];
    
    $orders[] = $new_order;
    
    if (file_put_contents($orders_file, json_encode($orders))) {
        echo json_encode(['success' => true, 'message' => 'Pasūtījums izveidots', 'order' => $new_order]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nevar saglabāt pasūtījumu']);
    }
    exit();
}

// Visu pasūtījumu iegūšana
if ($action === 'get_all_orders' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $orders = [];
    if (file_exists($orders_file)) {
        $orders = json_decode(file_get_contents($orders_file), true);
    }
    
    echo json_encode(['success' => true, 'orders' => $orders]);
    exit();
}

// Pasūtījuma atzīmēšana kā piegādāta
if ($action === 'mark_delivered' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['orderId'])) {
        echo json_encode(['success' => false, 'message' => 'Pasūtījuma ID nepieciešams']);
        exit();
    }
    
    $orders = [];
    if (file_exists($orders_file)) {
        $orders = json_decode(file_get_contents($orders_file), true);
    }
    
    $found = false;
    foreach ($orders as &$order) {
        if ($order['id'] === $data['orderId']) {
            $order['status'] = 'delivered';
            $found = true;
            break;
        }
    }
    
    if ($found && file_put_contents($orders_file, json_encode($orders))) {
        echo json_encode(['success' => true, 'message' => 'Pasūtījums atzīmēts']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nevar atjaunināt pasūtījumu']);
    }
    exit();
}

// Visu datu dzēšana
if ($action === 'clear_all' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (file_exists($orders_file)) {
        file_put_contents($orders_file, json_encode([]));
    }
    echo json_encode(['success' => true, 'message' => 'Dati notīrīti']);
    exit();
}

// Noklusējuma atbilde
echo json_encode(['status' => 'ok', 'message' => 'API darbojas']);
?>