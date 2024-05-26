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

    /**
     * Processes a checkout request and validates its integrity and product availability.
     * 
     * This function handles a POST request to initiate the checkout process. It validates the request method,
     * retrieves the request data, and analyzes the data for integrity and product availability. If any analysis
     * step fails, it returns an error response with a 400 status code. If all checks pass, it returns a success
     * response with the provided data.
     * 
     * @return ApiResponse The API response object containing the status and message.
     */
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

    /**
     * Handles the final confirmation of an order request.
     * 
     * This function processes a POST request to finalize an order. It extracts the order data from the request,
     * including restaurant ID, machine ID, total price, order items, and order status. It then adds the order to the
     * database and retrieves the newly created order ID. Subsequently, it adds the order items to the database.
     * If any errors occur during these operations, an appropriate error response is returned. On success, a confirmation
     * response with the order ID is returned.
     * 
     * @return ApiResponse The API response object with the result of the operation.
     */
    public function request_final_confirmation()
    {
        $response = new ApiResponse();
        $response->validate_request('POST');

        $data = $this->request->getJSON(true);

        // collect data from the request
        $id_restaurant = $data['id_restaurant'];
        $machine_id = $data['machine_id'];
        $total_price = $data['total_price'];
        $order_items = $data['order']['items'];
        $status = $data['order']['status'];

        // add order to database and get the id (order id)
        $api_model = new ApiModel($this->_get_project_id());

        // get the last order number from the active restaurant
        $order_number = $api_model->get_last_order_number($id_restaurant);

        // increment order number to preparate the nexty order
        $order_number++;

        $order_results = $api_model->add_order($id_restaurant, $machine_id, $total_price, $status, $order_number);

        // on error
        if ($order_results['status'] === 'error') {
            return $response->set_response_error(400, $order_results['message'], $this->_get_project_id());
        }

        // get order id
        $id_order = $order_results['id'];

        // add order items to database
        $order_items_results = $api_model->add_order_items($id_order, $order_items);

        // on error
        if ($order_items_results['status'] === 'error') {
            return $response->set_response_error(400, $order_items_results['message'], $this->_get_project_id());
        }

        // success
        return $response->set_response(200, 'success', [
            'id_order' => $id_order,
            'order_number' => $order_number
        ], $this->_get_project_id());
    }

    /**
     * Retrieves and responds with pending orders with a status of 'paid' for the current project.
     * 
     * This function validates a GET request and then uses the ApiModel to retrieve pending orders. 
     * If an error occurs during retrieval, it sets an error response. If successful, it sets a success 
     * response with the retrieved orders data.
     * 
     * @return ApiResponse The API response object with the result of the operation.
     */
    public function get_pending_orders()
    {
        $response = new ApiResponse();
        $response->validate_request('GET');

        $api_model = new ApiModel($this->_get_project_id());
        $results = $api_model->get_pending_orders();

        // on error
        if ($results['status'] === 'error') {
            return $response->set_response_error(400, $results['message'], $this->_get_project_id());
        }

        // success
        return $response->set_response(200, 'success', $results['data'], $this->_get_project_id());
    }

    /**
     * Retrieves detailed information about an order via API request.
     * 
     * This function handles a POST request to retrieve order details. It validates the request,
     * extracts the order ID from the JSON payload, and uses the ApiModel to fetch the order details.
     * If the request or the data retrieval fails, it returns an error response. On success, it returns
     * the order details.
     * 
     * @return ApiResponse The API response containing the status, message, and order details.
     */
    public function get_order_details()
    {
        $response = new ApiResponse();
        $response->validate_request('POST');

        $api_model = new ApiModel($this->_get_project_id());

        $data = $this->request->getJSON(true);
        if (empty($data)) {
            return $response->set_response_error(400, 'Invalid parameter', $this->_get_project_id());
        }

        $results = $api_model->get_order_details($data['id']);
        if ($results['status'] === 'error') {
            return $response->set_response_error(400, $results['message'], $this->_get_project_id());
        }

        // success
        return $response->set_response(200, 'success', $results['data'], $this->_get_project_id());
    }

    /**
     * Cancels and deletes an order through the API request.
     * 
     * This function sends a request to the API's 'delete_order' endpoint to cancel and delete
     * the order with the specified ID. It validates the request parameters, sends the request,
     * and handles the response. It returns a response indicating the success or failure of the
     * cancellation and deletion operation.
     * 
     * @return ApiResponse An instance of the ApiResponse class representing the response from
     *                      the API containing the status of the cancellation and deletion operation
     *                      and a corresponding message.
     */
    public function delete_order()
    {
        $response = new ApiResponse();
        $response->validate_request('POST');

        $api_model = new ApiModel($this->_get_project_id());

        $data = $this->request->getJSON(true);
        if (empty($data)) {
            return $response->set_response_error(400, 'Invalid parameter', $this->_get_project_id());
        }

        $results = $api_model->delete_order($data['id']);
        if ($results['status'] === 'error') {
            return $response->set_response_error(400, $results['message'], $this->_get_project_id());
        }

        // success
        return $response->set_response(200, 'success', [], $this->_get_project_id());
    }

    /**
     * Retrieves order details along with associated products.
     *
     * This method sends a POST request to the API's 'get_order_details_with_products' endpoint
     * to retrieve detailed information about an order, including its associated products. It validates
     * the request parameters, sends the request, and handles the response. If successful, it returns
     * an ApiResponse instance with a status of 200 indicating success, along with the order details
     * and associated products data. If there's an error, it returns an ApiResponse instance with a
     * status of 400 and an appropriate error message.
     *
     * @return ApiResponse An instance of the ApiResponse class representing the response from
     *                      the API containing the order details along with associated products
     *                      or an error message.
     */
    public function get_order_details_with_products()
    {
        $response = new ApiResponse();
        $response->validate_request('POST');

        $api_model = new ApiModel($this->_get_project_id());

        $data = $this->request->getJSON(true);
        if (empty($data)) {
            return $response->set_response_error(400, 'Invalid parameter', $this->_get_project_id());
        }

        $results = $api_model->get_order_details_with_products($data['id']);

        if ($results['status'] === 'error') {
            return $response->set_response_error(400, $results['message'], $this->_get_project_id());
        }

        // success
        return $response->set_response(200, 'success', $results['data'], $this->_get_project_id());
    }

    /**
     * Finalizes an order by processing product availability and updating the order status.
     * 
     * This function validates the incoming POST request to ensure it contains a valid order ID.
     * It then calls the `finish_order` method of the `ApiModel` to complete the order processing,
     * which includes checking product availability and updating the stock quantities.
     * The function returns an appropriate response based on the success or failure of the operation.
     * 
     * @return ApiResponse An instance of the ApiResponse class representing the response from
     *                      the API, containing the status of the operation and a corresponding message.
     */
    public function finish_order()
    {
        $response = new ApiResponse();
        $response->validate_request('POST');

        $api_model = new ApiModel($this->_get_project_id());

        $data = $this->request->getJSON(true);
        if (empty($data)) {
            return $response->set_response_error(400, 'Invalid parameter', $this->_get_project_id());
        }

        $results = $api_model->finish_order($data['id']);

        if ($results['status'] === 'error') {
            return $response->set_response_error(400, $results['message'], $this->_get_project_id());
        }

        // success
        return $response->set_response(200, 'success', $results['data'], $this->_get_project_id());
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
