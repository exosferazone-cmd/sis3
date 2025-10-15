<?php
// Controlador para el Módulo de Gestión de Proveedores
require_once SRC_PATH . '/models/SupplierModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class SupplierController {
    private $supplierModel;

    public function __construct() {
        $this->supplierModel = new SupplierModel();
    }

    // Muestra la lista de proveedores
    public function showList() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        $suppliers = $this->supplierModel->getAllSuppliers();
        $csrf_token = Helpers::generateCsrfToken();
        Helpers::loadView('suppliers', ['suppliers' => $suppliers, 'csrf_token' => $csrf_token]);
    }

    // Agrega un nuevo proveedor
    public function addSupplier() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $name = Helpers::escape($_POST['name']);
            $contactPerson = Helpers::escape($_POST['contact_person']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone = Helpers::escape($_POST['phone']);
            $address = Helpers::escape($_POST['address']);

            if ($this->supplierModel->addSupplier($name, $contactPerson, $email, $phone, $address)) {
                $_SESSION['success_message'] = 'Proveedor agregado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al agregar proveedor.';
            }
        }
        Helpers::redirect('/proveedores');
    }

    // Edita un proveedor existente
    public function editSupplier() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $name = Helpers::escape($_POST['name']);
            $contactPerson = Helpers::escape($_POST['contact_person']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone = Helpers::escape($_POST['phone']);
            $address = Helpers::escape($_POST['address']);

            if ($this->supplierModel->updateSupplier($id, $name, $contactPerson, $email, $phone, $address)) {
                $_SESSION['success_message'] = 'Proveedor actualizado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al actualizar proveedor.';
            }
        }
        Helpers::redirect('/proveedores');
    }

    // Elimina un proveedor
    public function deleteSupplier() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if ($this->supplierModel->deleteSupplier($id)) {
                $_SESSION['success_message'] = 'Proveedor eliminado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al eliminar proveedor.';
            }
        }
        Helpers::redirect('/proveedores');
    }
}
?>
