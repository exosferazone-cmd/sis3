<?php
// Controlador para el manejo de la autenticación
require_once SRC_PATH . '/models/AuthModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    // Muestra la vista de login
    public function showLogin() {
        // Redirecciona al dashboard si ya está autenticado
        if (Helpers::isAuthenticated()) {
            Helpers::redirect('/admin');
        }
        $csrf_token = Helpers::generateCsrfToken();
        Helpers::loadView('login', ['csrf_token' => $csrf_token]);
    }

    // Procesa el formulario de login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!Helpers::validateCsrfToken($_POST['csrf_token'])) {
                die('Token CSRF inválido.');
            }

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            $user = $this->authModel->findUserByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Autenticación exitosa, crea la sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role_id'];

                Helpers::redirect('/admin');
            } else {
                // Fallo en la autenticación
                $_SESSION['login_error'] = 'Email o contraseña incorrectos.';
                Helpers::redirect('/');
            }
        }
    }

    // Cierra la sesión del usuario
    public function logout() {
        session_destroy();
        Helpers::redirect('/');
    }
}
?>
