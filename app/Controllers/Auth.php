<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function index()
    {
        return view('auth/login_frm');
    }

    public function teste()
    {
        return view('teste');
    }
}
