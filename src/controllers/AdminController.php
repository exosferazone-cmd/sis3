<?php
// Controlador principal para el panel de administración
require_once SRC_PATH . '/utils/Helpers.php';

class AdminController {
    // Muestra la vista del dashboard principal
    public function showDashboard() {
        // La lógica para obtener datos del dashboard (ej. estadísticas) iría aquí.
        // Por ahora, solo cargamos la vista.
        Helpers::loadView('admin-dashboard');
    }
}
?>