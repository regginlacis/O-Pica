<?php
/**
 * EDIT_PROFILE.PHP - Profila Rediģēšana
 */

require_once 'theme_init.php';
session_start();
require_once 'config.php';

// Pārbaudi vai ir pierakstīts
if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = Auth::getCurrentUser();
$user_id = $user['user_id'];

$error = '';
$success = '';

// Iegūst pašreizējos datus
$stmt = $conn->prepare("SELECT username, email, theme FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Apstrādā profila atjauninājumus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $theme = $_POST['theme'] ?? 'light';
    
    // Validācija
    if (empty($username) || strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Pārbaudi vai e-pasts jau ir izmantots
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $check_stmt->bind_param('si', $email, $user_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = 'This email is already registered';
        } else {
            // Atjaunini profilu
            $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, theme = ? WHERE user_id = ?");
            $update_stmt->bind_param('sssi', $username, $email, $theme, $user_id);
            
            if ($update_stmt->execute()) {
                $success = 'Profile updated successfully!';
                $user_data['username'] = $username;
                $user_data['email'] = $email;
                $user_data['theme'] = $theme;
                
                // Žurnālē profila atjauninājumu
                log_action('profile_updated', $user_id, ['new_username' => $username, 'new_email' => $email, 'new_theme' => $theme]);
                
                // Atjaunini sesiju
                $_SESSION['user_email'] = $email;
                $_SESSION['user_id'] = $user_id;
            } else {
                $error = 'Error updating profile';
            }
        }
    }
}

// Apstrādā paroles maiņu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validācija
    if (empty($old_password)) {
        $error = 'Please enter current password';
    } elseif (empty($new_password) || strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Pārbaudi veco paroli
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_pwd = $result->fetch_assoc();
        
        if (!password_verify($old_password, $user_pwd['password'])) {
            $error = 'Current password is incorrect';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $update_stmt->bind_param('si', $hashed_password, $user_id);
            
            if ($update_stmt->execute()) {
                $success = 'OK Parole ir veiksmīgi nomainīta!';
                log_action('password_changed', $user_id, ['changed_at' => date('Y-m-d H:i:s')]);
            } else {
                $error = 'ERROR Kļūda mainot paroli';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv" data-theme="<?php echo isset($_SESSION['theme']) ? htmlspecialchars($_SESSION['theme']) : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT Rediģēt Profilu - O! Pica</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .edit-profile-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .edit-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .edit-form h3 {
            color: #E8360F;
            border-bottom: 2px solid #E8360F;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #E8360F;
            box-shadow: 0 0 5px rgba(232, 54, 15, 0.3);
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .btn-save {
            padding: 12px 25px;
            background: linear-gradient(135deg, #E8360F 0%, #ff6b4a 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(232, 54, 15, 0.3);
        }
        
        .btn-cancel {
            padding: 12px 25px;
            background: #ccc;
            color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
        }
        
        .btn-cancel:hover {
            background: #999;
            color: white;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
<body class="light-theme">
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>O! Pica</h1>
                <p>Rediģēt Profilu</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button class="theme-toggle" onclick="setTheme('light')" title="Gaiša tēma" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">LIGHT</button>
                <button class="theme-toggle" onclick="setTheme('dark')" title="Tumšā tēma" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">DARK</button>
            </div>
        </div>
    </header>
    
    <div class="edit-profile-container">
        <div class="breadcrumb">
            <a href="profile.php"><- Atpakaļ uz profilu</a>
        </div>
        
        <!-- Kļūdu/Veiksmes Ziņojums -->
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Profila Informācijas Veidlapa -->
        <div class="edit-form">
            <h3>USER Profila Informācija</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Lietotājvārds</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($user_data['username']); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-pasta Adrese</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($user_data['email']); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="theme">THEME Tēma</label>
                    <select id="theme" name="theme">
                        <option value="light" <?php echo $user_data['theme'] === 'light' ? 'selected' : ''; ?>>LIGHT Gaišā</option>
                        <option value="dark" <?php echo $user_data['theme'] === 'dark' ? 'selected' : ''; ?>>DARK Tumšā</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <a href="profile.php" class="btn-cancel">Atcelt</a>
                    <button type="submit" name="update_profile" class="btn-save">SAVE Saglabāt Izmainās</button>
                </div>
            </form>
        </div>
        
        <!-- Paroles Maiņa -->
        <div class="edit-form">
            <h3>LOCK Nomainīt Paroli</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="old_password">Pašreizējā Parole</label>
                    <input type="password" id="old_password" name="old_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">Jaunā Parole</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small style="color: #666;">Minimums 6 simboli</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Apstipriniet Jauno Paroli</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-actions">
                    <a href="profile.php" class="btn-cancel">Atcelt</a>
                    <button type="submit" name="change_password" class="btn-save">LOCK Nomainīt Paroli</button>
                </div>
            </form>
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
