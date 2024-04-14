<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login_frm');
    }

    public function login_submit()
    {
        echo 'login submit';
    }

    public function logout()
    {
        echo 'logout';
    }
}
