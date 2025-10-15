<?php
// Modelo para la gestión de productos
require_once SRC_PATH . '/utils/Database.php';

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Permite setear una instancia de Database externa (para transacciones compartidas).
     * @param Database $db Instancia activa de Database
     */
    public function setDb($db) {
        $this->db = $db;
    }
    
    
    // --- Métodos de Lectura (Con Alias) ---

    public function getAllProducts() {
        // 🚨 CRÍTICO: Usar 'regular_price as price' para compatibilidad con las vistas.
        $this->db->query('SELECT *, regular_price as price FROM products ORDER BY name ASC');
        return $this->db->resultSet();
    }

    public function getProductById($id) {
        // 🚨 CRÍTICO: Usar 'regular_price as price' para compatibilidad con el controlador de ventas.
        $this->db->query('SELECT *, regular_price as price FROM products WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    
    // --- Métodos CRUD (con campos WooCommerce) ---

    public function addProduct($name, $description, $sku, $regular_price, $sale_price, $woo_type, $stock, $min_stock_alert, $attributes_json, $images_url) {
        $this->db->query('INSERT INTO products (name, description, sku, regular_price, sale_price, woo_type, stock, min_stock_alert, attributes_json, images_url) 
                          VALUES (:name, :description, :sku, :regular_price, :sale_price, :woo_type, :stock, :min_stock_alert, :attributes_json, :images_url)');
        
        $this->db->bind(':name', $name);
        $this->db->bind(':description', $description);
        $this->db->bind(':sku', $sku);
        $this->db->bind(':regular_price', $regular_price);
        $this->db->bind(':sale_price', $sale_price);
        $this->db->bind(':woo_type', $woo_type);
        $this->db->bind(':stock', $stock, PDO::PARAM_INT);
        $this->db->bind(':min_stock_alert', $min_stock_alert, PDO::PARAM_INT);
        $this->db->bind(':attributes_json', $attributes_json);
        $this->db->bind(':images_url', $images_url);
        
        return $this->db->execute();
    }
    /**
     * Actualiza un producto existente con campos WooCommerce.
     */
    public function updateProduct($id, $name, $description, $sku, $regular_price, $sale_price, $woo_type, $stock, $min_stock_alert, $attributes_json, $images_url) {
        $this->db->query('UPDATE products SET 
                            name = :name, 
                            description = :description, 
                            sku = :sku, 
                            regular_price = :regular_price, 
                            sale_price = :sale_price,
                            woo_type = :woo_type, 
                            stock = :stock, 
                            min_stock_alert = :min_stock_alert,
                            attributes_json = :attributes_json,
                            images_url = :images_url
                          WHERE id = :id');
        
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':name', $name);
        $this->db->bind(':description', $description);
        $this->db->bind(':sku', $sku);
        $this->db->bind(':regular_price', $regular_price);
        $this->db->bind(':sale_price', $sale_price);
        $this->db->bind(':woo_type', $woo_type);
        $this->db->bind(':stock', $stock, PDO::PARAM_INT);
        $this->db->bind(':min_stock_alert', $min_stock_alert, PDO::PARAM_INT);
        $this->db->bind(':attributes_json', $attributes_json);
        $this->db->bind(':images_url', $images_url);

        return $this->db->execute();
    }

    /**
     * Elimina un producto por su ID.
     * @param int $id ID del producto a eliminar.
     * @return bool
     */
    public function deleteProduct($id) {
        $this->db->query('DELETE FROM products WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Disminuye el stock de un producto.
     * @param int $id ID del producto.
     * @param int $quantity Cantidad a disminuir.
     * @return bool
     */
    public function decreaseStock($id, $quantity) {
        $this->db->query('UPDATE products SET stock = stock - :quantity WHERE id = :id');
        $this->db->bind(':quantity', $quantity, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    
    /**
     * Busca productos por nombre o SKU para la API.
     * @param string $searchTerm Término de búsqueda.
     * @return array
     */
    public function searchProducts($searchTerm) {
        $searchTerm = "%{$searchTerm}%";
        // CRÍTICO: La consulta debe ser correcta y las columnas deben existir
        $this->db->query('SELECT id, name, sku, regular_price as price, stock FROM products 
                          WHERE name LIKE :term OR sku LIKE :term
                          ORDER BY name ASC LIMIT 10');
        $this->db->bind(':term', $searchTerm);
        return $this->db->resultSet();
    }

    /**
     * Actualiza el ID de WooCommerce después de la creación del producto.
     * @param int $erpId ID del producto en el ERP.
     * @param int $wooId ID del producto en WooCommerce.
     * @return bool
     */
    public function updateWooId($erpId, $wooId) {
        $this->db->query('UPDATE products SET woo_id = :woo_id WHERE id = :id');
        $this->db->bind(':woo_id', $wooId, PDO::PARAM_INT);
        $this->db->bind(':id', $erpId, PDO::PARAM_INT);
        return $this->db->execute();
    }
}
?>
