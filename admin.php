<?php
session_start();

// Datubāzes savienojums
$conn = new mysqli('localhost', 'root', '', 'opica_db', 3306);
if ($conn->connect_error) {
    die('<div style="padding:20px;margin:20px;background:#f0f0f0;border:1px solid #ddd;">
        <h2>Database Error</h2>
        <p>Cannot connect to database: ' . htmlspecialchars($conn->connect_error) . '</p>
        <p><a href="setup_db.php">Click here to setup database</a></p>
    </div>');
}
$conn->set_charset("utf8mb4");

// Autentifikācija
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_POST['password'] ?? false) {
        if ($_POST['password'] === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin.php');
            exit;
        }
        $login_error = "Nepareiza parole";
    }
    
    // Pierakstīšanās forma
    ?>
    <!DOCTYPE html>
    <html lang="lv">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Administrācija - O! Pica</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; padding: 20px; background: #fff; }
            .login-box {
                max-width: 300px;
                margin: 100px auto;
                padding: 30px;
                background: #f9f9f9;
                border: 1px solid #ddd;
            }
            .login-box h2 { text-align: center; margin-bottom: 25px; font-size: 20px; }
            .login-box input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; font-size: 14px; }
            .login-box button { width: 100%; padding: 10px; background: #333; color: white; border: none; cursor: pointer; margin-top: 15px; font-size: 14px; }
            .login-box button:hover { background: #555; }
            .error { color: #d00; text-align: center; padding: 10px; margin-bottom: 15px; font-size: 13px; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>Administratora Pierakstīšanās</h2>
            <?php if (isset($login_error)) echo "<div class='error'>" . htmlspecialchars($login_error) . "</div>"; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Parole" required autofocus>
                <button type="submit">Pierakstīties</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// LOGOUT
if ($_GET['logout'] ?? false) {
    unset($_SESSION['admin_logged_in']);
    header('Location: index.php');
    exit;
}

// HANDLE ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Mark order delivered (delete it)
    if ($action === 'deliver') {
        $order_id = (int)($_POST['order_id'] ?? 0);
        if ($order_id > 0) {
            $conn->query("DELETE FROM orders WHERE order_id = $order_id");
            $conn->query("DELETE FROM order_items WHERE order_id = $order_id");
        }
        header('Location: admin.php');
        exit;
    }
    
    // Delete customer message
    if ($action === 'delete_message') {
        $msg_id = (int)($_POST['msg_id'] ?? 0);
        if ($msg_id > 0) {
            $conn->query("DELETE FROM support_requests WHERE id = $msg_id");
        }
        header('Location: admin.php');
        exit;
    }
}

// GET ORDERS
$orders = [];
$result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// GET MESSAGES
$messages = [];
$msg_result = $conn->query("SELECT * FROM support_requests ORDER BY timestamp DESC LIMIT 50");
if ($msg_result) {
    while ($row = $msg_result->fetch_assoc()) {
        $messages[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - O! Pica</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #fff; color: #333; }
        .admin-container { max-width: 900px; margin: 0 auto; }
        .admin-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .admin-top h1 { margin: 0; font-size: 24px; }
        .admin-top a { padding: 8px 15px; background: #333; color: white; text-decoration: none; border: none; cursor: pointer; font-size: 14px; }
        .admin-top a:hover { background: #555; }
        
        h2 { font-size: 18px; margin: 25px 0 15px 0; border-bottom: 1px solid #999; padding-bottom: 8px; }
        
        .order-card { background: #f9f9f9; padding: 15px; margin-bottom: 12px; border: 1px solid #ddd; }
        .order-header { display: grid; grid-template-columns: 100px 150px 100px; gap: 15px; margin-bottom: 15px; }
        .order-id { font-weight: bold; }
        .order-time { color: #666; }
        .order-price { font-weight: bold; }
        .order-items { background: #fff; padding: 10px; margin: 10px 0; border: 1px solid #eee; }
        .order-item { padding: 4px 0; font-size: 13px; border-bottom: 1px solid #f0f0f0; }
        .order-item:last-child { border-bottom: none; }
        .btn-deliver { background: #333; color: white; padding: 8px 15px; border: none; cursor: pointer; font-size: 13px; }
        .btn-deliver:hover { background: #555; }
        .btn-delete { background: #666; color: white; padding: 6px 12px; border: none; cursor: pointer; font-size: 12px; }
        .btn-delete:hover { background: #888; }
        .empty-msg { padding: 30px; text-align: center; color: #999; background: #f9f9f9; border: 1px solid #ddd; }
        
        .message-card { background: #f9f9f9; padding: 15px; margin-bottom: 12px; border: 1px solid #ddd; }
        .message-header { margin-bottom: 8px; }
        .message-name { font-weight: bold; font-size: 14px; }
        .message-email { color: #666; font-size: 12px; }
        .message-time { font-size: 12px; color: #999; margin: 5px 0; }
        .message-text { color: #333; margin: 10px 0; white-space: pre-wrap; line-height: 1.5; font-size: 13px; }
        .count-badge { background: #333; color: white; padding: 3px 8px; font-size: 12px; }
    </style>
</head>
<body>
<div class="admin-container">
    <div class="admin-top">
        <h1>Administrācija 🔐</h1>
        <a href="?logout=1">Iziet</a>
    </div>
    <!-- PASŪTĪJUMI -->
    <h2>Pasūtījumi <span class="count-badge"><?php echo count($orders); ?></span></h2>
    
    <?php if (empty($orders)): ?>
        <div class="empty-msg">Nav pasūtījumu</div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-id">#<?php echo $order['order_id']; ?></div>
                    <div class="order-time"><?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?></div>
                    <div class="order-price">€<?php echo number_format($order['total_price'], 2); ?></div>
                </div>
                
                <div class="order-items">
                    <?php
                    $items_result = $conn->query("SELECT p.name, oi.quantity, oi.unit_price FROM order_items oi JOIN pizzas p ON oi.pizza_id = p.pizza_id WHERE oi.order_id = {$order['order_id']}");
                    if ($items_result && $items_result->num_rows > 0) {
                        while ($item = $items_result->fetch_assoc()):
                        ?>
                            <div class="order-item"><?php echo htmlspecialchars($item['name']); ?> x<?php echo $item['quantity']; ?> = €<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></div>
                        <?php endwhile; 
                    }
                    ?>
                </div>
                
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="deliver">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <button type="submit" class="btn-deliver" onclick="return confirm('Dzēst šo pasūtījumu?')">Atzīmēt kā Piegādāts</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- KLIENTU ZIŅAS -->
    <h2>Klientu Ziņas 💬 <span class="count-badge"><?php echo count($messages); ?></span></h2>
    
    <?php if (empty($messages)): ?>
        <div class="empty-msg">Nav ziņu</div>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div class="message-card">
                <div class="message-header">
                    <div class="message-name"><?php echo htmlspecialchars($msg['name'] ?? 'Unknown'); ?></div>
                    <div class="message-email"><?php echo htmlspecialchars($msg['email'] ?? 'N/A'); ?></div>
                </div>
                
                <div class="message-time"><?php echo htmlspecialchars($msg['timestamp'] ?? 'N/A'); ?></div>
                
                <div class="message-text">
                    <?php echo htmlspecialchars($msg['message'] ?? ''); ?>
                </div>
                
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_message">
                    <input type="hidden" name="msg_id" value="<?php echo $msg['id']; ?>">
                    <button type="submit" class="btn-delete" onclick="return confirm('Dzēst šo ziņu?')">Dzēst</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
