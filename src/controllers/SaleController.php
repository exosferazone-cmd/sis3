<?php
// Controlador para el M��dulo de Gesti��n de Ventas
require_once SRC_PATH . '/models/SaleModel.php';
require_once SRC_PATH . '/models/CustomerModel.php';
require_once SRC_PATH . '/models/ProductModel.php';
require_once SRC_PATH . '/models/ChannelModel.php'; 
require_once SRC_PATH . '/utils/Helpers.php';

class SaleController {
    private $saleModel;
    private $customerModel;
    private $productModel;
    private $channelModel; 

    public function __construct() {
    $this->saleModel = new SaleModel();
    $this->customerModel = new CustomerModel();
    $this->productModel = new ProductModel();
    $this->channelModel = new ChannelModel();
    require_once SRC_PATH . '/models/FinanceModel.php';
    $this->financeModel = new FinanceModel();
    }

    // Muestra la lista de ventas
    public function showList() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        $sales = $this->saleModel->getAllSalesWithDetails();
        Helpers::loadView('sales-list', ['sales' => $sales]);
    }

    // Muestra el formulario para crear una nueva venta
    public function showCreateForm() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        
        // Carga todos los datos para los SELECTs estables (Rollback de AJAX)
        $customers = $this->customerModel->getAllCustomers(); 
        $products = $this->productModel->getAllProducts();
        $channels = $this->channelModel->getAllChannelsWithCosts(); 
        
        $csrf_token = Helpers::generateCsrfToken();
        Helpers::loadView('sale-form', [
            'customers' => $customers,
            'products' => $products,
            'channels' => $channels, 
            'csrf_token' => $csrf_token
        ]);
    }

    // Guarda una nueva venta
    public function saveSale() {
        if (!Helpers::isAuthenticated()) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $userId = $_SESSION['user_id'];
            
            // 1. Manejo robusto de customer_id (puede ser NULL)
            $customerId = isset($_POST['customer_id']) && !empty($_POST['customer_id']) 
                ? filter_var($_POST['customer_id'], FILTER_SANITIZE_NUMBER_INT) 
                : null;
            
            // 2. Obtener channel_id
            $channelId = filter_var($_POST['channel_id'], FILTER_SANITIZE_NUMBER_INT);
            
            // 3. Manejo seguro de arrays de productos
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];

            if (empty($productIds) || count($productIds) != count($quantities)) {
                $_SESSION['error_message'] = 'Datos de productos inv��lidos o incompletos.';
                Helpers::redirect('/ventas/create');
            }

            $this->saleModel->startTransaction();
            try {
                // Se pasan TRES parámetros
                $saleId = $this->saleModel->addSale($userId, $customerId, $channelId); 
                if (!$saleId) {
                    throw new Exception('Error al crear la venta.');
                }

                // Compartir la conexión activa con ProductModel
                $this->productModel->setDb($this->saleModel->getDb());

                $totalAmount = 0;
                for ($i = 0; $i < count($productIds); $i++) {
                    $productId = filter_var($productIds[$i], FILTER_SANITIZE_NUMBER_INT);
                    $quantity = filter_var($quantities[$i], FILTER_SANITIZE_NUMBER_INT);

                    if ($quantity <= 0) {
                         throw new Exception('La cantidad del producto debe ser mayor a cero.');
                    }

                    // Se obtiene el producto. El modelo asegura que el precio se llama 'price'.
                    $product = $this->productModel->getProductById($productId); 
                    if (!$product || $product['stock'] < $quantity) {
                        throw new Exception('Stock insuficiente para el producto: ' . $product['name']);
                    }

                    $priceAtSale = $product['price']; 
                    $this->saleModel->addSaleItem($saleId, $productId, $quantity, $priceAtSale);
                    $this->productModel->decreaseStock($productId, $quantity);
                    $totalAmount += $priceAtSale * $quantity;
                }

                $this->saleModel->updateTotalAmount($saleId, $totalAmount);
                $this->saleModel->commitTransaction();

                // Sincronizar con módulo financiero: registrar ingreso
                $customerName = '';
                if ($customerId) {
                    $customer = $this->customerModel->getCustomerById($customerId);
                    $customerName = $customer ? $customer['name'] : '';
                }
                $description = 'Venta #' . $saleId . ' registrada';
                if ($customerName) {
                    $description .= ' a ' . $customerName;
                }
                $category = 'Venta de productos';
                // Obtener la fecha real de la venta
                $venta = $this->saleModel->getSaleByIdWithItems($saleId);
                // Ajustar la fecha a Buenos Aires (Argentina)
                if ($venta && isset($venta['sale_date'])) {
                    // Registrar la fecha en UTC (como la guarda MySQL)
                    $fechaVenta = (new DateTime($venta['sale_date'], new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
                } else {
                    $fechaVenta = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
                }
                $this->financeModel->addTransaction($userId, 'ingreso', $category, $description, $totalAmount, $fechaVenta);

                $_SESSION['success_message'] = 'Venta registrada con éxito.';
            } catch (Exception $e) {
                $this->saleModel->rollbackTransaction();
                $_SESSION['error_message'] = $e->getMessage();
            }
        }
        Helpers::redirect('/ventas');
    }

    // Muestra los detalles de una venta espec��fica
    public function showDetails() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        
        $saleId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if (!$saleId) {
            $this->handleNotFound();
            return;
        }

        $sale = $this->saleModel->getSaleByIdWithItems($saleId);

        if (!$sale) {
            $this->handleNotFound();
            return;
        }
        
        Helpers::loadView('sale-details', ['sale' => $sale]);
    }

    private function handleNotFound() {
        http_response_code(404);
        echo '404 - Venta no encontrada.';
    }
}
?>