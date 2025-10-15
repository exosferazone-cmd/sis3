<?php
// Simple router PHP para enrutar peticiones a controladores y métodos
class Router {
    private $routes = [];

    // Almacena una ruta GET
    public function get($uri, $action) {
        $this->routes['GET'][$uri] = $action;
    }

    // Almacena una ruta POST
    public function post($uri, $action) {
        $this->routes['POST'][$uri] = $action;
    }

    /**
     * Despacha la petición al controlador y método correctos.
     * Ahora soporta rutas con sufijos variables (ej: /ventas/details)
     */
    public function dispatch() {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        // Soporte para rutas con sufijos variables (ej: /ventas/details)
        if (isset($this->routes[$method][$uri])) {
            $action = $this->routes[$method][$uri];
        } else {
            // Buscar ruta por prefijo (ej: /ventas/details)
            $action = null;
            foreach ($this->routes[$method] as $route => $act) {
                if (strpos($uri, $route) === 0) {
                    $action = $act;
                    break;
                }
            }
        }

        if ($action) {
            list($controller, $methodName) = explode('@', $action);
            $controllerFile = SRC_PATH . '/controllers/' . $controller . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerInstance = new $controller();
                if (method_exists($controllerInstance, $methodName)) {
                    $controllerInstance->$methodName();
                } else {
                    $this->handleNotFound();
                }
            } else {
                $this->handleNotFound();
            }
        } else {
            $this->handleNotFound();
        }
    }

    // Maneja las rutas no encontradas
    private function handleNotFound() {
        http_response_code(404);
        echo "404 - Página no encontrada.";
    }
}
?>