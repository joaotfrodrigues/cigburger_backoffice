<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Main::index');

$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login_submit', 'Auth::login_submit');
$routes->get('/auth/logout', 'Auth::logout');