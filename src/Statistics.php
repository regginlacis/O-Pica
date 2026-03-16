<?php
/**
 * ADMIN DASHBOARD STATISTIKA
 * Datos par pasūtījumiem, ienākumiem, populārākajām picām
 */

class Statistics {
    private static $conn = null;
    
    public function __construct($database_connection = null) {
        self::$conn = $database_connection;
    }
    
    /**
     * Iegūst pasūtījuma skaitu par norādīto periodu
     */
    public static function getOrderCount($period = 'today', $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return 0;
        
        $where = self::getDateWhere($period);
        $query = "SELECT COUNT(*) as count FROM orders WHERE $where";
        
        $result = $connect->query($query);
        return $result ? $result->fetch_assoc()['count'] : 0;
    }
    
    /**
     * Iegūst kopējos ieņēmumus par norādīto periodu
     */
    public static function getTotalRevenue($period = 'today', $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return 0;
        
        $where = self::getDateWhere($period);
        $query = "SELECT SUM(total_price) as total FROM orders WHERE $where AND status != 'cancelled'";
        
        $result = $connect->query($query);
        $data = $result ? $result->fetch_assoc() : null;
        return $data['total'] ? round($data['total'], 2) : 0;
    }
    
    /**
     * Iegūst vidējo pasūtījuma vērtību
     */
    public static function getAverageOrderValue($period = 'today', $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return 0;
        
        $where = self::getDateWhere($period);
        $query = "SELECT AVG(total_price) as average FROM orders WHERE $where AND status != 'cancelled'";
        
        $result = $connect->query($query);
        $data = $result ? $result->fetch_assoc() : null;
        return $data['average'] ? round($data['average'], 2) : 0;
    }
    
    /**
     * Iegūst populārākos picas
     */
    public static function getTopPizzas($limit = 5, $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return [];
        
        $query = "
            SELECT p.pizza_id, p.name, p.price, COUNT(oi.order_item_id) as order_count, 
                   SUM(oi.quantity) as total_quantity, SUM(oi.subtotal) as revenue
            FROM pizzas p
            LEFT JOIN order_items oi ON p.pizza_id = oi.pizza_id
            LEFT JOIN orders o ON oi.order_id = o.order_id
            WHERE o.status != 'cancelled' OR o.order_id IS NULL
            GROUP BY p.pizza_id
            ORDER BY total_quantity DESC
            LIMIT ?
        ";
        
        $stmt = $connect->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $pizzas = [];
        
        while ($row = $result->fetch_assoc()) {
            $pizzas[] = $row;
        }
        
        return $pizzas;
    }
    
    /**
     * Iegūst pasūtījuma statusu sadalījumu
     */
    public static function getOrdersByStatus($period = 'month', $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return [];
        
        $where = self::getDateWhere($period);
        $query = "
            SELECT status, COUNT(*) as count
            FROM orders
            WHERE $where
            GROUP BY status
        ";
        
        $result = $connect->query($query);
        $statuses = [];
        
        while ($row = $result->fetch_assoc()) {
            $statuses[$row['status']] = $row['count'];
        }
        
        return $statuses;
    }
    
    /**
     * Iegūst maksāšanas metodes statistiku
     */
    public static function getPaymentMethodStats($period = 'month', $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return [];
        
        $where = self::getDateWhere($period);
        $query = "
            SELECT pm.method_name, COUNT(o.order_id) as count, SUM(o.total_price) as revenue
            FROM orders o
            LEFT JOIN payment_methods pm ON o.payment_method_id = pm.payment_method_id
            WHERE $where AND o.status != 'cancelled'
            GROUP BY o.payment_method_id
        ";
        
        $result = $connect->query($query);
        $methods = [];
        
        while ($row = $result->fetch_assoc()) {
            $methods[] = $row;
        }
        
        return $methods;
    }
    
    /**
     * Iegūst piegādes metodes statistiku
     */
    public static function getDeliveryMethodStats($period = 'month', $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return [];
        
        $where = self::getDateWhere($period);
        $query = "
            SELECT dm.method_name, COUNT(o.order_id) as count, SUM(o.total_price) as revenue
            FROM orders o
            LEFT JOIN delivery_methods dm ON o.delivery_method_id = dm.delivery_method_id
            WHERE $where AND o.status != 'cancelled'
            GROUP BY o.delivery_method_id
        ";
        
        $result = $connect->query($query);
        $methods = [];
        
        while ($row = $result->fetch_assoc()) {
            $methods[] = $row;
        }
        
        return $methods;
    }
    
    /**
     * Iegūst kavēja pasūtījuma informāciju
     */
    public static function getRecentOrders($limit = 10, $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return [];
        
        $query = "
            SELECT o.order_id, o.total_price, o.status, o.order_date, pm.method_name as payment_method,
                   dm.method_name as delivery_method, COUNT(oi.order_item_id) as items_count
            FROM orders o
            LEFT JOIN payment_methods pm ON o.payment_method_id = pm.payment_method_id
            LEFT JOIN delivery_methods dm ON o.delivery_method_id = dm.delivery_method_id
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            GROUP BY o.order_id
            ORDER BY o.order_date DESC
            LIMIT ?
        ";
        
        $stmt = $connect->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $orders = [];
        
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Iegūst atsauksmju skaitu un vidējo reitingu
     */
    public static function getReviewStats($conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return [];
        
        $query = "
            SELECT COUNT(*) as total_reviews, AVG(rating) as average_rating,
                   SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive_reviews,
                   SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative_reviews
            FROM reviews WHERE is_approved = TRUE
        ";
        
        $result = $connect->query($query);
        return $result ? $result->fetch_assoc() : [];
    }
    
    /**
     * Iegūst lietotāju skaitu
     */
    public static function getUserCount($period = 'all', $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return 0;
        
        $where = $period === 'all' ? '1=1' : self::getDateWhere($period, 'created_at');
        $query = "SELECT COUNT(*) as count FROM users WHERE $where AND role = 'user'";
        
        $result = $connect->query($query);
        return $result ? $result->fetch_assoc()['count'] : 0;
    }
    
    /**
     * Iegūst atsauksmes, kas gaida apstiprinājumu
     */
    public static function getPendingReviews($conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return [];
        
        $query = "
            SELECT r.review_id, r.rating, r.comment, p.name as pizza_name, r.created_at
            FROM reviews r
            LEFT JOIN pizzas p ON r.pizza_id = p.pizza_id
            WHERE r.is_approved = FALSE
            ORDER BY r.created_at DESC
        ";
        
        $result = $connect->query($query);
        $reviews = [];
        
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        
        return $reviews;
    }
    
    /**
     * Iegūst atbalsta pieprasījumus
     */
    public static function getSupportRequests($limit = 10, $conn = null) {
        $connect = $conn ?? self::$conn;
        
        if (!$connect) return [];
        
        $query = "
            SELECT support_id, name, email, message, status, created_at
            FROM support_requests
            WHERE status IN ('open', 'in_progress')
            ORDER BY created_at DESC
            LIMIT ?
        ";
        
        $stmt = $connect->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $requests = [];
        
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        
        return $requests;
    }
    
    /**
     * Palīgfunkcija, lai iegūtu WHERE klauzulū datumam
     */
    private static function getDateWhere($period = 'today', $date_field = 'order_date') {
        $today = date('Y-m-d');
        
        switch ($period) {
            case 'today':
                return "$date_field >= '$today 00:00:00' AND $date_field <= '$today 23:59:59'";
            case 'week':
                $week_ago = date('Y-m-d', strtotime('-7 days'));
                return "$date_field >= '$week_ago'";
            case 'month':
                $month_ago = date('Y-m-d', strtotime('-30 days'));
                return "$date_field >= '$month_ago'";
            case 'year':
                $year_ago = date('Y-m-d', strtotime('-365 days'));
                return "$date_field >= '$year_ago'";
            default:
                return "1=1";
        }
    }
}
?>
