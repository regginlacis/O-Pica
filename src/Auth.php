<?php
/**
 * USER AUTENTIFIKĀCIJAS KLASE
 * Reģistrācija, Login, Session pārvaldība
 */

class Auth {
    private static $conn = null;
    
    public function __construct($database_connection = null) {
        self::$conn = $database_connection;
    }
    
    /**
     * Reģistrē jaunu lietotāju
     */
    public static function register($username, $email, $password, $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) {
            return ['success' => false, 'message' => 'Datubāzes savienojuma kļūda'];
        }
        
        // Pārbaudi vai lietotājs jau eksistē
        $check_stmt = $connect->prepare("SELECT user_id FROM users WHERE email = ? OR username = ?");
        $check_stmt->bind_param('ss', $email, $username);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Lietotājs ar šo e-pastu vai lietotājvārdu jau eksistē'];
        }
        
        // Šifrē paroli
        $password_hash = Security::hashPassword($password);
        
        // Ievietojis jaunu lietotāju
        $insert_stmt = $connect->prepare(
            "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')"
        );
        $insert_stmt->bind_param('sss', $username, $email, $password_hash);
        
        if ($insert_stmt->execute()) {
            $user_id = $connect->insert_id;
            log_action('user_registered', $user_id, ['username' => $username, 'email' => $email]);
            return ['success' => true, 'message' => 'Reģistrācija veiksmīga', 'user_id' => $user_id];
        } else {
            log_error('User registration failed', ['username' => $username, 'error' => $insert_stmt->error]);
            return ['success' => false, 'message' => 'Reģistrācijas kļūda'];
        }
    }
    
    /**
     * Piesakās lietotāju
     */
    public static function login($email, $password, $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) {
            return ['success' => false, 'message' => 'Datubāzes savienojuma kļūda'];
        }
        
        // Atrod lietotāju
        $stmt = $connect->prepare("SELECT user_id, username, email, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            log_security('Failed login - user not found', ['email' => $email, 'ip' => Security::getClientIP()]);
            return ['success' => false, 'message' => 'Lietotājs nav atrasts'];
        }
        
        $user = $result->fetch_assoc();
        
        // Pārbaudi paroli
        if (!Security::verifyPassword($password, $user['password_hash'])) {
            log_security('Failed login - wrong password', ['email' => $email, 'ip' => Security::getClientIP()]);
            return ['success' => false, 'message' => 'Nepareiza parole'];
        }
        
        // Izveidojiet sesiju
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        log_action('user_login', $user['user_id'], ['email' => $email, 'ip' => Security::getClientIP()]);
        
        return ['success' => true, 'message' => 'Sekmīga pierakstīšanās', 'user' => $user];
    }
    
    /**
     * Izrakstās lietotāju
     */
    public static function logout() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $user_id = $_SESSION['user_id'] ?? null;
        log_action('user_logout', $user_id);
        
        session_destroy();
        return ['success' => true, 'message' => 'Sekmīga izrakstīšanās'];
    }
    
    /**
     * Pārbaudi vai lietotājs ir pierakstīts
     */
    public static function isLoggedIn() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Iegūst pašreizējā lietotāja ID
     */
    public static function getCurrentUserID() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Iegūst pašreizējā lietotāja informāciju
     */
    public static function getCurrentUser() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? 'user'
        ];
    }
    
    /**
     * Pārbaudi vai lietotājs ir admins
     */
    public static function isAdmin() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        return ($_SESSION['role'] ?? 'user') === 'admin';
    }
    
    /**
     * Atjauninājums lietotāja profilu
     */
    public static function updateProfile($user_id, $data, $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) {
            return ['success' => false, 'message' => 'Datubāzes savienojuma kļūda'];
        }
        
        $updates = [];
        $params = [];
        $types = '';
        
        if (!empty($data['username'])) {
            $updates[] = "username = ?";
            $params[] = $data['username'];
            $types .= 's';
        }
        
        if (!empty($data['email'])) {
            $updates[] = "email = ?";
            $params[] = $data['email'];
            $types .= 's';
        }
        
        if (!empty($data['theme'])) {
            $updates[] = "theme = ?";
            $params[] = $data['theme'];
            $types .= 's';
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'Nav datu atjaunināšanai'];
        }
        
        $params[] = $user_id;
        $types .= 'i';
        
        $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            log_action('profile_updated', $user_id, $data);
            return ['success' => true, 'message' => 'Profils atjaunināts'];
        } else {
            log_error('Profile update failed', ['user_id' => $user_id, 'error' => $stmt->error]);
            return ['success' => false, 'message' => 'Atjauninājuma kļūda'];
        }
    }
    
    /**
     * Mainīt paroli
     */
    public static function changePassword($user_id, $old_password, $new_password, $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) {
            return ['success' => false, 'message' => 'Datubāzes savienojuma kļūda'];
        }
        
        // Atrod lietotāju un pārbaudi veco paroli
        $stmt = $connect->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Lietotājs nav atrasts'];
        }
        
        $user = $result->fetch_assoc();
        
        if (!Security::verifyPassword($old_password, $user['password_hash'])) {
            log_security('Failed password change - wrong old password', ['user_id' => $user_id]);
            return ['success' => false, 'message' => 'Nepareiza pašreizējā parole'];
        }
        
        // Šifrē jauno paroli
        $new_password_hash = Security::hashPassword($new_password);
        
        // Atjauninājums paroli
        $update_stmt = $connect->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $update_stmt->bind_param('si', $new_password_hash, $user_id);
        
        if ($update_stmt->execute()) {
            log_action('password_changed', $user_id);
            return ['success' => true, 'message' => 'Parole mainīta'];
        } else {
            log_error('Password change failed', ['user_id' => $user_id, 'error' => $update_stmt->error]);
            return ['success' => false, 'message' => 'Paroles maiņas kļūda'];
        }
    }
    
    /**
     * Iegūst lietotāju pēc ID
     */
    public static function getUserByID($user_id, $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) {
            return null;
        }
        
        $stmt = $connect->prepare("SELECT user_id, username, email, role, theme, created_at FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
