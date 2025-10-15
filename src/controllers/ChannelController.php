<?php
// Controlador para el Módulo de Canales de Venta
require_once SRC_PATH . '/models/ChannelModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class ChannelController {
    private $channelModel;

    public function __construct() {
        $this->channelModel = new ChannelModel();
    }

    // Muestra la lista de canales de venta
    public function showList() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        $channels = $this->channelModel->getAllChannelsWithCosts();
        $salesData = $this->channelModel->getSalesDataByChannel();
        $csrf_token = Helpers::generateCsrfToken();
        
        Helpers::loadView('channels', [
            'channels' => $channels, 
            'salesData' => $salesData, 
            'csrf_token' => $csrf_token
        ]);
    }

    // Agrega un nuevo canal de venta
    public function addChannel() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $name = Helpers::escape($_POST['name']);
            $description = Helpers::escape($_POST['description']);

            if ($this->channelModel->addChannel($name, $description)) {
                $_SESSION['success_message'] = 'Canal de venta agregado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al agregar el canal de venta.';
            }
        }
        Helpers::redirect('/canales');
    }

    // Edita un canal de venta
    public function editChannel() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $name = Helpers::escape($_POST['name']);
            $description = Helpers::escape($_POST['description']);

            if ($this->channelModel->updateChannel($id, $name, $description)) {
                $_SESSION['success_message'] = 'Canal de venta actualizado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al actualizar el canal de venta.';
            }
        }
        Helpers::redirect('/canales');
    }
    
    // Elimina un canal de venta
    public function deleteChannel() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if ($this->channelModel->deleteChannel($id)) {
                $_SESSION['success_message'] = 'Canal de venta eliminado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al eliminar el canal de venta.';
            }
        }
        Helpers::redirect('/canales');
    }

    // Agrega un costo a un canal de venta
    public function addCost() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $channelId = filter_var($_POST['channel_id'], FILTER_SANITIZE_NUMBER_INT);
            $description = Helpers::escape($_POST['description']);
            $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $costDate = Helpers::escape($_POST['cost_date']);
            
            if ($this->channelModel->addCost($channelId, $description, $amount, $costDate)) {
                $_SESSION['success_message'] = 'Costo agregado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al agregar el costo.';
            }
        }
        Helpers::redirect('/canales');
    }
}
?>
