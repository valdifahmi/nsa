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
$routes->get('/', 'Home::index', ['filter' => 'auth']);

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

$routes->group('purchase', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PurchaseController::index');
    $routes->post('store', 'PurchaseController::store');
});

$routes->group('sale', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SaleController::index');
    $routes->post('store', 'SaleController::store');
});

$routes->group('people', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'PeopleController::index');
    $routes->get('fetchAll', 'PeopleController::fetchAll');
    $routes->post('store', 'PeopleController::store');
    $routes->get('edit/(:num)', 'PeopleController::edit/$1');
    $routes->post('update/(:num)', 'PeopleController::update/$1');
    $routes->post('delete/(:num)', 'PeopleController::delete/$1');
});

$routes->group('report', ['filter' => 'auth'], function ($routes) {
    $routes->get('purchaseReport', 'ReportController::purchaseReport');
    $routes->get('fetchPurchaseReport', 'ReportController::fetchPurchaseReport');
    $routes->get('purchaseItemReport', 'ReportController::purchaseItemReport');
    $routes->get('fetchPurchaseItemReport', 'ReportController::fetchPurchaseItemReport');
    $routes->get('saleReport', 'ReportController::saleReport');
    $routes->get('fetchSaleReport', 'ReportController::fetchSaleReport');
    $routes->get('saleItemReport', 'ReportController::saleItemReport');
    $routes->get('fetchSaleItemReport', 'ReportController::fetchSaleItemReport');
    $routes->get('stockReport', 'ReportController::stockReport');
    $routes->get('fetchStockReport', 'ReportController::fetchStockReport');
});

// Log Routes (Admin Only - With Auth and Admin Filter)
$routes->group('logs', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'LogController::index');
});
