<?php
// Controlador para el Módulo de Sincronización (WooCommerce)
require_once SRC_PATH . '/models/WooCommerceModel.php';
require_once SRC_PATH . '/models/ProductModel.php';
require_once SRC_PATH . '/utils/Helpers.php';

class SyncController {
    private $wooModel;
    private $productModel;

    public function __construct() {
        $this->wooModel = new WooCommerceModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Sincroniza un producto específico de ERP a WooCommerce.
     * @param int $id ID del producto en el ERP.
     */
    public function syncProduct($id) {
        if (!Helpers::isAuthenticated() || !Helpers::hasRole(ROLE_ADMIN)) {
            Helpers::redirect('/');
        }

        $erpProduct = $this->productModel->getProductById($id);
        if (!$erpProduct) {
            $_SESSION['error_message'] = 'Error: Producto ERP no encontrado.';
            Helpers::redirect('/productos');
        }
        
        // 1. Mapear los datos del ERP al formato de WooCommerce
        $wooData = $this->mapToWooCommerceFormat($erpProduct);

        // 2. Determinar si es una creación o una actualización
        $woo_id = $erpProduct['woo_id'] ?? null;
        
        if (empty($woo_id)) {
            // CREACIÓN
            $response = $this->wooModel->createProduct($wooData);
        } else {
            // ACTUALIZACIÓN
            $response = $this->wooModel->updateProduct($woo_id, $wooData);
        }

        // 3. Manejar la respuesta
        if (isset($response['error']) && $response['error']) {
            $_SESSION['error_message'] = "Error de sincronización: " . $response['message'];
        } else {
            $_SESSION['success_message'] = 'Producto sincronizado con éxito.';
            
            // Si fue una creación, guardar el ID de WooCommerce en la base de datos local
            if (empty($woo_id) && isset($response['id'])) {
                // Aquí necesitarás un método en ProductModel para actualizar solo woo_id.
                // Lo harás en el siguiente paso. Por ahora, solo loguea.
                // Por simplicidad, asumiremos que actualiza el ERP con el ID de Woo para futuras sincronizaciones.
                $this->productModel->updateWooId($id, $response['id']); 
            }
        }
        
        Helpers::redirect('/productos');
    }

    // ----------------------------------------------------
    // FUNCIÓN DE MAPEO
    // ----------------------------------------------------

    private function mapToWooCommerceFormat($erpProduct) {
        // Formatear imágenes
        $images = [];
        if (!empty($erpProduct['images_url'])) {
            $urls = explode(',', $erpProduct['images_url']);
            foreach ($urls as $url) {
                $images[] = ['src' => trim($url)];
            }
        }
        
        // Formatear atributos (si es producto variable)
        $attributes = [];
        if ($erpProduct['woo_type'] === 'variable' && !empty($erpProduct['attributes_json'])) {
            $erpAttributes = json_decode($erpProduct['attributes_json'], true);
            foreach ($erpAttributes as $attr) {
                $attributes[] = [
                    'name' => $attr['name'],
                    'options' => $attr['values'],
                    'visible' => true,
                    'variation' => true // Debe ser true para variaciones
                ];
            }
        }

        $data = [
            'name' => $erpProduct['name'],
            'type' => $erpProduct['woo_type'], // 'simple' o 'variable'
            'sku' => $erpProduct['sku'],
            'regular_price' => (string)$erpProduct['regular_price'],
            'sale_price' => (string)$erpProduct['sale_price'],
            'description' => $erpProduct['description'],
            'short_description' => $erpProduct['description'], // Usar la misma descripción por ahora
            'stock_quantity' => (int)$erpProduct['stock'],
            'manage_stock' => true, // Siempre gestionamos el stock desde el ERP
            'status' => 'publish',
            'images' => $images,
            'attributes' => $attributes,
            // Nota: Aquí faltaría la lógica de variaciones para productos 'variable'.
            // Por ahora, solo creará el producto 'variable' base con atributos.
        ];

        // Limpiar precios nulos para evitar errores de Woo
        if (empty($erpProduct['sale_price'])) {
            unset($data['sale_price']);
        }
        
        return $data;
    }
}