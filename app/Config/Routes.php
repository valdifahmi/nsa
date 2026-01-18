<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth Routes (No Filter)
$routes->get('auth/login', 'AuthController::login');
$routes->post('auth/processLogin', 'AuthController::processLogin');
$routes->get('auth/logout', 'AuthController::logout');

// Protected Routes (With Auth Filter)
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);

// Category Routes (With Auth Filter)
$routes->group('category', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CategoryController::index');
    $routes->get('fetchAll', 'CategoryController::fetchAll');
    $routes->post('store', 'CategoryController::store');
    $routes->get('edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('update/(:num)', 'CategoryController::update/$1');
    $routes->post('delete/(:num)', 'CategoryController::delete/$1');
});

$routes->group('brand', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'BrandController::index');
    $routes->get('fetchAll', 'BrandController::fetchAll');
    $routes->post('store', 'BrandController::store');
    $routes->get('edit/(:num)', 'BrandController::edit/$1');
    $routes->post('update/(:num)', 'BrandController::update/$1');
    $routes->post('delete/(:num)', 'BrandController::delete/$1');
});

// Supplier Routes (With Auth Filter)
$routes->group('supplier', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SupplierController::index');
    $routes->get('fetchSuppliers', 'SupplierController::fetchSuppliers');
    $routes->get('getSupplier/(:num)', 'SupplierController::getSupplier/$1');
    $routes->get('getForDropdown', 'SupplierController::getForDropdown');
    $routes->post('create', 'SupplierController::create');
    $routes->post('update/(:num)', 'SupplierController::update/$1');
    $routes->post('delete/(:num)', 'SupplierController::delete/$1');
});

// Client Routes (With Auth Filter)
$routes->group('client', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ClientController::index');
    $routes->get('fetchClients', 'ClientController::fetchClients');
    $routes->get('getClient/(:num)', 'ClientController::getClient/$1');
    $routes->get('getForDropdown', 'ClientController::getForDropdown');
    $routes->post('create', 'ClientController::create');
    $routes->post('update/(:num)', 'ClientController::update/$1');
    $routes->post('delete/(:num)', 'ClientController::delete/$1');
});

$routes->group('product', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProductController::index');
    $routes->get('fetchAll', 'ProductController::fetchAll');
    $routes->get('fetchDropdowns', 'ProductController::fetchDropdowns');
    $routes->get('findProductByCode', 'ProductController::findProductByCode');
    $routes->get('searchAutocomplete', 'ProductController::searchAutocomplete');
    $routes->post('store', 'ProductController::store');
    $routes->get('edit/(:num)', 'ProductController::edit/$1');
    $routes->post('update/(:num)', 'ProductController::update/$1');
    $routes->post('delete/(:num)', 'ProductController::delete/$1');
});

// Service Routes (With Auth Filter)
$routes->group('service', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ServiceController::index');
    $routes->get('fetchAll', 'ServiceController::fetchAll');
    $routes->post('store', 'ServiceController::store');
    $routes->get('edit/(:num)', 'ServiceController::edit/$1');
    $routes->post('update/(:num)', 'ServiceController::update/$1');
    $routes->post('delete/(:num)', 'ServiceController::delete/$1');
});

$routes->group('purchase', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PurchaseController::index');
    $routes->post('store', 'PurchaseController::store');
    $routes->get('findProductByCode', 'PurchaseController::findProductByCode');
    $routes->get('searchProducts', 'PurchaseController::searchProducts');
    $routes->get('list', 'PurchaseController::list');
    $routes->post('fetchList', 'PurchaseController::fetchList');
    $routes->post('updatePaymentStatus', 'PurchaseController::updatePaymentStatus');
    $routes->post('delete/(:num)', 'PurchaseController::delete/$1', ['filter' => 'admin']);
});

$routes->group('sale', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SaleController::index');
    $routes->get('list', 'SaleController::listTransactions');
    $routes->post('store', 'SaleController::store');

    // Work Order Routes
    $routes->get('updateWO/(:num)', 'SaleController::updateWO/$1');
    $routes->get('getWorkOrderDetail/(:num)', 'SaleController::getWorkOrderDetail/$1');
    $routes->post('addItemToWO', 'SaleController::addItemToWO');
    $routes->post('addServiceToWO', 'SaleController::addServiceToWO');
    $routes->post('finalizeWorkOrder', 'SaleController::finalizeWorkOrder');
});

// Invoice Routes (With Auth Filter)
$routes->group('invoice', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'InvoiceController::index');
    $routes->get('fetchInvoices', 'InvoiceController::fetchInvoices');
    $routes->get('getDetail/(:num)', 'InvoiceController::getDetail/$1');
    $routes->get('generatePDF/(:num)', 'InvoiceController::generatePDF/$1');
});

$routes->group('people', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'PeopleController::index');
    $routes->get('fetchAll', 'PeopleController::fetchAll');
    $routes->post('store', 'PeopleController::store');
    $routes->get('edit/(:num)', 'PeopleController::edit/$1');
    $routes->post('update/(:num)', 'PeopleController::update/$1');
    $routes->post('delete/(:num)', 'PeopleController::delete/$1');
});

// Pricing Routes (Admin Only)
$routes->group('pricing', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'PricingController::index');
    $routes->get('fetchAll', 'PricingController::fetchAll');
    $routes->post('updatePrice', 'PricingController::updatePrice');
    $routes->post('bulkMarkUp', 'PricingController::bulkMarkUp');
});

$routes->group('report', ['filter' => 'auth'], function ($routes) {
    $routes->get('stockReport', 'ReportController::stockReport');
    $routes->get('fetchStockReport', 'ReportController::fetchStockReport');
    $routes->get('productLog', 'ReportController::productLog');
    $routes->post('getLogBarang', 'ReportController::getLogBarang');
});

// Log Routes (Admin Only - With Auth and Admin Filter)
$routes->group('logs', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'LogController::index');
});

// Dashboard Routes (With Auth Filter)
$routes->group('dashboard', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->post('fetchData', 'DashboardController::fetchData');
    $routes->post('fetchWarehouseData', 'DashboardController::fetchWarehouseData');
    $routes->post('fetchWorkshopData', 'DashboardController::fetchWorkshopData');
});
