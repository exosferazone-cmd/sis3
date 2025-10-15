<?php
// Modelo para la gestión de caja y finanzas
require_once SRC_PATH . '/utils/Database.php';

class FinanceModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Registra una nueva transacción (ingreso o egreso).
     * @param int $userId El ID del usuario que registra la transacción.
     * @param string $type El tipo de transacción ('ingreso' o 'egreso').
     * @param string $category La categoría de la transacción.
     * @param string $description La descripción de la transacción.
     * @param float $amount El monto de la transacción.
     * @return bool
     */
    public function addTransaction($userId, $type, $category, $description, $amount) {
        // Permite opcionalmente registrar la fecha exacta de la venta
        $args = func_get_args();
        $transactionDate = isset($args[5]) ? $args[5] : null;
        if ($transactionDate) {
            $this->db->query('INSERT INTO finances (user_id, type, category, description, amount, transaction_date) VALUES (:user_id, :type, :category, :description, :amount, :transaction_date)');
        } else {
            $this->db->query('INSERT INTO finances (user_id, type, category, description, amount) VALUES (:user_id, :type, :category, :description, :amount)');
        }
        $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
        $this->db->bind(':type', $type);
        $this->db->bind(':category', $category);
        $this->db->bind(':description', $description);
        $this->db->bind(':amount', $amount);
        if ($transactionDate) {
            $this->db->bind(':transaction_date', $transactionDate);
        }
        return $this->db->execute();
    }

    /**
     * Obtiene todas las transacciones financieras.
     * @return array
     */
    public function getAllTransactions() {
        $this->db->query('SELECT f.*, u.full_name as user_name FROM finances f JOIN users u ON f.user_id = u.id ORDER BY f.transaction_date DESC');
        return $this->db->resultSet();
    }

    /**
     * Obtiene el total de ingresos.
     * @return float
     */
    public function getTotalIncome() {
        $this->db->query("SELECT SUM(amount) as total FROM finances WHERE type = 'ingreso'");
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }

    /**
     * Obtiene el total de egresos.
     * @return float
     */
    public function getTotalExpenses() {
        $this->db->query("SELECT SUM(amount) as total FROM finances WHERE type = 'egreso'");
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }
}
?>
