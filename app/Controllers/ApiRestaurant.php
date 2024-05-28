<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ApiRestaurantModel;
use CodeIgniter\HTTP\ResponseInterface;

class ApiRestaurant extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'API',
            'page' => 'API'
        ];

        // get information from the restaurant API data
        $model = new ApiRestaurantModel();
        $id_restaurant = session('user')['id_restaurant'];

        $results = $model->select('project_id, api_key_openssl')
            ->where('id', $id_restaurant)
            ->first();

        $data['project_id'] = $results->project_id;
        $data['api_key_openssl'] = Decrypt($results->api_key_openssl);

        // get list of all cigrequest machines operating for this restaurant
        $data['machines'] = $model->get_machines_from_restaurant($id_restaurant);

        return view('dashboard/api/index', $data);
    }
}
