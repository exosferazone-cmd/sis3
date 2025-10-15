<?php
/**
 * Modelo para gestión de usuarios y roles
 */
require_once SRC_PATH . '/utils/Database.php';
class UserModel {
    private $db;
    public function __construct() {
        $this->db = new Database();
    }
    public function getAllUsersWithRoles() {
        $this->db->query('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.full_name ASC');
        return $this->db->resultSet();
    }
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    public function updateProfile($id, $name, $email, $phone, $password = null) {
        $sql = 'UPDATE users SET full_name = :name, email = :email, phone = :phone';
        if ($password) {
            $sql .= ', password_hash = :password';
        }
        $sql .= ' WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':phone', $phone);
        if ($password) {
            $this->db->bind(':password', $password);
        }
        return $this->db->execute();
    }
    // ...alta, edición y baja de usuarios...
}
?>
