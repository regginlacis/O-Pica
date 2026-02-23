<?php
// Admin panelis

session_start();

// Admin parole
define('ADMIN_PASSWORD', 'parole123');

// Login loģika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "Nepareiza parole!";
    }
}

// Pārbaudiet vai admin ir autentificēts
if (!isset($_SESSION['admin_logged_in'])) {
    // Parāda login formu
    ?>
    <!DOCTYPE html>
    <html lang="lv">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>O! Pica - Admin Pieeja</title>
        <link rel="stylesheet" href="style.css">
        <style>
            .login-container {
                max-width: 400px;
                margin: 100px auto;
                padding: 40px;
                background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
                border-radius: 10px;
                border: 1px solid #333;
                text-align: center;
            }
            .login-container h2 {
                color: #ff4757;
                margin-bottom: 30px;
                font-size: 1.8em;
            }
            .login-container input {
                width: 100%;
                padding: 12px;
                margin-bottom: 20px;
                border: 1px solid #ff4757;
                border-radius: 5px;
                background: #0f0f0f;
                color: white;
                font-size: 1em;
            }
            .login-container input::placeholder {
                color: #999;
            }
            .login-container button {
                width: 100%;
                padding: 12px;
                background: #ff4757;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-weight: 700;
                font-size: 1em;
                transition: all 0.3s ease;
            }
            .login-container button:hover {
                background: #ff6b7a;
            }
            .error {
                color: #ff4757;
                margin-bottom: 15px;
            }
            .back-link {
                color: #999;
                text-decoration: none;
                margin-top: 15px;
                display: inline-block;
            }
            .back-link:hover {
                color: #ff4757;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="container">
                <h1>🍕 O! Pica</h1>
                <p>Admin Pieeja</p>
            </div>
        </header>
        
        <div class="login-container">
            <h2>Admin Pieeja</h2>
            <?php if (isset($login_error)): ?>
                <p class="error">⚠️ <?php echo htmlspecialchars($login_error); ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Ievadiet paroli" required>
                <button type="submit">Ieiet</button>
            </form>
            <a href="index.php" class="back-link">← Atpakaļ uz sākumu</a>
        </div>

        <footer style="margin-top: 80px;">
            <p>&copy; 2026 O! Pica. Visas tiesības rezervētas. 🍕</p>
        </footer>
    </body>
    </html>
    <?php
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: index.php");
    exit();
}

// Pārbaudiet vai admin ir izsaukts
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Pasūtījumu fails
$orders_file = 'data/orders.json';

// Marķēt kā piegādātu un dzēst
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'deliver') {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
        
        if ($order_id) {
            // Ielādē pasūtījumus no JSON faila
            $orders = [];
            if (file_exists($orders_file)) {
                $orders = json_decode(file_get_contents($orders_file), true);
            }
            
            // Dzēš pasūtījumu
            $orders = array_filter($orders, function($o) use ($order_id) {
                return $o['id'] !== $order_id;
            });
            
            // Saglabā atpakaļ
            if (!is_dir('data')) {
                mkdir('data', 0755);
            }
            file_put_contents($orders_file, json_encode(array_values($orders)));
            
            $success_message = "Pasūtījums piegādāts un dzēsts no sistēmas!";
        }
    }
    
    // Pēc apstrādes, pārlādēt lapu
    header("Location: admin.php");
    exit();
}

// Notīrīt visu datubāzi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear') {
    if (!is_dir('data')) {
        mkdir('data', 0755);
    }
    file_put_contents($orders_file, json_encode([]));
    header("Location: admin.php");
    exit();
}

// Tīras GET notīrīšanas darbības atbalsts (no formas)
if ($action === 'clear' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!is_dir('data')) {
        mkdir('data', 0755);
    }
    file_put_contents($orders_file, json_encode([]));
    header("Location: admin.php");
    exit();
}

// Iegūt visus pasūtījumus no JSON faila
$orders = [];
if (file_exists($orders_file)) {
    $orders = json_decode(file_get_contents($orders_file), true);
    if (!is_array($orders)) {
        $orders = [];
    }
    // Sortē pēc timestamp descending
    usort($orders, function($a, $b) {
        return strtotime($b['timestamp'] ?? '2000-01-01') - strtotime($a['timestamp'] ?? '2000-01-01');
    });
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O! Pica - Admin Panelis</title>
    <link rel="stylesheet" href="style.css">
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
            color: #d63031;
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
            color: #d63031;
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
<body>
    <header>
        <div class="container">
            <h1>🍕 O! Pica</h1>
            <p>Admin Panelis</p>
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
            <?php if (!empty($orders)): ?>
                <form method="GET" style="display: inline;">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn-clear" onclick="return confirm('Vai esat pārliecināts? Šo nevar atsaukt!');">
                        🗑️ Notīrīt Visu
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
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
    
    <footer>
        <p>&copy; 2026 O! Pica Admin. Visas tiesības rezervētas. 🍕</p>
    </footer>
</body>
</html>
