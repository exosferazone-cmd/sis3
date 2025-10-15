<?php
// Modelo para la gestión de canales de venta
require_once SRC_PATH . '/utils/Database.php';

class ChannelModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Obtiene todos los canales de venta y sus costos totales.
     * @return array
     */
    public function getAllChannelsWithCosts() {
        $this->db->query("
            SELECT 
                c.*, 
                COALESCE(SUM(cc.amount), 0) AS total_costs 
            FROM channels c 
            LEFT JOIN channel_costs cc ON c.id = cc.channel_id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Agrega un nuevo canal de venta.
     * @param string $name Nombre del canal.
     * @param string $description Descripción.
     * @return bool
     */
    public function addChannel($name, $description) {
        $this->db->query('INSERT INTO channels (name, description) VALUES (:name, :description)');
        $this->db->bind(':name', $name);
        $this->db->bind(':description', $description);
        return $this->db->execute();
    }

    /**
     * Actualiza un canal existente.
     * @param int $id ID del canal.
     * @param string $name Nuevo nombre.
     * @param string $description Nueva descripción.
     * @return bool
     */
    public function updateChannel($id, $name, $description) {
        $this->db->query('UPDATE channels SET name = :name, description = :description WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':name', $name);
        $this->db->bind(':description', $description);
        return $this->db->execute();
    }

    /**
     * Elimina un canal de venta.
     * @param int $id ID del canal.
     * @return bool
     */
    public function deleteChannel($id) {
        // Para evitar errores de integridad, primero eliminamos los costos asociados
        $this->db->query('DELETE FROM channel_costs WHERE channel_id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->execute();

        // Ahora eliminamos el canal
        $this->db->query('DELETE FROM channels WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Registra un costo asociado a un canal de venta.
     * @param int $channelId El ID del canal.
     * @param string $description Descripción del costo.
     * @param float $amount Monto del costo.
     * @param string $costDate Fecha del costo.
     * @return bool
     */
    public function addCost($channelId, $description, $amount, $costDate) {
        $this->db->query('INSERT INTO channel_costs (channel_id, description, amount, cost_date) VALUES (:channel_id, :description, :amount, :cost_date)');
        $this->db->bind(':channel_id', $channelId, PDO::PARAM_INT);
        $this->db->bind(':description', $description);
        $this->db->bind(':amount', $amount);
        $this->db->bind(':cost_date', $costDate);
        return $this->db->execute();
    }

    /**
     * Obtiene los datos de ventas por canal.
     * @return array
     */
    public function getSalesDataByChannel() {
        $this->db->query("
            SELECT 
                c.name AS channel_name, 
                COALESCE(SUM(s.total_amount), 0) AS total_sales
            FROM channels c
            LEFT JOIN sales s ON c.id = s.channel_id
            GROUP BY c.name
            ORDER BY total_sales DESC
        ");
        return $this->db->resultSet();
    }
}
?>
