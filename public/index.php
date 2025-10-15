<?php
// Carga de la configuración y dependencias
require_once __DIR__ . '/../config/config.php';

// CARGA MANUAL DE CLASES BASE (CRÍTICO para evitar "Class 'Database' not found")
require_once SRC_PATH . '/utils/Database.php';
require_once SRC_PATH . '/utils/Helpers.php';
require_once VENDOR_PATH . '/php-router/Router.php';

// Inicializa el router
$router = new Router();

// Definición de rutas - AUTENTICACIÓN
$router->get('/', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Definición de rutas - DASHBOARD
$router->get('/admin', 'AdminController@showDashboard');

// --------------------------------------------------
// Definición de rutas - Módulo de Clientes y Proveedores
// --------------------------------------------------
$router->get('/clientes', 'CustomerController@showList');
$router->get('/clientes/detail', 'CustomerController@showDetail');
$router->post('/clientes/add', 'CustomerController@addCustomer');
$router->post('/clientes/edit', 'CustomerController@editCustomer');
$router->post('/clientes/delete', 'CustomerController@deleteCustomer'); 

$router->get('/proveedores', 'SupplierController@showList');
$router->post('/proveedores/add', 'SupplierController@addSupplier');
$router->post('/proveedores/edit', 'SupplierController@editSupplier');
$router->get('/proveedores/delete', 'SupplierController@deleteSupplier');


// --------------------------------------------------
// Definición de rutas - Módulo de Productos (Inventario)
// --------------------------------------------------
$router->get('/productos', 'ProductController@showList');
$router->get('/productos/detail', 'ProductController@showDetail');
$router->post('/productos/add', 'ProductController@addProduct');
$router->post('/productos/edit', 'ProductController@editProduct');
$router->get('/productos/delete', 'ProductController@deleteProduct');

// --------------------------------------------------
// Definición de rutas - Módulo de Ventas
// --------------------------------------------------
$router->get('/ventas', 'SaleController@showList');
$router->get('/ventas/create', 'SaleController@showCreateForm');
$router->post('/ventas/save', 'SaleController@saveSale');
$router->get('/ventas/details', 'SaleController@showDetails'); 

// --------------------------------------------------
// Definición de rutas - Módulo de Caja y Finanzas
// --------------------------------------------------
$router->get('/caja', 'FinanceController@showCashRegister');
$router->post('/caja/add-transaction', 'FinanceController@addTransaction');

// --------------------------------------------------
// Definición de rutas - Módulo de Reportes y Canales
// --------------------------------------------------
$router->get('/reportes', 'ReportController@showDashboard');
$router->get('/canales', 'ChannelController@showList');
$router->post('/canales/add', 'ChannelController@addChannel');
$router->post('/canales/edit', 'ChannelController@editChannel');
$router->post('/canales/add-cost', 'ChannelController@addCost');
$router->get('/canales/delete', 'ChannelController@deleteChannel');


// 🚨 RUTAS API ELIMINADAS

// Despacha la ruta
$router->dispatch();

?>