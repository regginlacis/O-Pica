<?php
/**
 * API VALIDĀCIJAS KLASE
 * Validē visus API input datus
 */

class Validator {
    private static $errors = [];
    private static $validated_data = [];
    
    /**
     * Sāc validāciju ar jaunu datu kopu
     */
    public static function start() {
        self::$errors = [];
        self::$validated_data = [];
        return new self();
    }
    
    /**
     * Validē nepieciešamo lauku
     */
    public static function required($field, $data, $message = null) {
        if (empty($data[$field]) && $data[$field] !== 0 && $data[$field] !== '0') {
            $msg = $message ?? "Lauks '{$field}' ir obligāts";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = trim($data[$field]);
        return true;
    }
    
    /**
     * Validē email
     */
    public static function email($field, $data, $message = null) {
        if (empty($data[$field])) return true;
        
        if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $msg = $message ?? "Lauks '{$field}' nav derīgs email";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = $data[$field];
        return true;
    }
    
    /**
     * Validē ciparus
     */
    public static function numeric($field, $data, $message = null) {
        if (empty($data[$field])) return true;
        
        if (!is_numeric($data[$field])) {
            $msg = $message ?? "Lauks '{$field}' jābūt ciparam";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = (float) $data[$field];
        return true;
    }
    
    /**
     * Validē integer
     */
    public static function integer($field, $data, $message = null) {
        if (empty($data[$field]) && $data[$field] !== 0) return true;
        
        if (!is_int($data[$field]) && !ctype_digit((string)$data[$field])) {
            $msg = $message ?? "Lauks '{$field}' jābūt veselam skaitlim";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = (int) $data[$field];
        return true;
    }
    
    /**
     * Validē string garuma diapazonu
     */
    public static function string($field, $data, $min = null, $max = null, $message = null) {
        if (empty($data[$field])) return true;
        
        $value = $data[$field];
        $length = strlen($value);
        
        if ($min && $length < $min) {
            $msg = $message ?? "Lauks '{$field}' jābūt vismaz {$min} rakstzīmēm";
            self::addError($field, $msg);
            return false;
        }
        
        if ($max && $length > $max) {
            $msg = $message ?? "Lauks '{$field}' var būt maksimāli {$max} rakstzīmēm";
            self::addError($field, $msg);
            return false;
        }
        
        self::$validated_data[$field] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return true;
    }
    
    /**
     * Validē minimum vērtību
     */
    public static function min($field, $data, $min_value, $message = null) {
        if (empty($data[$field]) && $data[$field] !== 0) return true;
        
        if ((float)$data[$field] < $min_value) {
            $msg = $message ?? "Lauks '{$field}' jābūt vismaz {$min_value}";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = $data[$field];
        return true;
    }
    
    /**
     * Validē maksimālo vērtību
     */
    public static function max($field, $data, $max_value, $message = null) {
        if (empty($data[$field]) && $data[$field] !== 0) return true;
        
        if ((float)$data[$field] > $max_value) {
            $msg = $message ?? "Lauks '{$field}' var būt maksimāli {$max_value}";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = $data[$field];
        return true;
    }
    
    /**
     * Validē izvēli no masīva
     */
    public static function in($field, $data, $allowed = [], $message = null) {
        if (empty($data[$field])) return true;
        
        if (!in_array($data[$field], $allowed, true)) {
            $msg = $message ?? "Lauks '{$field}' satur neļautu vērtību";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = $data[$field];
        return true;
    }
    
    /**
     * Validē regex shēmu
     */
    public static function regex($field, $data, $pattern, $message = null) {
        if (empty($data[$field])) return true;
        
        if (!preg_match($pattern, $data[$field])) {
            $msg = $message ?? "Lauks '{$field}' satur nevalīdu formātu";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = $data[$field];
        return true;
    }
    
    /**
     * Validē masīvu
     */
    public static function array_required($field, $data, $message = null) {
        if (empty($data[$field]) || !is_array($data[$field])) {
            $msg = $message ?? "Lauks '{$field}' jābūt nepieciešamam masīvam";
            self::addError($field, $msg);
            return false;
        }
        self::$validated_data[$field] = $data[$field];
        return true;
    }
    
    /**
     * Pēc validācijas, pārbaudi vai ir kļūdas
     */
    public static function validate() {
        return count(self::$errors) === 0;
    }
    
    /**
     * Pievieno kļūdu
     */
    private static function addError($field, $message) {
        self::$errors[$field] = $message;
    }
    
    /**
     * Iegūst visas kļūdas
     */
    public static function errors() {
        return self::$errors;
    }
    
    /**
     * Iegūst pirmā kļūdu
     */
    public static function first_error() {
        return reset(self::$errors) ?: null;
    }
    
    /**
     * Iegūst validētos datus
     */
    public static function validated() {
        return self::$validated_data;
    }
    
    /**
     * Iegūst konkrētu validēto lauku
     */
    public static function get($field) {
        return self::$validated_data[$field] ?? null;
    }
    
    /**
     * Pārinicializē validātoru
     */
    public static function reset() {
        self::$errors = [];
        self::$validated_data = [];
    }
}
?>
