<?php
// Controlador para el Módulo de Caja y Finanzas
require_once SRC_PATH . '/models/FinanceModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class FinanceController {
    private $financeModel;

    public function __construct() {
        $this->financeModel = new FinanceModel();
    }

    // Muestra la vista de la caja y listado de transacciones
    public function showCashRegister() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        
        $transactions = $this->financeModel->getAllTransactions();
        $totalIncome = $this->financeModel->getTotalIncome();
        $totalExpenses = $this->financeModel->getTotalExpenses();
        $balance = $totalIncome - $totalExpenses;
        $csrf_token = Helpers::generateCsrfToken();
        
        Helpers::loadView('cash-register', [
            'transactions' => $transactions,
            'balance' => $balance,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'csrf_token' => $csrf_token
        ]);
    }

    // Agrega una nueva transacción financiera
    public function addTransaction() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $userId = $_SESSION['user_id'];
            $type = $_POST['type'] == 'ingreso' ? 'ingreso' : 'egreso';
            $category = Helpers::escape($_POST['category']);
            $description = Helpers::escape($_POST['description']);
            $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
            if ($this->financeModel->addTransaction($userId, $type, $category, $description, $amount)) {
                $_SESSION['success_message'] = 'Transacción registrada con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al registrar la transacción.';
            }
        }
        Helpers::redirect('/caja');
    }
}
?>
