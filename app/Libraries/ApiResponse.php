<?php

namespace App\Libraries;

class ApiResponse
{
    /**
     * Constructor function for the APIResponse class.
     * Checks if the API is active and terminates script if not.
     * 
     * @return void
     */
    public function __construct()
    {
        // check if API is active
        if (!API_ACTIVE) {
            echo $this->_api_not_active();
            die(1);
        }
    }

    /**
     * Validates the request method against the specified method.
     *
     * @param string $method The expected request method (e.g., 'GET', 'POST').
     * 
     * @return void
     */
    public function validate_request($method)
    {
        // validate request method
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
            echo $this->set_response_error(400, 'Invalid request method');
            die(1);
        }
    }

    /**
     * Sets the API response structure.
     *
     * @param int    $status      The HTTP status code (default: 200).
     * @param string $message     The response message (default: 'success').
     * @param array  $data        The response data (default: []).
     * @param mixed  $project_id  The project ID associated with the response (default: null).
     *
     * @return string The JSON-encoded API response.
     */
    public function set_response($status = 200, $message = 'success', $data = [], $project_id = null)
    {
        response()->setContentType('application/json');
        return json_encode([
            'status' => $status,
            'message' => $message,
            'info' => [
                'version' => API_VERSION,
                'datetime' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
                'project_id' => $project_id
            ],
            'data' => $data
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Sets the API response structure for an error.
     *
     * @param int    $status      The HTTP status code (default: 404).
     * @param string $message     The error message (default: 'error').
     * @param mixed  $project_id  The project ID associated with the error (default: null).
     *
     * @return string The JSON-encoded API error response.
     */
    public function set_response_error($status = 404, $message = 'error', $project_id = null)
    {
        response()->setContentType('application/json');
        return json_encode([
            'status' => $status,
            'message' => $message,
            'info' => [
                'version' => API_VERSION,
                'datetime' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
                'project_id' => $project_id
            ]
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Generates the API response for an inactive API.
     *
     * @return string The JSON-encoded API response indicating that the API is not active.
     */
    private function _api_not_active()
    {
        response()->setContentType('application/json');
        return json_encode([
            'status' => 400,
            'message' => 'API is not active',
            'info' => [
                'version' => API_VERSION,
                'datetime' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
                'project_id' => null
            ]
        ], JSON_PRETTY_PRINT);
    }
}
