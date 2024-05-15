<?php

namespace App\Models;

use App\Libraries\ApiResponse;
use CodeIgniter\Model;
use Config\Database;

class ApiModel extends Model
{
    private $project_id;

    /**
     * Constructs a new instance of the API model with the specified project ID.
     *
     * @param mixed $project_id The project ID associated with the API model.
     */
    public function __construct($project_id)
    {
        $this->project_id = $project_id;
    }

    /**
     * Handles SQL errors and generates appropriate API responses.
     * 
     * If API_DEBUG_LEVEL is 1, detailed error messages are included in the response.
     * If API_DEBUG_LEVEL is 0, a generic error message is included in the response.
     *
     * @param \Exception $error The SQL error object.
     */
    private function _sql_error($error)
    {
        if (API_DEBUG_LEVEL) {
            $response = new ApiResponse();
            die($response->set_response_error(500, $error->getMessage()));
        } else {
            $response = new ApiResponse();
            die($response->set_response_error(500, 'Internal Server Error'));
        }
    }

    /**
     * Retrieves details of the restaurant, including main information, categories, and products.
     * 
     * This method connects to the database and retrieves various details related to the restaurant,
     * such as its main information, categories of products available, and the list of products offered.
     * It queries the database using the provided project ID to filter results based on the specific project.
     * 
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException If an SQL error occurs during database queries.
     * 
     * @return array|null An array containing restaurant details, categories, and products,
     *                    or null in case of an SQL error.
     */
    public function get_restaurant_details()
    {
        try {
            $db = Database::connect();
            $data = [];

            $params = [
                'project_id' => $this->project_id
            ];

            // get restaurant main information
            $data['restaurant_details'] = $db->query("
                SELECT *
                FROM restaurants
                WHERE 1
                AND project_id = :project_id:
            ", $params)->getResult()[0];

            // get restaurant categories
            $data['products_categories'] = $db->query("
                SELECT DISTINCT(p.category) category
                FROM products AS p
                INNER JOIN restaurants AS r ON p.id_restaurant = r.id
                WHERE r.project_id = :project_id:
                AND p.deleted_at IS NULL
            ", $params)->getResult();

            // get restaurant products
            $data['products'] = $db->query("
                SELECT p.*
                FROM products AS p
                INNER JOIN restaurants AS r ON p.id_restaurant = r.id
                WHERE r.project_id = :project_id:
                AND p.deleted_at IS NULL
            ", $params)->getResult();

            return $data;
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $error) {
            return $this->_sql_error($error);
        }
    }
}
