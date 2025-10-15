<?php
// Modelo para la gesti��n de ventas
require_once SRC_PATH . '/utils/Database.php';

class SaleModel {
    /**
     * Obtiene una venta por ID con sus ítems y detalles completos.
     * @param int $saleId
     * @return array|null
     */
    public function getSaleByIdWithItems($saleId) {
        // 1. Obtener datos principales de la venta
        $this->db->query('
            SELECT
                s.id, s.total_amount, s.sale_date,
                c.name as customer_name,
                u.full_name as user_name,
                ch.name as channel_name
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN channels ch ON s.channel_id = ch.id
            WHERE s.id = :id
        ');
        $this->db->bind(':id', $saleId, PDO::PARAM_INT);
        $sale = $this->db->single();
        if (!$sale) return null;

        // 2. Obtener ítems de la venta
        $this->db->query('
            SELECT si.*, p.name as product_name
            FROM sale_items si
            JOIN products p ON si.product_id = p.id
            WHERE si.sale_id = :id
        ');
        $this->db->bind(':id', $saleId, PDO::PARAM_INT);
        $sale['items'] = $this->db->resultSet();

        return $sale;
    }
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Devuelve la instancia de Database activa (para transacciones compartidas).
     * @return Database
     */
    public function getDb() {
        return $this->db;
    }

    // --- M��todos de Transacci��n ---
    public function startTransaction() {
        $this->db->query('START TRANSACTION');
        return $this->db->execute();
    }
    public function commitTransaction() {
        $this->db->query('COMMIT');
        return $this->db->execute();
    }
    public function rollbackTransaction() {
        $this->db->query('ROLLBACK');
        return $this->db->execute();
    }

    /**
     * Obtiene todas las ventas con detalles.
     * @return array
     */
    public function getAllSalesWithDetails() {
        $this->db->query('
            SELECT
                s.id, s.total_amount, s.sale_date,
                -- �0�4 CR�0�1TICO: LEFT JOIN para permitir que customer_id sea NULL
                c.name as customer_name,
                u.full_name as user_name,
                ch.name as channel_name
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN channels ch ON s.channel_id = ch.id
            ORDER BY s.sale_date DESC
        ');
        return $this->db->resultSet();
    }

    /**
     * Agrega una nueva venta.
     * @param int $userId ID del usuario.
     * @param int|null $customerId ID del cliente (acepta NULL).
     * @param int|null $channelId ID del canal.
     * @return int ID de la venta creada.
     */
    public function addSale($userId, $customerId, $channelId) {
    // Fecha local Buenos Aires
    $dt = new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires'));
    $fechaLocal = $dt->format('Y-m-d H:i:s');
    $this->db->query('INSERT INTO sales (user_id, customer_id, channel_id, total_amount, sale_date) VALUES (:user_id, :customer_id, :channel_id, 0, :sale_date)');
    $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
    $this->db->bind(':customer_id', $customerId, is_null($customerId) ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $this->db->bind(':channel_id', $channelId, PDO::PARAM_INT);
    $this->db->bind(':sale_date', $fechaLocal);
    $this->db->execute();
    return $this->db->lastInsertId();
    }

    // --- M��todos de Detalle de Venta (sin cambios) ---

    /**
     * Agrega un ��tem a una venta.
     */
    public function addSaleItem($saleId, $productId, $quantity, $priceAtSale) {
        $this->db->query('INSERT INTO sale_items (sale_id, product_id, quantity, price_at_sale) VALUES (:sale_id, :product_id, :quantity, :price_at_sale)');
        $this->db->bind(':sale_id', $saleId, PDO::PARAM_INT);
        $this->db->bind(':product_id', $productId, PDO::PARAM_INT);
        $this->db->bind(':quantity', $quantity, PDO::PARAM_INT);
        $this->db->bind(':price_at_sale', $priceAtSale);
        return $this->db->execute();
    }

    /**
     * Actualiza el total de la venta.
     */
    public function updateTotalAmount($saleId, $totalAmount) {
        $this->db->query('UPDATE sales SET total_amount = :total_amount WHERE id = :id');
        $this->db->bind(':total_amount', $totalAmount);
        $this->db->bind(':id', $saleId, PDO::PARAM_INT);
        return $this->db->execute();
    }
    
    /**
     * Obtiene el resumen de ventas agrupado por usuario.
     * @return array
     */
    public function getSalesSummaryByUser() {
        $this->db->query("
            SELECT
                u.full_name as user_name,
                COALESCE(SUM(s.total_amount), 0) AS total_sales
            FROM users u
            LEFT JOIN sales s ON u.id = s.user_id
            GROUP BY u.id, u.full_name
            ORDER BY total_sales DESC
        ");
        return $this->db->resultSet();
    }
}
?>
