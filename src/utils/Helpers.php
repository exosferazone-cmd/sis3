<?php
// Funciones de utilidad y helpers del sistema
class Helpers {

    // Redirecciona a una URL
    public static function redirect($url) {
        header('Location: ' . $url);
        exit();
    }

    // Escapa caracteres especiales para prevenir XSS
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    // Genera un token CSRF y lo guarda en la sesión
    public static function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        return $token;
    }

    // Valida un token CSRF
    public static function validateCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        if (time() - $_SESSION['csrf_token_time'] > CSRF_EXPIRY_TIME) {
            unset($_SESSION['csrf_token']);
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    // Comprueba si el usuario está autenticado
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    // Comprueba si el usuario tiene un rol específico
    public static function hasRole($role_id) {
        return self::isAuthenticated() && $_SESSION['user_role'] == $role_id;
    }

    // Carga una vista desde la carpeta de templates
    public static function loadView($view, $data = []) {
        // Extrae las variables del array $data para usarlas en la vista
        extract($data);
        require_once TEMPLATES_PATH . '/' . $view . '.php';
    }
}
?>