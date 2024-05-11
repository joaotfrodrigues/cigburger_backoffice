<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ApiResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    public function create_api_credentials($project_id, $api_key)
    {
        if (empty($project_id) || empty($api_key)) {
            echo 'Invalid parameters';
            return;
        }

        echo '<pre>';
        echo "project_id: $project_id<br>";
        echo "api_key: $api_key<br>";
        echo 'api_key (hash): ' . password_hash($api_key, PASSWORD_DEFAULT) . '<br>';

        $data = json_encode([
            'project_id' => $project_id,
            'api_key' => $api_key
        ]);

        $encrypter = \Config\Services::encrypter();
        $encrypted_data = bin2hex($encrypter->encrypt($data));

        echo $encrypted_data;
    }

    public function api_status()
    {
        $response = new ApiResponse();
        $response->validate_request('GET');
        return $response->set_response();
    }
}
