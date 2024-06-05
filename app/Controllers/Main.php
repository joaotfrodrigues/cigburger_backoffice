<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RestaurantModel;
use CodeIgniter\HTTP\ResponseInterface;

class Main extends BaseController
{
    /**
     * Displays the dashboard home page.
     *
     * This method sets up the data needed for the dashboard home view and returns the view to be displayed.
     * It retrieves the restaurant information for the currently logged-in user's restaurant and passes it to the view.
     *
     * @return ResponseInterface The view for the dashboard home page.
     */
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'page'  => 'Dashboard'
        ];

        // get data for the home page
        $restaurant_model = new RestaurantModel();

        $id_restaurant = session('user')['id_restaurant'];

        $data['restaurant'] = $restaurant_model->find($id_restaurant);

        return view('dashboard/home', $data);
    }

    /**
     * Displays the "Access Denied" error page.
     *
     * This method sets up the data needed for the "Access Denied" view and returns the view to be displayed.
     * It is typically called when a user tries to access a resource they do not have permission to view.
     *
     * @return ResponseInterface The view for the "Access Denied" error page.
     */
    public function no_access_allowed()
    {
        $data = [
            'title' => 'Acesso negado',
            'page'  => ''
        ];

        return view('errors/no_access_allowed', $data);
    }
}
