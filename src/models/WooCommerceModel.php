<?php
// Modelo para la interacción con la API de WooCommerce
// Nota: Para una solución en producción, se recomienda usar un SDK oficial (ej. Automattic/WooCommerce-Rest-Api)
// Pero para el propósito de este proyecto, usaremos cURL de forma simple.

class WooCommerceModel {
    private $api_url;
    private $consumer_key;
    private $consumer_secret;

    public function __construct() {
        $this->api_url = WOO_URL . '/wp-json/wc/v3/';
        $this->consumer_key = WOO_CONSUMER_KEY;
        $this->consumer_secret = WOO_CONSUMER_SECRET;
    }

    // Método privado genérico para realizar peticiones API
    private function makeApiCall($endpoint, $method = 'GET', $data = []) {
        $ch = curl_init();
        $url = $this->api_url . $endpoint;

        // Autenticación básica (usando Consumer Key y Secret)
        $auth = $this->consumer_key . ':' . $this->consumer_secret;

        $headers = [
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_USERPWD, $auth);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Para entornos de desarrollo: desactivar verificación SSL (quitar en producción)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        
        if ($http_code < 200 || $http_code >= 300) {
            // Manejo de error de API
            error_log("WooCommerce API Error (HTTP $http_code): " . ($result['message'] ?? 'Error desconocido'));
            return ['error' => true, 'message' => $result['message'] ?? 'Error desconocido de API', 'http_code' => $http_code];
        }

        return $result;
    }

    // ----------------------------------------------------
    // Endpoints específicos para productos
    // ----------------------------------------------------

    /**
     * Sube un nuevo producto a WooCommerce.
     * @param array $productData Datos del producto formateados para Woo.
     * @return array Respuesta de la API.
     */
    public function createProduct($productData) {
        return $this->makeApiCall('products', 'POST', $productData);
    }

    /**
     * Actualiza un producto existente en WooCommerce.
     * @param int $woo_id ID de WooCommerce.
     * @param array $productData Datos del producto formateados para Woo.
     * @return array Respuesta de la API.
     */
    public function updateProduct($woo_id, $productData) {
        return $this->makeApiCall("products/{$woo_id}", 'PUT', $productData);
    }

    // Puedes añadir más métodos: deleteProduct, getProduct, etc.
}