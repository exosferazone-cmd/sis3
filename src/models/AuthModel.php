<?php
// Clase para la lógica de autenticación y gestión de usuarios
require_once SRC_PATH . '/utils/Database.php';

class AuthModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Busca un usuario por su email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    // Registra un nuevo usuario
    public function register($full_name, $email, $password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query('INSERT INTO users (full_name, email, password_hash, role_id) VALUES (:full_name, :email, :password_hash, :role_id)');
        $this->db->bind(':full_name', $full_name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password_hash', $password_hash);
        $this->db->bind(':role_id', ROLE_VENDEDOR); // Por defecto, el rol es vendedor
        return $this->db->execute();
    }
}
?>