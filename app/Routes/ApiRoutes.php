<?php

namespace App\Routes;

use Config\Services;

$routes = Services::routes();

// temporary route
$routes->get('/create_api_credentials/(:alphanum)/(:alphanum)', 'Api::create_api_credentials/$1/$2');

// api routes
$routes->get('/api/get_status', 'Api::api_status');
