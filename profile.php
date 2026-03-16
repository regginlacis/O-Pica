<?php
/**
 * PROFILE.PHP - Lietotāja Profils
 */
session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Try to load additional data if database is available
$user_data = [
    'user_id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? 'Unknown',
    'email' => $_SESSION['email'] ?? 'unknown@example.com',
    'role' => $_SESSION['role'] ?? 'user',
    'login_time' => $_SESSION['login_time'] ?? time(),
    'theme' => $_SESSION['theme'] ?? 'light'
];

$orders = [];
$reviews = [];
$error = null;

// Try database queries if available
try {
    require_once 'config.php';
    
    // Get full user data
    $stmt = $conn->prepare("SELECT user_id, username, email, role, theme, created_at FROM users WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
        }
    }
    
    // Try to get orders if table exists
    if ($conn->query("SHOW TABLES LIKE 'orders'")->num_rows > 0) {
        $orders_stmt = $conn->prepare("SELECT order_id, total_price, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
        if ($orders_stmt) {
            $orders_stmt->bind_param('i', $_SESSION['user_id']);
            $orders_stmt->execute();
            $orders_result = $orders_stmt->get_result();
            while ($order = $orders_result->fetch_assoc()) {
                $orders[] = $order;
            }
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<?php require_once 'theme_init.php'; ?>
<!DOCTYPE html>
<html lang="lv" data-theme="<?php echo isset($_SESSION['theme']) ? htmlspecialchars($_SESSION['theme']) : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O! Pica - Mans Profils</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #E8360F;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-info h2 {
            color: #E8360F;
            margin: 0 0 10px 0;
            font-size: 1.8em;
        }
        
        .profile-info p {
            color: #666;
            margin: 5px 0;
            font-size: 0.95em;
        }
        
        .profile-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-profile {
            padding: 10px 20px;
            background: #E8360F;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-profile:hover {
            background: #ff6b4a;
            transform: translateY(-2px);
        }
        
        .section {
            margin: 30px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #E8360F;
        }
        
        .section h3 {
            color: #E8360F;
            margin-top: 0;
            font-size: 1.3em;
        }
        
        .empty-message {
            color: #999;
            font-style: italic;
        }
        
        .order-item {
            padding: 12px;
            background: #f5f5f5;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-status {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .error-box {
            background: #ffe0e0;
            border: 1px solid #ff4444;
            color: #d32f2f;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .success-box {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body style="background: #f5f5f5;">
    <header>
        <div class="container">
            <h1>O! Pica</h1>
            <p>Mans Profils</p>
        </div>
    </header>
    
    <div class="profile-container">
        <?php if ($error): ?>
            <div class="error-box">
                <strong>Datubāzes Savienojuma Kļūda:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <div class="success-box">
                Pierakstīšanās veiksmīga
            </div>
        <?php endif; ?>
        
        <div class="profile-header">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user_data['username'] ?? 'Lietotājs'); ?></h2>
                <p><strong>E-pasts:</strong> <?php echo htmlspecialchars($user_data['email'] ?? ''); ?></p>
                <p><strong>Loma:</strong> <?php echo ucfirst(htmlspecialchars($user_data['role'] ?? 'user')); ?></p>
                <?php if (isset($user_data['created_at'])): ?>
                    <p><strong>Reģistrēts:</strong> <?php echo date('d.m.Y H:i:s', strtotime($user_data['created_at'])); ?></p>
                <?php endif; ?>
            </div>
            <div class="profile-actions">
                <a href="edit_profile.php" class="btn-profile">Rediģēt Profilu</a>
                <a href="logout.php" class="btn-profile" style="background: #666;">Izlogošanās</a>
            </div>
        </div>
        
        <div class="section">
            <h3>Jūsu Pasūtījumi</h3>
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-item">
                        <div>
                            <strong>Pasūtījums #<?php echo htmlspecialchars($order['order_id']); ?></strong>
                            <div style="font-size: 0.9em; color: #999;">
                                <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 1.1em; color: #E8360F; font-weight: 600;">
                                €<?php echo number_format($order['total_price'], 2); ?>
                            </div>
                            <span class="order-status status-<?php echo htmlspecialchars($order['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-message">Jums vēl nav pasūtījumu.</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h3>Konta Iestatījumi</h3>
            <ul style="list-style: none; padding: 0;">
                <li><a href="edit_profile.php" style="color: #E8360F; text-decoration: none;">Rediģēt personīgo informāciju</a></li>
                <li><a href="change_password.php" style="color: #E8360F; text-decoration: none;">Mainīt paroli</a></li>
                <li><a href="logout.php" style="color: #E8360F; text-decoration: none;">Izlogošanās</a></li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" style="color: #E8360F; text-decoration: none; font-weight: 600;"><- Atgriezties uz galveno lapu</a>
        </div>
    </div>
    
    <footer style="margin-top: 50px; text-align: center; color: #999; padding: 20px;">
        <p>&copy; 2026 O! Pica. Visas tiesības rezervētas.</p>
    </footer>
    <script>
        function setTheme(theme) {
            document.body.classList.remove('light-theme', 'dark-theme');
            document.body.classList.add(theme + '-theme');
            localStorage.setItem('theme', theme);
            fetch('set_theme.php?theme=' + theme).catch(e => {});
        }
        
        // Initialize theme on page load
        window.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        });
    </script>
</body>
</html>
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mans Profils - O! Pica</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .profile-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .profile-header {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 40px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .profile-avatar {
            text-align: center;
        }
        
        .avatar-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E8360F 0%, #ff6b4a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            margin: 0 auto 20px;
            color: white;
        }
        
        .profile-info h2 {
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        
        .profile-field {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .profile-field label {
            font-weight: 600;
            color: #E8360F;
            min-width: 150px;
        }
        
        .profile-field value {
            color: #666;
        }
        
        .profile-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-edit {
            padding: 10px 20px;
            background: #E8360F;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit:hover {
            background: #ff6b4a;
        }
        
        .btn-logout {
            padding: 10px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-logout:hover {
            background: #c0392b;
        }
        
        .profile-section {
            margin-bottom: 30px;
        }
        
        .profile-section h3 {
            color: #E8360F;
            border-bottom: 2px solid #E8360F;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .orders-table th {
            background: linear-gradient(135deg, #E8360F 0%, #ff6b4a 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .orders-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .orders-table tbody tr:hover {
            background: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
        }
        
        .status-pending {
            background: #f39c12;
            color: white;
        }
        
        .status-confirmed {
            background: #3498db;
            color: white;
        }
        
        .status-preparing {
            background: #e67e22;
            color: white;
        }
        
        .status-on_way {
            background: #9b59b6;
            color: white;
        }
        
        .status-delivered {
            background: #27ae60;
            color: white;
        }
        
        .empty-message {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 10px;
            color: #999;
        }
        
        .review-card {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #E8360F;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .review-pizza {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .review-rating {
            color: #f39c12;
            font-size: 1.1em;
        }
        
        .review-comment {
            color: #666;
            font-style: italic;
        }
        
        .review-date {
            font-size: 0.9em;
            color: #999;
            margin-top: 10px;
        }
        
        .breadcrumb {
            margin-bottom: 20px;
        }
        
        .breadcrumb a {
            color: #E8360F;
            text-decoration: none;
            font-weight: 600;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>O! Pica</h1>
            <p>Mans Profils</p>
        </div>
    </header>
    
    <div class="profile-container">
        <div class="breadcrumb">
            <a href="index.php"><- Atpakaļ uz sākumu</a>
        </div>
        
        <!-- Profila Galvene -->
        <div class="profile-header">
            <div class="profile-avatar">
                <div class="avatar-circle">U</div>
                <p style="color: #999;">Profila Apbilde</p>
            </div>
            
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user_data['username']); ?></h2>
                
                <div class="profile-field">
                    <label>E-pasts:</label>
                    <value><?php echo htmlspecialchars($user_data['email']); ?></value>
                </div>
                
                <div class="profile-field">
                    <label>Loma:</label>
                    <value><?php echo htmlspecialchars($user_data['role'] === 'admin' ? 'Administrators' : 'Parastais Lietotājs'); ?></value>
                </div>
                
                <div class="profile-field">
                    <label>Reģistrācija:</label>
                    <value><?php echo date('d.m.Y', strtotime($user_data['created_at'])); ?></value>
                </div>
                
                <div class="profile-field">
                    <label>Tēma:</label>
                    <value><?php echo htmlspecialchars($user_data['theme'] === 'dark' ? 'Tumšā' : 'Gaišā'); ?></value>
                </div>
                
                <div class="profile-actions">
                    <a href="edit_profile.php" class="btn-edit">Rediģēt Profilu</a>
                    <button onclick="logout()" class="btn-logout">Iziet</button>
                </div>
            </div>
        </div>
        
        <!-- Pasūtījumi Sekcija -->
        <div class="profile-section">
            <h3>Mani Pasūtījumi</h3>
            <?php if (!empty($orders)): ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Pasūtījuma ID</th>
                            <th>Cena</th>
                            <th>Statuss</th>
                            <th>Datums</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td>€<?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php 
                                        $status_translations = [
                                            'pending' => 'Gaidīts',
                                            'confirmed' => 'OK Apstiprinājums',
                                            'preparing' => 'Pagatavošana',
                                            'on_way' => 'Ceļā',
                                            'delivered' => 'OK Piegādāts',
                                            'cancelled' => 'ERROR Atcelts'
                                        ];
                                        echo $status_translations[$order['status']] ?? $order['status'];
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-message">
                    Jums vēl nav neviena pasūtījuma
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Atsauksmes Sekcija -->
        <div class="profile-section">
            <h3>Manas Atsauksmes</h3>
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="review-pizza"><?php echo htmlspecialchars($review['pizza_name'] ?? 'Vispārējā'); ?></span>
                            <span class="review-rating"><?php echo str_repeat('*', $review['rating']); ?></span>
                        </div>
                        <div class="review-comment">
                            "<?php echo htmlspecialchars($review['comment']); ?>"
                        </div>
                        <div class="review-date">
                            <?php echo date('d.m.Y', strtotime($review['created_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-message">
                    Jūs vēl neesat sniedzis atsaukas
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer style="text-align: center; margin-top: 80px; color: #666; padding: 20px;">
        <p>&copy; 2026 O! Pica. Visas tiesības rezervētas.</p>
    </footer>
    
    <script>
        function logout() {
            if (confirm('Vai esat pārliecināts, ka vēlaties iziet?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
