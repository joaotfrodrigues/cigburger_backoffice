<?php

namespace App\Routes;

use App\Libraries\ApiResponse;
use Config\Services;

$routes = Services::routes();

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
    if (strpos(uri_string(), 'api/') !== false) {
        // api routes
        $response = new ApiResponse;
    
        echo $response->set_response_error(404, 'Route does not exists');
    
    } else {
        // other routes (CigRoutes)
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
});