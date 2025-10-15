
<?php
// Controlador para el Módulo de Gestión de Clientes
require_once SRC_PATH . '/models/CustomerModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class CustomerController {
    private $customerModel;

    public function __construct() {
        $this->customerModel = new CustomerModel();
    }

    // Muestra el detalle de un cliente
    public function showDetail() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        if (!isset($_GET['id'])) {
            Helpers::redirect('/clientes');
        }
        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $customer = $this->customerModel->getCustomerById($id);
        if (!$customer) {
            $_SESSION['error_message'] = 'Cliente no encontrado.';
            Helpers::redirect('/clientes');
        }
        Helpers::loadView('customer-details', ['customer' => $customer]);
    }

    // Muestra la lista de clientes
    public function showList() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        $customers = $this->customerModel->getAllCustomers();
        $csrf_token = Helpers::generateCsrfToken();
        Helpers::loadView('customers', ['customers' => $customers, 'csrf_token' => $csrf_token]);
    }

    // Agrega un nuevo cliente
    public function addCustomer() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $name = Helpers::escape($_POST['name']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone = Helpers::escape($_POST['phone']);
            $address = Helpers::escape($_POST['address']);

            if ($this->customerModel->addCustomer($name, $email, $phone, $address)) {
                $_SESSION['success_message'] = 'Cliente agregado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al agregar cliente.';
            }
        }
        Helpers::redirect('/clientes');
    }

    // Edita un cliente existente
    public function editCustomer() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $name = Helpers::escape($_POST['name']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone = Helpers::escape($_POST['phone']);
            $address = Helpers::escape($_POST['address']);

            try {
                if ($this->customerModel->updateCustomer($id, $name, $email, $phone, $address)) {
                    $_SESSION['success_message'] = 'Cliente actualizado con éxito.';
                } else {
                    $_SESSION['error_message'] = 'Error desconocido al actualizar cliente.';
                }
            } catch (PDOException $e) {
                // Captura la excepción. El código 23000 es la violación de integridad (UNIQUE KEY, etc.)
                if ($e->getCode() == '23000') {
                    $_SESSION['error_message'] = 'Error: Ya existe un cliente con ese Email. Los emails deben ser únicos.';
                } else {
                    // Si no es un error de unicidad, muestra el error de PDO completo para depuración
                    $_SESSION['error_message'] = 'Error de BD al actualizar: ' . $e->getMessage();
                }
            }
        }
        Helpers::redirect('/clientes');
    }

    // Elimina un cliente
    public function deleteCustomer() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        // Se corrigió para esperar un POST, no un GET
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

            if ($this->customerModel->deleteCustomer($id)) {
                $_SESSION['success_message'] = 'Cliente eliminado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al eliminar cliente.';
            }
        }
        Helpers::redirect('/clientes');
    }
    
    // Devuelve clientes en formato JSON para peticiones API/AJAX
    public function getClientsApi() {
        if (!Helpers::isAuthenticated()) {
            http_response_code(403);
            die(json_encode(['error' => 'Acceso denegado']));
        }
        
        $searchTerm = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $customers = $this->customerModel->searchCustomers($searchTerm);
        
        header('Content-Type: application/json');
        echo json_encode($customers);
    }
}
?>
