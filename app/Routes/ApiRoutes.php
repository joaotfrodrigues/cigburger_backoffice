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
$routes->get('/api/get_pending_orders',                                'Api::get_pending_orders');
$routes->post('/api/request_checkout',                                 'Api::request_checkout');
$routes->post('/api/request_final_confirmation',                       'Api::request_final_confirmation');
$routes->post('/api/get_order_details',                                'Api::get_order_details');
$routes->post('/api/delete_order',                                     'Api::delete_order');
$routes->post('/api/get_order_details_with_products',                  'Api::get_order_details_with_products');
$routes->post('/api/finish_order',                                     'Api::finish_order');

// api routes does not exists
$routes->set404Override(function () {
    $response = new ApiResponse;
    
    response()->setContentType('application/json');

    echo $response->set_response_error(404, 'Route does not exists');
});