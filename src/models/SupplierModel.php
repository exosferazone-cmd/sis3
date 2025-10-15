<?php
// Modelo para la gestión de proveedores
require_once SRC_PATH . '/utils/Database.php';

class SupplierModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Obtiene todos los proveedores de la base de datos.
     * @return array
     */
    public function getAllSuppliers() {
        $this->db->query('SELECT * FROM suppliers ORDER BY name ASC');
        return $this->db->resultSet();
    }

    /**
     * Agrega un nuevo proveedor.
     * @param string $name Nombre del proveedor.
     * @param string $contactPerson Persona de contacto.
     * @param string $email Email del proveedor.
     * @param string $phone Teléfono del proveedor.
     * @param string $address Dirección del proveedor.
     * @return bool
     */
    public function addSupplier($name, $contactPerson, $email, $phone, $address) {
        $this->db->query('INSERT INTO suppliers (name, contact_person, email, phone, address) VALUES (:name, :contact_person, :email, :phone, :address)');
        $this->db->bind(':name', $name);
        $this->db->bind(':contact_person', $contactPerson);
        $this->db->bind(':email', $email);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':address', $address);
        return $this->db->execute();
    }

    /**
     * Actualiza un proveedor existente.
     * @param int $id ID del proveedor a actualizar.
     * @param string $name Nombre del proveedor.
     * @param string $contactPerson Persona de contacto.
     * @param string $email Email del proveedor.
     * @param string $phone Teléfono del proveedor.
     * @param string $address Dirección del proveedor.
     * @return bool
     */
    public function updateSupplier($id, $name, $contactPerson, $email, $phone, $address) {
        $this->db->query('UPDATE suppliers SET name = :name, contact_person = :contact_person, email = :email, phone = :phone, address = :address WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':name', $name);
        $this->db->bind(':contact_person', $contactPerson);
        $this->db->bind(':email', $email);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':address', $address);
        return $this->db->execute();
    }

    /**
     * Elimina un proveedor por su ID.
     * @param int $id ID del proveedor a eliminar.
     * @return bool
     */
    public function deleteSupplier($id) {
        $this->db->query('DELETE FROM suppliers WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}
?>
