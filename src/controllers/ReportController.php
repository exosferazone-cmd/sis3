<?php
// Controlador para el Módulo de Reportes y Analíticas
require_once SRC_PATH . '/models/SaleModel.php';
require_once SRC_PATH . '/models/FinanceModel.php';
require_once SRC_PATH . '/models/ChannelModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class ReportController {
    private $saleModel;
    private $financeModel;
    private $channelModel;

    public function __construct() {
        $this->saleModel = new SaleModel();
        $this->financeModel = new FinanceModel();
        $this->channelModel = new ChannelModel();
    }

    // Muestra el dashboard principal de reportes
    public function showDashboard() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }
        
        // 1. Datos Financieros
        $totalIncome = $this->financeModel->getTotalIncome();
        $totalExpenses = $this->financeModel->getTotalExpenses();
        $netBalance = $totalIncome - $totalExpenses;

        // 2. Reporte de Ventas por Canal
        $salesByChannel = $this->channelModel->getSalesDataByChannel();
        $totalSales = array_sum(array_column($salesByChannel, 'total_sales'));

        // 3. Ventas por Vendedor (Usuario)
        $salesByUser = $this->saleModel->getSalesSummaryByUser();
        
        Helpers::loadView('reports-dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netBalance' => $netBalance,
            'salesByChannel' => $salesByChannel,
            'totalSales' => $totalSales,
            'salesByUser' => $salesByUser,
        ]);
    }
}
?>
