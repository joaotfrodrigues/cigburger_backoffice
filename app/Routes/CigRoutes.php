<?php

namespace App\Routes;

use Config\Services;

$routes = Services::routes();

// main
$routes->get('/',                                                     'Main::index');

// auth - GET
$routes->get('/auth/login',                                           'Auth::login');
$routes->get('/auth/logout',                                          'Auth::logout');
$routes->get('/auth/finish_registration/(:alphanum)',                 'Auth::finish_registration/$1');
$routes->get('/auth/define_password',                                 'Auth::define_password');
$routes->get('/auth/welcome',                                         'Auth::welcome');
$routes->get('/auth/profile',                                         'Auth::profile');
$routes->get('/auth/forgot_password',                                 'Auth::forgot_password');
$routes->get('/auth/redefine_password/(:alphanum)',                   'Auth::redefine_password/$1');

// auth - POST
$routes->post('/auth/profile_submit',                                 'Auth::profile_submit');
$routes->post('/auth/change_password_submit',                         'Auth::change_password_submit');
$routes->post('/auth/login_submit',                                   'Auth::login_submit');
$routes->post('/auth/define_password_submit',                         'Auth::define_password_submit');
$routes->post('/auth/forgot_password_submit',                         'Auth::forgot_password_submit');
$routes->post('/auth/redefine_password_submit',                       'Auth::redefine_password_submit');

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

// consumptions
$routes->get('/consumptions',                                         'Consumptions::index');
$routes->get('/consumptions/reset_date_interval',                     'Consumptions::reset_date_interval');
$routes->get('/consumptions/last_seven_days',                         'Consumptions::last_seven_days');
$routes->get('/consumptions/reset_all_filters',                       'Consumptions::reset_all_filters');
$routes->get('/consumptions/product_details/(:alphanum)',             'Consumptions::product_details/$1');
$routes->get('/consumptions/set_category/(:alphanum)',                'Consumptions::set_category/$1');
$routes->post('/consumptions/filter_date_interval',                   'Consumptions::filter_date_interval');

// sales
$routes->get('/sales',                                                 'Sales::index');
$routes->get('/sales/reset_date_interval',                             'Sales::reset_date_interval');
$routes->get('/sales/last_seven_days',                                 'Sales::last_seven_days');
$routes->post('/sales/filter_date_interval',                           'Sales::filter_date_interval');

// users management
$routes->get('/users_management',                                      'UsersManagement::index');
$routes->get('/users_management/new_user',                             'UsersManagement::new_user');
$routes->get('/users_management/edit/(:alphanum)',                     'UsersManagement::edit/$1');
$routes->get('/users_management/delete_user/(:alphanum)',              'UsersManagement::delete_user/$1');
$routes->get('/users_management/recover_user/(:alphanum)',             'UsersManagement::recover_user/$1');
$routes->post('/users_management/new_user_submit',                     'UsersManagement::new_user_submit');
$routes->post('/users_management/edit_user_submit',                    'UsersManagement::edit_user_submit');
