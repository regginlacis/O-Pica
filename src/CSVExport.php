<?php
/**
 * CSV EKSPORTA KLASE
 * Eksportēj datus uz CSV formātu
 */

class CSVExport {
    
    /**
     * Eksportē pasūtījumus uz CSV
     */
    public static function exportOrders($orders, $filename = 'pasutijumi.csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Galvenes
        fputcsv($output, [
            'Pasūtījuma ID',
            'Kopējā Cena (EUR)',
            'Statuss',
            'Maksāšanas Metode',
            'Piegādes Metode',
            'Piegādes Adrese',
            'Pasūtījuma Datums'
        ], ';');
        
        // Dati
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['order_id'],
                number_format($order['total_price'], 2, '.', ''),
                self::translateStatus($order['status']),
                $order['payment_method'] ?? 'N/A',
                $order['delivery_method'] ?? 'N/A',
                $order['delivery_address'] ?? '',
                self::formatDate($order['order_date'])
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Eksportē pasūtījuma pozīcijas uz CSV
     */
    public static function exportOrderItems($order_id, $items, $filename = 'pasutijuma_items.csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Galvenes
        fputcsv($output, [
            'Picas Nosaukums',
            'Daudzums',
            'Cena (EUR)',
            'Kopā (EUR)'
        ], ';');
        
        $total = 0;
        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $total += $subtotal;
            
            fputcsv($output, [
                $item['pizza_name'],
                $item['quantity'],
                number_format($item['unit_price'], 2, '.', ''),
                number_format($subtotal, 2, '.', '')
            ], ';');
        }
        
        // Kopējā summa
        fputcsv($output, ['', '', 'Kopā:', number_format($total, 2, '.', '')], ';');
        
        fclose($output);
        exit;
    }
    
    /**
     * Eksportē picas uz CSV
     */
    public static function exportPizzas($pizzas, $filename = 'picas.csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Galvenes
        fputcsv($output, [
            'Picas ID',
            'Nosaukums',
            'Apraksts',
            'Cena (EUR)',
            'Kategorija',
            'Alergēni',
            'Aktīvs'
        ], ';');
        
        // Dati
        foreach ($pizzas as $pizza) {
            fputcsv($output, [
                $pizza['pizza_id'],
                $pizza['name'],
                strip_tags($pizza['description'] ?? ''),
                number_format($pizza['price'], 2, '.', ''),
                $pizza['category'] ?? 'pizza',
                $pizza['allergens'] ?? 'Nav',
                $pizza['is_active'] ? 'Jā' : 'Nē'
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Eksportē statistiku uz CSV
     */
    public static function exportStatistics($stats, $filename = 'statistika.csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Galvenes
        fputcsv($output, [
            'Rādītājs',
            'Vērtība'
        ], ';');
        
        // Dati
        foreach ($stats as $key => $value) {
            fputcsv($output, [
                self::translateKey($key),
                is_array($value) ? json_encode($value) : $value
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Eksportē atsauksmes uz CSV
     */
    public static function exportReviews($reviews, $filename = 'atsauksmes.csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Galvenes
        fputcsv($output, [
            'Atsauksmes ID',
            'Picas Nosaukums',
            'Reitings (1-5)',
            'Komentārs',
            'Apstiprinājums',
            'Datums'
        ], ';');
        
        // Dati
        foreach ($reviews as $review) {
            fputcsv($output, [
                $review['review_id'],
                $review['pizza_name'] ?? 'Vispārējā',
                $review['rating'] . '/5',
                $review['comment'] ?? '',
                $review['is_approved'] ? 'Jā' : 'Nē',
                self::formatDate($review['created_at'] ?? '')
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Eksportē atsauksmes uz CSV
     */
    public static function exportSupportRequests($requests, $filename = 'atbalsta_pieprasijumi.csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Galvenes
        fputcsv($output, [
            'Pieprasījuma ID',
            'Vārds',
            'E-pasts',
            'Ziņojums',
            'Statuss',
            'Datums'
        ], ';');
        
        // Dati
        foreach ($requests as $request) {
            fputcsv($output, [
                $request['support_id'],
                $request['name'],
                $request['email'],
                $request['message'],
                self::translateStatus($request['status']),
                self::formatDate($request['created_at'])
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Eksportē lietotājus uz CSV
     */
    public static function exportUsers($users, $filename = 'lietotaji.csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Galvenes
        fputcsv($output, [
            'Lietotāja ID',
            'Lietotājvārds',
            'E-pasts',
            'Loma',
            'Reģistrācijas Datums'
        ], ';');
        
        // Dati
        foreach ($users as $user) {
            fputcsv($output, [
                $user['user_id'],
                $user['username'],
                $user['email'],
                $user['role'],
                self::formatDate($user['created_at'])
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Palīgfunkcija - formatē datumu
     */
    private static function formatDate($date) {
        if (empty($date)) return '';
        return date('Y-m-d H:i', strtotime($date));
    }
    
    /**
     * Palīgfunkcija - tulko statusu
     */
    private static function translateStatus($status) {
        $translations = [
            'pending' => 'Gaidīts',
            'confirmed' => 'Apstiprinājums',
            'preparing' => 'Pagatavošana',
            'on_way' => 'Ceļā',
            'delivered' => 'Piegādāts',
            'cancelled' => 'Atcelts',
            'open' => 'Atvērts',
            'in_progress' => 'Tiek Apstrādāts',
            'resolved' => 'Atrisināts',
            'closed' => 'Slēgts'
        ];
        
        return $translations[$status] ?? $status;
    }
    
    /**
     * Palīgfunkcija - tulko atslēgus
     */
    private static function translateKey($key) {
        $translations = [
            'order_count' => 'Pasūtījumi',
            'total_revenue' => 'Kopējie Ieņēmumi',
            'average_order' => 'Vidējais Pasūtījums',
            'user_count' => 'Lietotāji',
            'review_count' => 'Atsauksmes'
        ];
        
        return $translations[$key] ?? $key;
    }
}
?>
