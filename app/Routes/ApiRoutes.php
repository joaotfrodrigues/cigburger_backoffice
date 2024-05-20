<?php

namespace App\Routes;

use App\Libraries\ApiResponse;
use Config\Services;

$routes = Services::routes();

// temporary route
$routes->get('/create_api_credentials/(:alphanum)/(:alphanum)',        'Api::create_api_credentials/$1/$2');

// api routes
$routes->get('/api/get_status',                                        'Api::api_status');
$routes->get('/api/get_restaurant_details',                            'Api::get_restaurant_details');

// api routes does not exists
$routes->set404Override(function () {
    $response = new ApiResponse;
    
    response()->setContentType('application/json');

    echo $response->set_response_error(404, 'Route does not exists');
});