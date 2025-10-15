<?php
/**
 * Controlador para gestión de usuarios y perfiles
 * Solo admin puede acceder al CRUD completo
 */
require_once SRC_PATH . '/models/UserModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class UserController {
    private $userModel;
    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Listado de usuarios (solo admin)
    public function showList() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            Helpers::redirect('/');
        }
        $users = $this->userModel->getAllUsersWithRoles();
        Helpers::loadView('users-list', ['users' => $users]);
    }

    // Formulario de edición de perfil propio
    public function showProfile() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        Helpers::loadView('user-profile', ['user' => $user]);
    }

    // Actualizar perfil propio
    public function updateProfile() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        $userId = $_SESSION['user_id'];
        $name = Helpers::escape($_POST['name']);
        $email = Helpers::escape($_POST['email']);
        $phone = Helpers::escape($_POST['phone']);
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
        $this->userModel->updateProfile($userId, $name, $email, $phone, $password);
        $_SESSION['success_message'] = 'Perfil actualizado.';
        Helpers::redirect('/mi-perfil');
    }

    // CRUD de usuarios (solo admin)
    public function addUser() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            Helpers::redirect('/');
        }
        // ...implementación alta usuario...
    }
    public function editUser() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            Helpers::redirect('/');
        }
        // ...implementación edición usuario...
    }
    public function deleteUser() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            Helpers::redirect('/');
        }
        // ...implementación baja usuario...
    }
}
?>
