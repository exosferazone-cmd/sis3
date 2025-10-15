<?php
// Modelo para la gestión de clientes
require_once SRC_PATH . '/utils/Database.php';

class CustomerModel {
    /**
     * Obtiene un cliente por ID
     * @param int $id
     * @return array|null
     */
    public function getCustomerById($id) {
        $this->db->query('SELECT * FROM customers WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Obtiene todos los clientes
    public function getAllCustomers() {
        $this->db->query('SELECT * FROM customers ORDER BY name ASC');
        return $this->db->resultSet();
    }
    
    // Agrega un nuevo cliente
    public function addCustomer($name, $email, $phone, $address) {
        $this->db->query('INSERT INTO customers (name, email, phone, address) VALUES (:name, :email, :phone, :address)');
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':address', $address);
        return $this->db->execute();
    }

    // Actualiza un cliente
    public function updateCustomer($id, $name, $email, $phone, $address) {
        // Asegúrate de que $id sea un entero válido para evitar errores de PDO
        if (!is_numeric($id) || $id <= 0) {
            error_log("Error: ID de cliente inválido en updateCustomer: " . $id);
            return false;
        }

        $this->db->query('UPDATE customers SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT); // FORZAMOS EL TIPO INT
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':address', $address);
        
        return $this->db->execute();
    }

    // Elimina un cliente
    public function deleteCustomer($id) {
        $this->db->query('DELETE FROM customers WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    
    /**
     * Busca clientes por nombre, email o teléfono para la API.
     * @param string $searchTerm Término de búsqueda.
     * @return array
     */
    public function searchCustomers($searchTerm) {
        $searchTerm = "%{$searchTerm}%";
        // CRÍTICO: La consulta debe ser correcta y las columnas deben existir
        $this->db->query('SELECT id, name, email, phone FROM customers 
                          WHERE name LIKE :term OR email LIKE :term OR phone LIKE :term
                          ORDER BY name ASC LIMIT 10');
        $this->db->bind(':term', $searchTerm);
        return $this->db->resultSet();
    }
}
?>
