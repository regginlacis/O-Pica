<?php
/**
 * REGISTER.PHP - Lietotāja Reģistrācijas Lapa
 */

require_once 'theme_init.php';
session_start();
require_once 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    
    // Validācija
    if (empty($username)) {
        $errors['username'] = 'Lietotājvārds ir obligāts';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Lietotājvārds jāsatur vismaz 3 rakstzīmes';
    }
    
    if (empty($email)) {
        $errors['email'] = 'E-pasts ir obligāts';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-pasts nav derīgs';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Parole ir obligāta';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Parole jāsatur vismaz 6 rakstzīmes';
    }
    
    if ($password !== $password_confirm) {
        $errors['password_confirm'] = 'Paroles neatbilst';
    }
    
    // Ja nav kļūdu, reģistrē
    if (empty($errors)) {
        $result = Auth::register($username, $email, $password, $conn);
        
        if ($result['success']) {
            $success = true;
        } else {
            $errors['general'] = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv" data-theme="<?php echo isset($_SESSION['theme']) ? htmlspecialchars($_SESSION['theme']) : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O! Pica - Reģistrācija</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-radius: 10px;
            border: 1px solid #333;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .auth-container h2 {
            color: #E8360F;
            margin-bottom: 30px;
            font-size: 1.8em;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #f5f5f5;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #E8360F;
            border-radius: 5px;
            background: #0f0f0f;
            color: white;
            font-size: 1em;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ff6b4a;
            box-shadow: 0 0 5px rgba(232, 54, 15, 0.5);
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        .success-message {
            background: #27ae60;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .general-error {
            background: #e74c3c;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #E8360F 0%, #ff6b4a 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
            transition: transform 0.2s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(232, 54, 15, 0.4);
        }
        
        .auth-links {
            margin-top: 20px;
            text-align: center;
            color: #999;
        }
        
        .auth-links a {
            color: #E8360F;
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #95a5a6;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            color: #E8360F;
        }
    </style>
</head>
<body style="background: #1a1a1a; color: #f5f5f5;">
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>O! Pica</h1>
                <p>Reģistrācija</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button class="theme-toggle" onclick="setTheme('light')" title="Gaisma tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">☀️</button>
                <button class="theme-toggle" onclick="setTheme('dark')" title="Tumšā tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🌙</button>
            </div>
        </div>
    </header>
    
    <div class="auth-container">
        <h2>Izveidot Kontu</h2>
        
        <?php if ($success): ?>
            <div class="success-message">
                OK Reģistrācija veiksmīga! <br>
                <a href="login.php" style="color: white; text-decoration: underline;">Pierakstieties šeit</a>
            </div>
        <?php elseif (!empty($errors)): ?>
            <?php if (isset($errors['general'])): ?>
                <div class="general-error">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Lietotājvārds:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                <?php if (isset($errors['username'])): ?>
                    <div class="error-message"><?php echo $errors['username']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">E-pasts:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="error-message"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Parole:</label>
                <input type="password" id="password" name="password" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="error-message"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Paroles Apstiprināšana:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="error-message"><?php echo $errors['password_confirm']; ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-register">Reģistrēties</button>
        </form>
        
        <div class="auth-links">
            Jau ir konts? <a href="login.php">Pierakstieties</a>
        </div>
        
        <a href="index.php" class="back-link"><- Atpakaļ uz sākumu</a>
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
    </script>
</body>
</html>
