<?php
// Entrada al panel de administración
require_once __DIR__ . '/../config/config.php';
require_once SRC_PATH . '/utils/Helpers.php';
require_once SRC_PATH . '/controllers/AdminController.php';

// Redirecciona al login si el usuario no está autenticado
if (!Helpers::isAuthenticated()) {
    Helpers::redirect('/');
}

// Verifica si el usuario tiene rol de administrador
if (!Helpers::hasRole(ROLE_ADMIN)) {
    echo 'Acceso denegado. No tienes permisos para ver esta página.';
    exit();
}

$adminController = new AdminController();
$adminController->showDashboard();
?>
