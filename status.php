<?php
require_once 'theme_init.php';
session_start();
?>
<!DOCTYPE html>
<html lang="lv" data-theme="<?php echo isset($_SESSION['theme']) ? htmlspecialchars($_SESSION['theme']) : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O! Pica - Sistēmas Stāvoklis</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .status-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .status-check {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #E8360F;
        }
        
        .status-green {
            border-left-color: #4CAF50;
        }
        
        .status-red {
            border-left-color: #f44336;
        }
        
        .status-orange {
            border-left-color: #ff9800;
        }
        
        .quick-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .quick-links a {
            display: inline-block;
            padding: 10px 15px;
            background: #E8360F;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .quick-links a:hover {
            background: #ff6b4a;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="light-theme">
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>O! Pica</h1>
                <p>Sistēmas Stāvoklis</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button class="theme-toggle" onclick="setTheme('light')" title="Gaisma tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">☀️</button>
                <button class="theme-toggle" onclick="setTheme('dark')" title="Tumšā tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🌙</button>
            </div>
        </div>
    </header>
    
    <div class="status-container">
        <div class="status-check <?php echo isset($_SESSION['user_id']) ? 'status-green' : 'status-orange'; ?>">
            <h3>Pārbaudīt 1: Mājaslapa</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p style="color:green;">PIEĶEMS? - Lietotājs ir pierakstīts</p>
                <ul>
                    <li>Lietotāja ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></li>
                    <li>E-pasts: <?php echo htmlspecialchars($_SESSION['email']); ?></li>
                    <li>Lietotājvārds: <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                </ul>
            <?php else: ?>
                <p style="color:orange;">Lietotājs NAV pierakstīts. <a href="login.php">Pierakstieties šeit</a></p>
            <?php endif; ?>
        </div>
        
        <div class="status-check">
            <h3>Pārbaudīt 2: Datubāzes Savienojums</h3>
            <?php
            try {
                require_once 'config.php';
                echo "<p style='color:green;'>PIEĶEMS? - Datubāze veiksmīgi savienota</p>";
                
                // Check tables
                echo "<h4>Tabulu Stāvoklis:</h4>";
                $tables = ['users', 'orders', 'pizzas', 'order_items'];
                foreach ($tables as $table) {
                    $check = $conn->query("SHOW TABLES LIKE '$table'");
                    $exists = ($check && $check->num_rows > 0) ? 'PIEĶEMS?' : 'KļūDA';
                    $color = ($check && $check->num_rows > 0) ? 'green' : 'red';
                    echo "<p style='color:$color;'>$exists - $table tabula</p>";
                }
                
                // Check users count
                echo "<h4>Lietotāji Datubāzē:</h4>";
                $result = $conn->query("SELECT COUNT(*) as count FROM users");
                if ($result) {
                    $row = $result->fetch_assoc();
                    echo "<p>Kopējais lietotāju skaits: <strong>" . $row['count'] . "</strong></p>";
                    
                    if ($row['count'] > 0) {
                        echo "<h5>Regēstrainātie lietotāji:</h5>";
                        $users = $conn->query("SELECT user_id, username, email FROM users LIMIT 10");
                        echo "<ul>";
                        while ($user = $users->fetch_assoc()) {
                            echo "<li>" . htmlspecialchars($user['username']) . " (" . htmlspecialchars($user['email']) . ")</li>";
                        }
                        echo "</ul>";
                    }
                }
            } catch (Exception $e) {
                echo "<p style='color:red;'>KļūDA - Datubāzes savienojuma kļūda:</p>";
                echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            }
            ?>
        </div>
        
        <div style="margin-top: 30px;">
            <h3>Jātrie Darbības</h3>
            <div class="quick-links">
                <a href="login.php">Pierakstīties</a>
                <a href="register.php">Reģistrēties</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Mans Profils</a>
                    <a href="logout.php">Iziet</a>
                <?php endif; ?>
                <a href="index.php">Galvenais</a>
            </div>
        </div>
    </div>
    
    <footer style="text-align: center; margin-top: 80px; color: #666; padding: 20px;">
        <p>&copy; 2026 O! Pica. Visas tiesības rezervētas.</p>
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
