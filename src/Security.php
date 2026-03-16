<?php
/**
 * DROŠĪBAS KLASE
 * CSRF aizsardzība, Rate Limiting, utt.
 */

class Security {
    
    /**
     * Generiē CSRF token
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Iegūst CSRF token
     */
    public static function getCSRFToken() {
        return $_SESSION['csrf_token'] ?? null;
    }
    
    /**
     * Pārbaudi CSRF token
     */
    public static function verifyCSRFToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Rate Limiting - pārbauda pieprasījumu skaitu
     * Izmanto $_SESSION vai file-based storage
     * @param string $identifier - IP vai user_id
     * @param int $limit - Maksimālie pieprasījumi
     * @param int $window - Laika logs sekundēs
     */
    public static function checkRateLimit($identifier, $limit = 100, $window = 60) {
        $log_dir = __DIR__ . '/../logs';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $rate_file = $log_dir . '/rate_limit_' . md5($identifier) . '.json';
        $now = time();
        
        $data = [];
        if (file_exists($rate_file)) {
            $data = json_decode(file_get_contents($rate_file), true) ?: [];
        }
        
        // Notīri vecas pieprasījumus
        $data['requests'] = array_filter(
            $data['requests'] ?? [],
            function($timestamp) use ($now, $window) {
                return ($now - $timestamp) < $window;
            }
        );
        
        // Pārbaudi ierobežojuma
        if (count($data['requests']) >= $limit) {
            return false;
        }
        
        // Pievieno jaunu pieprasījumu
        $data['requests'][] = $now;
        file_put_contents($rate_file, json_encode($data));
        
        return true;
    }
    
    /**
     * Šifrē datus
     */
    public static function encrypt($data, $key = null) {
        $key = $key ?? hash('sha256', SECRET_KEY ?? 'default_key', true);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Dešifrē datus
     */
    public static function decrypt($data, $key = null) {
        $key = $key ?? hash('sha256', SECRET_KEY ?? 'default_key', true);
        $data = base64_decode($data);
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }
    
    /**
     * Šifrē paroli (bcrypt)
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Pārbaudi paroli
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Generiē TLS token (2FA)
     */
    public static function generate2FAToken() {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Pārbaudi 2FA token ar laika loga
     */
    public static function verify2FAToken($token, $stored_token, $max_age = 600) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $token_time = $_SESSION['token_time'] ?? 0;
        $current_time = time();
        
        if (($current_time - $token_time) > $max_age) {
            return false;
        }
        
        return hash_equals($token, $stored_token);
    }
    
    /**
     * Iegūst IP adresi (ņem vērā proxies)
     */
    public static function getClientIP() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'UNKNOWN';
    }
    
    /**
     * Bloķē IP adresi
     */
    public static function blockIP($ip_address, $reason = 'Suspicious activity') {
        $blacklist_file = __DIR__ . '/../logs/ip_blacklist.txt';
        $log_dir = __DIR__ . '/../logs';
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_entry = date('Y-m-d H:i:s') . " - Blocked IP: $ip_address - Reason: $reason\n";
        file_put_contents($blacklist_file, $log_entry, FILE_APPEND);
        
        log_security("IP Blocked: $ip_address", ['reason' => $reason]);
    }
    
    /**
     * Pārbaudi vai IP ir bloķēts
     */
    public static function isIPBlocked($ip_address) {
        $blacklist_file = __DIR__ . '/../logs/ip_blacklist.txt';
        
        if (!file_exists($blacklist_file)) {
            return false;
        }
        
        $blocked_ips = file($blacklist_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($blocked_ips as $line) {
            if (strpos($line, $ip_address) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validē URL
     */
    public static function isValidURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Sanitizē HTML
     */
    public static function sanitizeHTML($html) {
        return htmlspecialchars($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Stripu HTML tagai
     */
    public static function stripHTML($html) {
        return strip_tags($html);
    }
    
    /**
     * Pārbaudi vai pieprasījums ir AJAX
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Pārbaudi Content-Type
     */
    public static function validateContentType($expected = 'application/json') {
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($content_type, $expected) !== false;
    }
}
?>
