<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Main::index');

// auth
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login_submit', 'Auth::login_submit');
$routes->get('/auth/logout', 'Auth::logout');

// products
$routes->get('/products', 'Products::index');
$routes->get('/products/new', 'Products::new_product');
$routes->post('/products/new_submit', 'Products::new_submit');

// edit product
$routes->get('/products/edit/(:alphanum)', 'Products::edit/$1');
$routes->post('/products/edit_submit', 'Products::edit_submit');

// delete product
$routes->get('/products/delete/(:alphanum)', 'Products::delete/$1');
$routes->get('/products/delete_confirm/(:alphanum)', 'Products::delete_confirm/$1');

// stock
$routes->get('/stocks', 'Stocks::index');
$routes->get('/stocks/add/(:alphanum)', 'Stocks::add/$1');
$routes->post('/stocks/add_submit', 'Stocks::add_submit');
$routes->get('/stocks/remove/(:alphanum)', 'Stocks::remove/$1');
$routes->post('/stocks/remove_submit', 'Stocks::remove_submit');
$routes->get('/stocks/movements/(:alphanum)', 'Stocks::movements/$1');