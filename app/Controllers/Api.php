<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ApiResponse;
use App\Models\ApiModel;
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

    private function _get_project_id()
    {
        $header_credentials = $this->request->getHeaderLine('X-API-CREDENTIALS');

        $encrypter = \Config\Services::encrypter();
        $credentials = json_decode($encrypter->decrypt(hex2bin($header_credentials)), true);

        return $credentials['project_id'];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // API METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public function api_status()
    {
        $response = new ApiResponse();
        $response->validate_request('GET');
        return $response->set_response(200, 'success', [], $this->_get_project_id());
    }

    public function get_restaurant_details()
    {
        $response = new ApiResponse();
        $response->validate_request('GET');

        $api_model = new ApiModel($this->_get_project_id());

        return $response->set_response(
            200,
            'success',
            $api_model->get_restaurant_details(),
            $this->_get_project_id()
        );
    }
}
