<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ApiResponse;
use App\Models\ApiModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    /**
     * Creates API credentials for a project.
     * 
     * This function generates API credentials for a project by accepting a project ID
     * and API key as parameters. It first checks if the parameters are valid and then
     * displays the project ID, API key, and hashed API key. It then generates encrypted
     * credentials using the Encrypter service provided by CodeIgniter and displays the
     * encrypted data as a hexadecimal string.
     * 
     * @param string $project_id The project ID for which API credentials are to be created.
     * @param string $api_key The API key to be associated with the project.
     * 
     * @return void
     */
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

    // -----------------------------------------------------------------------------------------------------------------
    // API METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Retrieves the API status.
     * 
     * This API method retrieves the status of the API. It first validates the request
     * method to ensure it's a GET request. Then, it generates a response using the ApiResponse
     * class, setting the status code to 200 and the message to 'success'. Additional data
     * can be included in the response, such as the project ID retrieved from the API credentials.
     * 
     * @return array The API response containing the status information.
     */
    public function api_status()
    {
        $response = new ApiResponse();
        $response->validate_request('GET');
        return $response->set_response(200, 'success', [], $this->_get_project_id());
    }

    /**
     * Retrieves restaurant details from the CigBurger API.
     * 
     * This API method retrieves detailed information about the restaurant from the
     * CigBurger API. It first validates the request method to ensure it's a GET request.
     * Then, it initializes an instance of the ApiModel class with the project ID retrieved
     * from the API credentials. It calls the `get_restaurant_details` method of the ApiModel
     * class to fetch the restaurant details. Finally, it generates a response using the
     * ApiResponse class, setting the status code to 200, the message to 'success', and
     * including the retrieved restaurant details in the response.
     * 
     * @return array The API response containing the restaurant details.
     */
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

    public function request_checkout()
    {
        $response = new ApiResponse();
        $response->validate_request('POST');

        // respond the same data that was sent
        $data = $this->request->getJSON(true);

        // analyze request data for data integrity
        $analysis = $this->_analyse_request_data($data);
        if ($analysis['status'] === 'error') {
            return $response->set_response_error(
                400,
                $analysis['message'],
                $this->_get_project_id()
            );
        }

        // analyse request data for products availability
        $analysis = $this->_analyse_order_products_availability($data);
        if ($analysis['status'] === 'error') {
            return $response->set_response_error(
                400,
                $analysis['message'],
                $this->_get_project_id()
            );
        }

        // on analysis success
        return $response->set_response(
            200,
            'success',
            $data,
            $this->_get_project_id()
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PRIVATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Retrieves the project ID from the API credentials provided in the request header.
     * 
     * This function extracts the project ID from the API credentials provided in the
     * request header. It decrypts the credentials using the Encrypter service provided
     * by CodeIgniter, extracts the project ID from the decrypted data, and returns it.
     * 
     * @return string|null The project ID extracted from the API credentials, or null if not found.
     */
    private function _get_project_id()
    {
        $header_credentials = $this->request->getHeaderLine('X-API-CREDENTIALS');

        $encrypter = \Config\Services::encrypter();
        $credentials = json_decode($encrypter->decrypt(hex2bin($header_credentials)), true);

        return $credentials['project_id'];
    }

    /**
     * Analyzes and validates the provided request data.
     * 
     * This function checks if the required fields are present and valid in the provided data array. It ensures that
     * the request data contains the 'restaurant_id', 'order.items', 'order.status', and 'machine_id'. It also verifies
     * that the 'order.status' is set to 'paid'. If any of these checks fail, it returns an error status with an appropriate
     * message. If all checks pass, it returns a success status.
     * 
     * @param array $data The request data to be analyzed.
     * 
     * @return array An associative array with 'status' and 'message' indicating the result of the analysis.
     */
    private function _analyse_request_data($data)
    {
        // restaurant id
        if (!isset($data['restaurant_id'])) {
            return [
                'status' => 'error',
                'message' => 'restaurant_id is mandatory'
            ];
        }

        // check if order contains items collection
        if (!isset($data['order']['items'])) {
            return [
                'status' => 'error',
                'message' => 'order.items is mandatory'
            ];
        }

        // check if order contains status
        if (!isset($data['order']['status'])) {
            return [
                'status' => 'error',
                'message' => 'order.status is mandatory'
            ];
        }

        // check if order contains status is paid
        if ($data['order']['status'] !== 'paid') {
            return [
                'status' => 'error',
                'message' => 'order must be paid'
            ];
        }

        // check if order contains machine_id
        if (!isset($data['machine_id'])) {
            return [
                'status' => 'error',
                'message' => 'machine_id is mandatory'
            ];
        }

        // everything is ok
        return [
            'status' => 'success',
            'message' => 'success'
        ];
    }

    /**
     * Analyzes the availability of order products.
     * 
     * This function retrieves the availability of the products in the order by calling an API model. It prepares the order
     * products data from the input array and checks their availability against the data retrieved from the database.
     * 
     * @param array $data The input data containing order items.
     * 
     * @return array The availability results of the order products from the database.
     */
    private function _analyse_order_products_availability($data)
    {
        $api_model = new ApiModel($this->_get_project_id());

        // get order products from api request
        $order_products = [];
        foreach ($data['order']['items'] as $id => $item) {
            $order_products[] = [
                'id_product' => $id,
                'quantity' => $item['quantity']
            ];
        }

        // get products from database
        $results = $api_model->get_products_availability($order_products);

        return $results;
    }
}
