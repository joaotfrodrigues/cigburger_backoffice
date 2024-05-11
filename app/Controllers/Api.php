<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ApiResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    public function api_status()
    {
        $response = new ApiResponse();
        $response->validate_request('GET');
        return $response->set_response();
    }
}
