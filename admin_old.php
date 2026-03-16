<?php
require_once 'config.php';
require_once 'theme_init.php';
session_start();

// Simple password authentication
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_POST['password'] ?? false) {
        if ($_POST['password'] === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin.php');
            exit;
        }
        $login_error = "ERROR Invalid Password";
    }
    
    // Pierakstīšanās forma
    ?>
    <!DOCTYPE html>
    <html lang="lv">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Pieeja</title>
        <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
        <style>
            .login-box {
                max-width: 400px;
                margin: 80px auto;
                padding: 40px;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                border-top: 4px solid #E8360F;
            }
            .login-box h2 { color: #E8360F; text-align: center; margin-bottom: 30px; }
            .login-box input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
            .login-box button { width: 100%; padding: 12px; background: #E8360F; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 15px; }
            .login-box button:hover { background: #ff6b4a; }
            .error { color: #e74c3c; text-align: center; padding: 10px; background: #fadbd8; border-radius: 5px; margin-bottom: 15px; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>LOCK Admin</h2>
            <?php if (isset($login_error)) echo "<div class='error'>$login_error</div>"; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Parole" required autofocus>
                <button type="submit">Ienākt</button>
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

// GET ALL ORDERS
$orders = [];
$result = $conn->query("SELECT o.order_id, o.total_price, o.order_date FROM orders o ORDER BY o.order_date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items_result = $conn->query("SELECT p.name, oi.quantity, oi.unit_price FROM order_items oi JOIN pizzas p ON oi.pizza_id = p.pizza_id WHERE oi.order_id = {$row['order_id']}");
        $items = [];
        while ($item = $items_result->fetch_assoc()) {
            $items[] = $item['quantity'] . 'x ' . $item['name'];
        }
        $row['items'] = $items;
        $orders[] = $row;
    }
}

// GET ALL CUSTOMER MESSAGES
$messages = [];
$msg_result = $conn->query("SELECT id, name, email, message, timestamp FROM support_requests ORDER BY timestamp DESC LIMIT 50");
if ($msg_result) {
    while ($row = $msg_result->fetch_assoc()) {
        $messages[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="lv" data-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - O! Pica</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .admin-container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .admin-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .admin-top h1 { margin: 0; color: #E8360F; }
        .admin-top a { padding: 12px 25px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .admin-top a:hover { background: #c0392b; }
        
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #fff; padding: 20px; border-radius: 8px; border-left: 4px solid #E8360F; box-shadow: 0 2px 6px rgba(0,0,0,0.1); text-align: center; }
        .stat-box big { font-size: 2.5em; color: #E8360F; font-weight: bold; display: block; margin: 10px 0; }
        .stat-box small { color: #999; font-size: 0.9em; }
        
        .orders-list { margin-top: 30px; }
        .order-card { background: #fff; padding: 20px; margin-bottom: 15px; border-radius: 8px; border-left: 4px solid #3498db; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .order-header { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; margin-bottom: 15px; align-items: center; flex-wrap: wrap; }
        .order-header-item { font-size: 0.9em; }
        .order-id { font-weight: bold; color: #3498db; font-size: 1.1em; }
        .order-time { color: #999; }
        .order-items { background: #f8f9fa; padding: 12px; border-radius: 5px; margin-bottom: 15px; }
        .order-item { padding: 6px 0; font-size: 0.95em; border-bottom: 1px solid #e0e0e0; }
        .order-item:last-child { border-bottom: none; }
        .order-total { font-weight: bold; color: #E8360F; font-size: 1.2em; margin: 15px 0; }
        .btn-deliver { background: #27ae60; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1em; }
        .btn-deliver:hover { background: #229954; }
        .empty-msg { text-align: center; padding: 40px; color: #999; font-size: 1.1em; }
    </style>
</head>
<body>
<div class="admin-container">
    <div class="admin-top">
        <h1>ADMIN Administrācija</h1>
        <a href="?logout=1">← Izbeigt</a>
    </div>
    
    <!-- STATISTIKA -->
    <div class="stats-row">
        <div class="stat-box">
            <small>ORDERS Kopējie Pasūtījumi</small>
            <big><?php echo count($orders); ?></big>
        </div>
        <div class="stat-box">
            <small>REVENUE Kopējie Ieñēmumi</small>
            <big>€<?php 
                $total = 0;
                foreach ($orders as $o) $total += $o['total_price'];
                echo number_format($total, 2);
            ?></big>
        </div>
        <div class="stat-box">
            <small>STATS Viduējais Pasūtījums</small>
            <big>€<?php echo count($orders) > 0 ? number_format($total / count($orders), 2) : '0.00'; ?></big>
        </div>
    </div>
    
    <!-- PASŪTĪJUMI -->
    <h2 style="color: #E8360F; margin: 40px 0 20px 0;">ORDERS Pasūtījumi</h2>
    
    <?php if (empty($orders)): ?>
        <div class="empty-msg">EMPTY Nav pasūtījumu</div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-header-item order-id">#<?php echo $order['order_id']; ?></div>
                        <div class="order-header-item order-time">DATE <?php echo date('H:i d.m.Y', strtotime($order['order_date'])); ?></div>
                        <div class="order-header-item" style="font-weight: bold; color: #E8360F;"><?php echo number_format($order['total_price'], 2); ?>€</div>
                    </div>
                    
                    <div class="order-items">
                        <?php
                        $items_result = $conn->query("SELECT p.name, oi.quantity, oi.unit_price FROM order_items oi JOIN pizzas p ON oi.pizza_id = p.pizza_id WHERE oi.order_id = {$order['order_id']}");
                        while ($item = $items_result->fetch_assoc()):
                        ?>
                            <div class="order-item">PIZZA <?php echo $item['name']; ?> x<?php echo $item['quantity']; ?> = €<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></div>
                        <?php endwhile; ?>
                    </div>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="deliver">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        <button type="submit" class="btn-deliver" onclick="return confirm('Atzīmēt kā piegādātu un dzēst?')">OK Piegādāts</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- POPULĀRĀKĀS PICAS -->
    <h2 style="color: #E8360F; margin: 40px 0 20px 0;">PIZZA Populārākās Picas</h2>
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
        <?php
        $pizza_result = $conn->query("SELECT p.name, COUNT(oi.order_id) as orders, SUM(oi.quantity) as total_qty FROM order_items oi JOIN pizzas p ON oi.pizza_id = p.pizza_id GROUP BY p.pizza_id ORDER BY orders DESC LIMIT 5");
        if ($pizza_result && $pizza_result->num_rows > 0):
        ?>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 2px solid #ddd;">
                    <th style="text-align: left; padding: 12px; color: #E8360F;">Pica</th>
                    <th style="text-align: center; padding: 12px; color: #E8360F;">Pasūtījumi</th>
                    <th style="text-align: center; padding: 12px; color: #E8360F;">Kopā (gab.)</th>
                </tr>
                <?php while ($pizza = $pizza_result->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;">PIZZA <?php echo $pizza['name']; ?></td>
                        <td style="text-align: center; padding: 12px; color: #E8360F; font-weight: bold;"><?php echo $pizza['orders']; ?></td>
                        <td style="text-align: center; padding: 12px; color: #667eea; font-weight: bold;"><?php echo $pizza['total_qty']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #999;">Nav datu</p>
        <?php endif; ?>
    </div>
    
    <!-- KLIENTU ZI AS -->
    <h2 style="color: #E8360F; margin: 40px 0 20px 0;">MESSAGES Klientu Ziñas</h2>
    <div id="supportRequestsContainer">
        <p style="text-align: center; color: #999;">Nav ziņu</p>
    </div>
</div>

<script>
function loadSupportRequests() {
    const requests = JSON.parse(localStorage.getItem('supportRequests') || '[]');
    const container = document.getElementById('supportRequestsContainer');
    
    if (requests.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #999;">Nav ziņu</p>';
        return;
    }
    
    let html = '';
    requests.forEach(request => {
        html += `<div style="background: white; padding: 20px; margin-bottom: 15px; border-radius: 8px; border-left: 4px solid #E8360F; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
            <div style="margin-bottom: 10px;">
                <strong style="color: #333;">USER ${escapeHtml(request.name)}</strong> - EMAIL ${escapeHtml(request.email)}
            </div>
            <div style="color: #666; margin: 10px 0; white-space: pre-wrap;">${escapeHtml(request.message)}</div>
            <div style="font-size: 0.9em; color: #999; margin-bottom: 10px;">TIME ${request.timestamp || 'N/A'}</div>
            <button onclick="deleteRequest(${request.id})" style="background: #e74c3c; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">DELETE Dzēst</button>
        </div>`;
    });
    
    container.innerHTML = html;
}

function deleteRequest(id) {
    if (confirm('Dzēst šo ziņu?')) {
        const requests = JSON.parse(localStorage.getItem('supportRequests') || '[]');
        const filtered = requests.filter(r => r.id !== id);
        localStorage.setItem('supportRequests', JSON.stringify(filtered));
        loadSupportRequests();
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', loadSupportRequests);
</script>
</body>
</html>
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .admin-header h1 {
            color: #E8360F;
            margin-bottom: 10px;
        }
        
        .admin-controls {
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-refresh {
            padding: 12px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-refresh:hover {
            background-color: #2980b9;
        }
        
        .btn-clear {
            padding: 12px 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-clear:hover {
            background-color: #c0392b;
        }
        
        .success-message {
            background-color: #27ae60;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .orders-container {
            display: grid;
            gap: 20px;
        }
        
        .admin-order-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #3498db;
        }
        
        .admin-order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .order-id {
            font-weight: bold;
            color: #3498db;
            font-size: 1.1em;
        }
        
        .order-time {
            color: #666;
            font-size: 0.9em;
        }
        
        .order-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
            background-color: #f39c12;
            color: white;
        }
        
        .order-items-list {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .order-item {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-total {
            font-size: 1.2em;
            font-weight: bold;
            color: #E8360F;
            margin: 15px 0;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-deliver {
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-deliver:hover {
            background-color: #229954;
        }
        
        .empty-orders {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            color: #999;
        }
        
        .back-button {
            margin-bottom: 20px;
        }
        
        .back-button a {
            padding: 10px 20px;
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
        }
        
        .back-button a:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body class="light-theme">
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>🍕 O! Pica</h1>
                <p>Admin Panelis</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button class="theme-toggle" onclick="setTheme('light')" title="Gaisma tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">☀️</button>
                <button class="theme-toggle" onclick="setTheme('dark')" title="Tumšā tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🌙</button>
            </div>
        </div>
    </header>
    
    <div class="admin-container">
        <div class="back-button">
            <a href="index.php">← Atpakaļ uz sākumu</a>
            <a href="admin.php?logout=1" style="background-color: #e74c3c; margin-left: 10px;">Iziet</a>
        </div>
        
        <div class="admin-header">
            <h1>Admin Panelis</h1>
            <p>Pārvaldiet pasūtījumus un piegādes</p>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                ✓ <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="admin-controls">
            <a href="admin.php" class="btn-refresh">🔄 Pārlādēt</a>
            
            <!-- Statistikas Perioda Izvēle -->
            <select id="statisticsPeriod" class="period-select" style="padding: 12px 20px; border-radius: 5px; border: 1px solid #ddd; cursor: pointer;">
                <option value="today">📅 Šodien</option>
                <option value="week">📊 Šonedēļ</option>
                <option value="month" selected>📈 Šomēnesi</option>
            </select>
            
            <!-- CSV Eksporta Poga -->
            <a href="/opica/api_mysql.php?action=export_orders" class="btn-refresh" style="background-color: #27ae60;">📥 Eksportēt CSV</a>
            
            <?php if (!empty($orders)): ?>
                <form method="GET" style="display: inline;">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn-clear" onclick="return confirm('Vai esat pārliecināts? Šo nevar atsaukt!');">
                        🗑️ Notīrīt Visu
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- DASHBOARD SEKSIJA -->
        <div class="dashboard-section" id="dashboardSection" style="margin-bottom: 40px;">
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <!-- Pasūtījumi Widget -->
                <div class="stat-widget" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5em; margin-bottom: 10px;">📦</div>
                    <div style="font-size: 0.9em; opacity: 0.9;">Pasūtījumi</div>
                    <div style="font-size: 2.5em; font-weight: bold;" id="orderCount">-</div>
                </div>
                
                <!-- Ieņēmumi Widget -->
                <div class="stat-widget" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5em; margin-bottom: 10px;">💰</div>
                    <div style="font-size: 0.9em; opacity: 0.9;">Kopējie Ieņēmumi</div>
                    <div style="font-size: 2.5em; font-weight: bold;" id="totalRevenue">-</div>
                </div>
                
                <!-- Vidējis Pasūtījums Widget -->
                <div class="stat-widget" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5em; margin-bottom: 10px;">📊</div>
                    <div style="font-size: 0.9em; opacity: 0.9;">Vidējais Pasūtījums</div>
                    <div style="font-size: 2.5em; font-weight: bold;" id="averageOrder">-</div>
                </div>
                
                <!-- Atsauksmes Widget -->
                <div class="stat-widget" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5em; margin-bottom: 10px;">⭐</div>
                    <div style="font-size: 0.9em; opacity: 0.9;">Vidējais Reitings</div>
                    <div style="font-size: 2.5em; font-weight: bold;" id="avgRating">-</div>
                </div>
            </div>
            
            <!-- Grafiki -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <!-- Pasūtījumus pēc Statusa -->
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="color: #E8360F; margin-bottom: 15px;">📋 Pasūtījumi pēc Statusa</h3>
                    <canvas id="statusChart" style="max-height: 300px;"></canvas>
                </div>
                
                <!-- Maksāšanas Metodes -->
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="color: #E8360F; margin-bottom: 15px;">💳 Maksāšanas Metodes</h3>
                    <canvas id="paymentChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
            
            <!-- Top Picas Tabula -->
            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="color: #E8360F; margin-bottom: 15px;">🍕 Populārākās Picas</h3>
                <table id="topPizzasTable" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd;">
                            <th style="text-align: left; padding: 12px; color: #E8360F;">Pica</th>
                            <th style="text-align: center; padding: 12px; color: #E8360F;">Pasūtījumi</th>
                            <th style="text-align: center; padding: 12px; color: #E8360F;">Kopā (vienības)</th>
                        </tr>
                    </thead>
                    <tbody id="topPizzasBody">
                        <tr><td colspan="3" style="text-align: center; padding: 20px; color: #999;">Ielādē...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <h2 style="color: #E8360F; margin: 30px 0 20px 0;">📦 Pasūtījumi</h2>
        
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <p style="font-size: 48px; margin-bottom: 10px;">📦</p>
                <p>Nav neviena pasūtījuma</p>
            </div>
        <?php else: ?>
            <div class="orders-container">
                <p style="text-align: center; color: #666; margin-bottom: 20px;">
                    Kopā pasūtījumu: <strong><?php echo count($orders); ?></strong>
                </p>
                
                <?php foreach ($orders as $order): ?>
                    <div class="admin-order-card">
                        <div class="admin-order-header">
                            <span class="order-id">Pasūtījums #<?php echo htmlspecialchars($order['id']); ?></span>
                            <span class="order-time"><?php echo htmlspecialchars($order['timestamp']); ?></span>
                            <span class="order-status">⏳ Gaida</span>
                        </div>
                        
                        <div class="order-items-list">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item">
                                    <?php echo htmlspecialchars($item['emoji']); ?>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                    x<?php echo htmlspecialchars($item['quantity']); ?>
                                    - €<?php echo number_format($item['price'], 2); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-total">
                            Kopā: €<?php echo number_format($order['total'], 2); ?>
                        </div>
                        
                        <div class="order-payment" style="background: rgba(255, 71, 87, 0.1); color: #E8360F; padding: 10px; border-radius: 5px; margin: 15px 0; font-weight: 500; text-align: center;">
                            <?php 
                            $paymentMethod = isset($order['paymentMethod']) ? $order['paymentMethod'] : 'cash';
                            $paymentIcon = $paymentMethod === 'card' ? '💳' : '💵';
                            $paymentText = $paymentMethod === 'card' ? 'Maksāšana ar karti' : 'Maksāšana uz vietas';
                            echo $paymentIcon . ' ' . $paymentText;
                            ?>
                        </div>
                        
                        <div class="order-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                <input type="hidden" name="action" value="deliver">
                                <button type="submit" class="btn-deliver" onclick="return confirm('Atzīmēt šo pasūtījumu kā piegādātu?');">
                                    ✓ Atzīmēt Piegādātu
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Support Requests Sekcija -->
    <div class="admin-container" style="margin-top: 40px; border-top: 2px solid #ddd; padding-top: 30px;">

        <h2 style="color: #E8360F; margin-bottom: 20px;">�💬 Klientu Palīdzības Pieprasījumi</h2>
        
        <p id="noRequestsMessage" style="text-align: center; color: #999;">Vēl nav neviena palīdzības pieprasījuma</p>
        <div id="supportRequestsContainer"></div>
    </div>

    <!-- Chart.js biblioteka -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <script>
        let statusChart = null;
        let paymentChart = null;
        
        // Ielāda statistiku no API
        function loadDashboardStatistics() {
            const period = document.getElementById('statisticsPeriod')?.value || 'month';
            
            fetch(`/opica/api_mysql.php?action=get_statistics&period=${period}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.data;
                        
                        // Atjaunina widgetus
                        document.getElementById('orderCount').textContent = stats.order_count;
                        document.getElementById('totalRevenue').textContent = '€' + stats.total_revenue.toFixed(2);
                        document.getElementById('averageOrder').textContent = '€' + stats.average_order.toFixed(2);
                        document.getElementById('avgRating').textContent = (stats.reviews.average_rating || 'N/A') + ' ⭐';
                        
                        // Grafiki
                        drawStatusChart(stats.status_breakdown);
                        drawPaymentChart(stats.payment_methods);
                        drawTopPizzas(stats.top_pizzas);
                    }
                })
                .catch(error => console.error('Kļūda ielādējot statistiku:', error));
        }
        
        // Pasūtījumus pēc Statusa Grafiks
        function drawStatusChart(statusData) {
            const ctx = document.getElementById('statusChart')?.getContext('2d');
            if (!ctx) return;
            
            const labels = Object.keys(statusData);
            const data = Object.values(statusData);
            const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c'];
            
            if (statusChart) statusChart.destroy();
            
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels.map(l => {
                        const translations = {'pending': 'Gaidīts', 'confirmed': 'Apstiprinājums', 'preparing': 'Pagatavošana', 'on_way': 'Ceļā', 'delivered': 'Piegādāts', 'cancelled': 'Atcelts'};
                        return translations[l] || l;
                    }),
                    datasets: [{
                        data: data,
                        backgroundColor: colors.slice(0, labels.length)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        }
        
        // Maksāšanas Metodes Grafiks
        function drawPaymentChart(paymentData) {
            const ctx = document.getElementById('paymentChart')?.getContext('2d');
            if (!ctx) return;
            
            const labels = paymentData.map(p => p.method_name || 'N/A');
            const data = paymentData.map(p => p.count);
            
            if (paymentChart) paymentChart.destroy();
            
            paymentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pasūtījumi',
                        data: data,
                        backgroundColor: ['#4facfe', '#f5576c'],
                        borderColor: ['#00f2fe', '#E8360F'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            });
        }
        
        // Top Picas Tabula
        function drawTopPizzas(pizzas) {
            const tbody = document.getElementById('topPizzasBody');
            if (!tbody) return;
            
            if (!pizzas || pizzas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #999;">Nav datu</td></tr>';
                return;
            }
            
            let html = '';
            pizzas.forEach((pizza, idx) => {
                const bgColor = idx % 2 === 0 ? '#f9f9f9' : 'white';
                html += `<tr style="background: ${bgColor}; border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">🍕 ${pizza.name}</td>
                    <td style="text-align: center; padding: 12px; color: #E8360F; font-weight: bold;">${pizza.orders || 0}</td>
                    <td style="text-align: center; padding: 12px; color: #667eea; font-weight: bold;">${pizza.total_qty || 0}</td>
                </tr>`;
            });
            
            tbody.innerHTML = html;
        }
        
        // Perioda maiņa
        document.getElementById('statisticsPeriod')?.addEventListener('change', loadDashboardStatistics);
        
        // Ielāda statistiku palaižanās
        document.addEventListener('DOMContentLoaded', loadDashboardStatistics);
        
        // Lokālā datu ielāde
        
            const ordersContainer = document.getElementById('ordersContainer');
            const noMessage = document.getElementById('noLocalOrdersMessage');
            const debugInfo = document.getElementById('debugInfo');
            
            // Debugošana
            console.log('🔍 Meklējam localStorage atslēgas...');
            console.log('localStorage atslēgas:', Object.keys(localStorage));
            
            const storedRaw = localStorage.getItem('userOrders');
            console.log('📦 userOrders raw:', storedRaw);
            
            let storedOrders = [];
            try {
                storedOrders = storedRaw ? JSON.parse(storedRaw) : [];
            } catch(e) {
                console.error('❌ Parsēšanas kļūda:', e);
                if (debugInfo) debugInfo.textContent = 'Datu parsēšanas kļūda';
            }
            
            if (debugInfo) {
                debugInfo.textContent = 'Atrasts ' + storedOrders.length + ' pasūtījumu';
            }
            
            if (!Array.isArray(storedOrders) || storedOrders.length === 0) {
                console.log('⚠️ Nav neviena pasūtījuma');
                if (noMessage) noMessage.style.display = 'block';
                if (ordersContainer) ordersContainer.innerHTML = '';
                return;
            }
            
            if (noMessage) noMessage.style.display = 'none';
            
            let html = '<div class="orders-container">';
            
            storedOrders.forEach((order, idx) => {
                console.log('📋 Apstrādāju pasūtījumu ' + idx + ':', order);
                
                if (!order || !order.id) {
                    console.warn('⚠️ Nepilnīgs pasūtījums:', order);
                    return;
                }
                
                let itemsHtml = '';
                if (order.items && Array.isArray(order.items)) {
                    order.items.forEach(item => {
                        itemsHtml += '<div class="order-item">🍕 ' + escapeHtml(item.name) + ' x' + item.quantity + ' - €' + (item.price || 0).toFixed(2) + '</div>';
                    });
                }
                
                const paymentMethod = order.paymentMethod || 'cash';
                const paymentIcon = paymentMethod === 'card' ? '💳' : '💵';
                const paymentText = paymentMethod === 'card' ? 'Maksāšana ar karti' : 'Maksāšana uz vietas';
                const totalPrice = order.total ? order.total.toFixed(2) : '0.00';
                
                html += '<div class="admin-order-card" style="border-left: 5px solid #E8360F;">';
                html += '<div class="admin-order-header">';
                html += '<span class="order-id" style="color: #E8360F;">Pasūtījums #' + escapeHtml(order.id) + '</span>';
                html += '<span class="order-time">' + escapeHtml(order.timestamp || 'N/A') + '</span>';
                html += '<span class="order-status" style="background: #f39c12;">⏳ Gaida</span>';
                html += '</div>';
                html += '<div class="order-items-list">' + (itemsHtml || '<div class="order-item">Nav informācijas</div>') + '</div>';
                html += '<div class="order-total">Kopā: €' + totalPrice + '</div>';
                html += '<div class="order-payment" style="background: rgba(232, 54, 15, 0.1); color: #E8360F; padding: 10px; border-radius: 5px; margin: 15px 0; font-weight: 500; text-align: center;">' + paymentIcon + ' ' + paymentText + '</div>';
                html += '<button onclick="markLocalStorageOrderAsDelivered(\'' + escapeHtml(order.id) + '\')" class="btn-deliver" style="background: #27ae60;">✓ Pievienot pie Piegādātiem</button>';
                html += '</div>';
            });
            
            html += '</div>';
            if (ordersContainer) ordersContainer.innerHTML = html;
            console.log('✅ Ielādēti ' + storedOrders.length + ' pasūtījumi');
        }
        
        function markLocalStorageOrderAsDelivered(orderId) {
            if (confirm('Atzīmēt šo pasūtījumu kā piegādātu?')) {
                let orders = JSON.parse(localStorage.getItem('userOrders') || '[]');
                orders = orders.filter(o => o.id !== orderId);
                localStorage.setItem('userOrders', JSON.stringify(orders));
                loadLocalStorageOrders();
                alert('Pasūtījums atzīmēts kā piegādāts! ✓');
            }
        }
        
        // Ielāda support requests no localStorage un parāda admin panelī
        function loadSupportRequests() {
            const requests = JSON.parse(localStorage.getItem('supportRequests') || '[]');
            const container = document.getElementById('supportRequestsContainer');
            const noMessage = document.getElementById('noRequestsMessage');
            
            if (requests.length === 0) {
                noMessage.style.display = 'block';
                return;
            }
            
            noMessage.style.display = 'none';
            container.innerHTML = '';
            
            requests.forEach(request => {
                const requestDiv = document.createElement('div');
                requestDiv.style.cssText = 'background: #f5f5f5; padding: 20px; margin-bottom: 15px; border-radius: 8px; border-left: 4px solid #E8360F;';
                
                requestDiv.innerHTML = `
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #333;">👤 ${escapeHtml(request.name)}</strong>
                        <span style="color: #999; font-size: 0.9em;"> - ${request.timestamp}</span>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #333;">📧 E-pasts:</strong> <a href="mailto:${escapeHtml(request.email)}">${escapeHtml(request.email)}</a>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #333;">💬 Ziņojums:</strong>
                        <p style="color: #666; margin: 5px 0; white-space: pre-wrap;">${escapeHtml(request.message)}</p>
                    </div>
                    <button onclick="deleteRequest(${request.id})" style="background: #e74c3c; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">
                        🗑️ Dzēst
                    </button>
                `;
                
                container.appendChild(requestDiv);
            });
        }
        
        function deleteRequest(id) {
            if (confirm('Vai dzēst šo pieprasījumu?')) {
                const requests = JSON.parse(localStorage.getItem('supportRequests') || '[]');
                const filtered = requests.filter(r => r.id !== id);
                localStorage.setItem('supportRequests', JSON.stringify(filtered));
                loadSupportRequests();
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Ielāda datus lapas ielādes laikā
        document.addEventListener('DOMContentLoaded', function() {
            loadLocalStorageOrders();
            loadSupportRequests();
        });
    </script>
    
    <footer>
        <p>&copy; 2026 O! Pica Admin. Visas tiesības rezervētas. 🍕</p>
    </footer>
    
    <script>
        function setTheme(theme) {
            document.body.classList.remove('light-theme', 'dark-theme');
            document.body.classList.add(theme + '-theme');
            localStorage.setItem('theme', theme);
            fetch('set_theme.php?theme=' + theme).catch(e => {});
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.key === 't') {
                e.preventDefault();
                const current = localStorage.getItem('theme') || 'light';
                setTheme(current === 'light' ? 'dark' : 'light');
            }
        });
    </script>
</body>
</html>

