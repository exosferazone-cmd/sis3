
<?php
// Controlador para el Módulo de Control de Inventario
require_once SRC_PATH . '/models/ProductModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }
    
    // Muestra el detalle de un producto
    public function showDetail() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        if (!isset($_GET['id'])) {
            Helpers::redirect('/productos');
        }
        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            $_SESSION['error_message'] = 'Producto no encontrado.';
            Helpers::redirect('/productos');
        }
        Helpers::loadView('product-details', ['product' => $product]);
    }
    // Muestra la lista de productos
    public function showList() {
        if (!Helpers::isAuthenticated()) {
            Helpers::redirect('/');
        }
        $products = $this->productModel->getAllProducts();
        $csrf_token = Helpers::generateCsrfToken();
        Helpers::loadView('products', ['products' => $products, 'csrf_token' => $csrf_token]);
    }

    // Agrega un nuevo producto (actualizado para WooCommerce)
    public function addProduct() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $name = Helpers::escape($_POST['name']);
            $description = Helpers::escape($_POST['description']);
            $sku = Helpers::escape($_POST['sku']);
            $regular_price = filter_var($_POST['regular_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $sale_price = filter_var($_POST['sale_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $woo_type = Helpers::escape($_POST['woo_type']);
            $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);
            $min_stock_alert = filter_var($_POST['min_stock_alert'], FILTER_SANITIZE_NUMBER_INT);
            $images_url = Helpers::escape($_POST['images_url']);
            $attributes_json = self::parseAttributesToJSON(Helpers::escape($_POST['attributes_input']));

            if ($this->productModel->addProduct($name, $description, $sku, $regular_price, $sale_price, $woo_type, $stock, $min_stock_alert, $attributes_json, $images_url)) {
                $_SESSION['success_message'] = 'Producto agregado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al agregar producto.';
            }
        }
        Helpers::redirect('/productos');
    }

    // Edita un producto existente (actualizado para WooCommerce)
    public function editProduct() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Helpers::validateCsrfToken($_POST['csrf_token'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $name = Helpers::escape($_POST['name']);
            $description = Helpers::escape($_POST['description']);
            $sku = Helpers::escape($_POST['sku']);
            $regular_price = filter_var($_POST['regular_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $sale_price = filter_var($_POST['sale_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $woo_type = Helpers::escape($_POST['woo_type']);
            $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);
            $min_stock_alert = filter_var($_POST['min_stock_alert'], FILTER_SANITIZE_NUMBER_INT);
            $images_url = Helpers::escape($_POST['images_url']);
            $attributes_json = self::parseAttributesToJSON(Helpers::escape($_POST['attributes_input']));

            if ($this->productModel->updateProduct($id, $name, $description, $sku, $regular_price, $sale_price, $woo_type, $stock, $min_stock_alert, $attributes_json, $images_url)) {
                $_SESSION['success_message'] = 'Producto actualizado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al actualizar producto.';
            }
        }
        Helpers::redirect('/productos');
    }

    // Elimina un producto
    public function deleteProduct() {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if ($this->productModel->deleteProduct($id)) {
                $_SESSION['success_message'] = 'Producto eliminado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al eliminar producto.';
            }
        }
        Helpers::redirect('/productos');
    }
    
    // Devuelve productos en formato JSON para peticiones API/AJAX (se mantiene para compatibilidad futura)
    public function getProductsApi() {
        if (!Helpers::isAuthenticated()) {
            http_response_code(403);
            die(json_encode(['error' => 'Acceso denegado']));
        }
        
        $searchTerm = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $products = $this->productModel->searchProducts($searchTerm);
        
        header('Content-Type: application/json');
        echo json_encode($products);
    }
    
    // Función auxiliar para parsear el string de atributos a JSON
    private static function parseAttributesToJSON($attributes_string) {
        $attributes = [];
        // Espera formato: "Color|Azul,Blanco,Negra;Talles|S,M,L"
        $attribute_groups = explode(';', $attributes_string);
        
        foreach ($attribute_groups as $group) {
            $parts = explode('|', $group, 2);
            if (count($parts) === 2) {
                $name = trim($parts[0]);
                $values_string = trim($parts[1]);
                $values = array_map('trim', explode(',', $values_string));
                
                if (!empty($name) && !empty($values)) {
                    $attributes[] = [
                        'name' => $name,
                        'values' => $values,
                        'visible' => 1, 
                        'global' => 1
                    ];
                }
            }
        }
        return json_encode($attributes);
    }
}
?>
