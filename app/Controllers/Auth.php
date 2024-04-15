<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RestaurantModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function login()
    {
        // load restaurants
        $restaurants_model = new RestaurantModel();
        $restaurants = $restaurants_model->select('id, name')->findAll();

        return view('auth/login_frm', [
            'restaurants' => $restaurants
        ]);
    }

    public function login_submit()
    {
        // show restaurant id
        $restaurant_id = Decrypt($this->request->getPost('select_restaurant'));
        dd($restaurant_id);
    }

    public function logout()
    {
        echo 'logout';
    }
}
