<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Main extends BaseController
{
    /**
     * Displays the dashboard home page.
     * 
     * This method is responsible for displaying the home page of the dashboard in the backoffice.
     * It renders the dashboard home view.
     * 
     * @return View The dashboard home view.
     */
    public function index()
    {
        return view('dashboard/home');
    }
}
