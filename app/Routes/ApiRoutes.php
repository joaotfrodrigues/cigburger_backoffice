<?php

namespace App\Routes;

use Config\Services;

$routes = Services::routes();

// api routes
$routes->get('/api/get_status', 'Api::api_status'); 