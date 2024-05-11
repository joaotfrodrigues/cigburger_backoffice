<?php

namespace App\Models;

use App\Libraries\ApiResponse;
use CodeIgniter\Model;
use Config\Database;

class ApiModel extends Model
{
    private $project_id;

    public function __construct($project_id)
    {
        $this->project_id = $project_id;
    }

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
                SELECT *
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
