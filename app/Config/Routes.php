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