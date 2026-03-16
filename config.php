<?php
// ================================================
// O! PICA KONFIGURĀCIJA
// ================================================

// ================================================
// KLASES AUTOLOADING
// ================================================
require_once __DIR__ . '/src/Validator.php';
require_once __DIR__ . '/src/Security.php';
require_once __DIR__ . '/src/Auth.php';
require_once __DIR__ . '/src/Statistics.php';
require_once __DIR__ . '/src/CSVExport.php';

// DATUBĀZES PARAMETRI
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'opica_db');
define('DB_PORT', 3306);

// SAVIENOJUMS AR DATU BĀZI
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Kļūdu pārbaude
if ($conn->connect_error) {
    error_log('Database Connection Error: ' . $conn->connect_error);
    // Don't die here - let the page handle it
    $db_error = 'Datubāzes savienojuma kļūda: ' . $conn->connect_error;
}

// UTF-8 kodējums
$conn->set_charset("utf8mb4");

// DROŠĪBAS PARAMETRI
define('ADMIN_PASSWORD', 'parole123');
define('SESSION_TIMEOUT', 3600); // 1 stunda
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 15 * 60); // 15 minūtes

// ZIŅOJUMU PARAMETRI
define('SUCCESS_MSG', 'Operācija veiksmīga');
define('ERROR_MSG', 'Kļūda darbības laikā');
define('NOT_FOUND_MSG', 'Resurss nav atrasts');
define('UNAUTHORIZED_MSG', 'Neautentificēts pieprasījums');

// PIEGĀDES PARAMETRI
define('FREE_DELIVERY_PIZZA_COUNT', 3);
define('DELIVERY_FREE_ABOVE_PRICE', 25.00);

// JSON RESPONSE HELPER FUNKCIJAS
function json_response($success, $message = '', $data = null, $status_code = 200) {
    http_response_code($status_code);
    $response = ['success' => $success];
    if ($message) $response['message'] = $message;
    if ($data) $response['data'] = $data;
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// ================================================
// ERROR LOGGING SISTĒMA
// ================================================
define('LOG_FILE', __DIR__ . '/logs/errors.log');
define('LOG_DIR', __DIR__ . '/logs');
define('ENABLE_LOGGING', true);

// Izveidot logs mapi, ja tā neeksistē
if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

/**
 * Pierakstīt kļūdu žurnālā
 * @param string $message - Kļūdas ziņojums
 * @param array $context - Papildinformācija
 * @param string $level - Kļūdas līmenis (ERROR, WARNING, INFO, DEBUG)
 */
function log_error($message, $context = [], $level = 'ERROR') {
    if (!ENABLE_LOGGING) return;
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $request_method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
    $request_uri = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN';
    
    $log_message = "[$timestamp] [$level] IP: $ip | $request_method $request_uri | Message: $message";
    
    if (!empty($context)) {
        $log_message .= " | Context: " . json_encode($context, JSON_UNESCAPED_UNICODE);
    }
    
    file_put_contents(LOG_FILE, $log_message . PHP_EOL, FILE_APPEND);
}

/**
 * Pierakstīt datubāzes kļūdu
 */
function log_db_error($error_message, $query = '') {
    log_error("Database Error: $error_message", ['query' => $query], 'ERROR');
}

/**
 * Pierakstīt API izsaukumu
 */
function log_api_call($action, $method, $success = true) {
    $level = $success ? 'INFO' : 'WARNING';
    log_error("API Call: action=$action method=$method", [], $level);
}

/**
 * Pierakstīt drošības notikumus
 */
function log_security($event, $details = []) {
    log_error("Security Event: $event", $details, 'WARNING');
}

/**
 * Pierakstīt lietotāja darbības
 */
function log_action($action, $user_id = null, $details = []) {
    $context = ['user_id' => $user_id];
    if (!empty($details)) {
        $context = array_merge($context, $details);
    }
    log_error("User Action: $action", $context, 'INFO');
}

// PHP kļūdu handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (error_reporting() == 0) return false;
    
    $error_types = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED'
    ];
    
    $error_type = $error_types[$errno] ?? 'UNKNOWN';
    log_error("PHP Error: $errstr", ['file' => $errfile, 'line' => $errline], $error_type);
    
    return true;
});

// Exception handler
set_exception_handler(function($exception) {
    log_error("Exception: " . $exception->getMessage(), 
        [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ], 
        'ERROR'
    );
    
    // Parādīt draudzīgu paziņojumu lietotājam
    http_response_code(500);
    json_response(false, 'Internal Server Error', null, 500);
});
