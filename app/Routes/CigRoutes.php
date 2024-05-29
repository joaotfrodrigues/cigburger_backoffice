<?php

namespace App\Routes;

use Config\Services;

$routes = Services::routes();

// main
$routes->get('/',                                                     'Main::index');

// auth
$routes->get('/auth/login',                                           'Auth::login');
$routes->get('/auth/logout',                                          'Auth::logout');
$routes->post('/auth/login_submit',                                   'Auth::login_submit');

// products
$routes->get('/products',                                             'Products::index');
$routes->get('/products/new',                                         'Products::new_product');
$routes->post('/products/new_submit',                                 'Products::new_submit');

// edit product
$routes->get('/products/edit/(:alphanum)',                            'Products::edit/$1');
$routes->post('/products/edit_submit',                                'Products::edit_submit');

// delete product
$routes->get('/products/delete/(:alphanum)',                          'Products::delete/$1');
$routes->get('/products/delete_confirm/(:alphanum)',                  'Products::delete_confirm/$1');

// stock
$routes->get('/stocks',                                               'Stocks::index');
$routes->get('/stocks/add/(:alphanum)',                               'Stocks::add/$1');
$routes->get('/stocks/remove/(:alphanum)',                            'Stocks::remove/$1');
$routes->get('/stocks/movements/(:alphanum)',                         'Stocks::movements/$1');
$routes->get('/stocks/movements/(:alphanum)/(:alphanum)',             'Stocks::movements/$1/$2');
$routes->get('/stocks/export_csv/(:alphanum)',                        'Stocks::export_csv/$1');
$routes->post('/stocks/add_submit',                                   'Stocks::add_submit');
$routes->post('/stocks/remove_submit',                                'Stocks::remove_submit');

// api restaurant
$routes->get('/api_restaurant',                                       'ApiRestaurant::index');
$routes->get('/api_restaurant/download/(:alphanum)',                  'ApiRestaurant::download/$1');
$routes->get('/api_restaurant/create_new_machine',                    'ApiRestaurant::create_new_machine');
$routes->get('/api_restaurant/change_api_key',                        'ApiRestaurant::change_api_key');
