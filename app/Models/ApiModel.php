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

    /**
     * Checks the availability of products in the database.
     * 
     * This function takes an array of product data (including product IDs and quantities) and checks their availability
     * against the product data stored in the database. It verifies if the products exist, if they are available,
     * and if the requested quantities can be fulfilled. It returns a status indicating the result of the check.
     * 
     * @param array $data The array of product data, where each product includes 'id_product' and 'quantity'.
     * 
     * @return array An associative array containing the status ('success' or 'error') and a message.
     */
    public function get_products_availability($data)
    {
        // get key->value pair of products (id->quantity)
        $products = [];
        foreach ($data as $product) {
            $id = $product['id_product'];
            $quantity = $product['quantity'];

            $products[$id] = $quantity;
        }

        // create a string with all the products ids
        $products_ids = implode(',', array_keys($products));

        // get products from database
        try {
            $db = Database::connect();
            $results = $db->query(
                "SELECT *
                FROM products
                WHERE id IN ($products_ids)
                AND availability = 1
                AND stock > stock_min_limit
                AND deleted_at IS NULL"
            )->getResult();

            // check if the total products is equal to the total products in the order
            if (count($results) !== count($products)) {
                return [
                    'status' => 'error',
                    'message' => 'Some products are not available'
                ];
            }

            // check if the quantity of each product is available 
            foreach ($results as $product) {
                $quantity = $products[$id];
                if ($product->stock - $quantity <= $product->stock_min_limit) {
                    return [
                        'status' => 'error',
                        'message' => 'Some products have not enough stock'
                    ];
                }
            }

            // all products are available
            return [
                'status' => 'success',
                'message' => 'All products are available'
            ];
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Adds a new order to the 'orders' table.
     * 
     * This function inserts a new order record into the 'orders' table in the database. It includes the restaurant ID,
     * machine ID, total price, status, and timestamps for the order date and creation time. This method only adds the
     * order itself, not the individual order items, which should be handled separately.
     * 
     * @param int $id_restaurant The ID of the restaurant placing the order.
     * @param string $machine_id The ID of the machine associated with the order.
     * @param int $order_number The order number.
     * @param float $total_price The total price of the order.
     * @param string $status The status of the order.
     * 
     * @return array An associative array containing the status of the operation, a message, and the ID of the newly inserted order.
     */
    public function add_order($id_restaurant, $machine_id, $total_price, $status, $order_number)
    {
        try {
            $db = Database::connect();

            $data = [
                'id_restaurant' => $id_restaurant,
                'machine_id' => $machine_id,
                'order_number' => $order_number,
                'order_date' => date('Y-m-d H:i:s'),
                'order_status' => $status,
                'total_price' => $total_price,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $db->table('orders')->insert($data);

            return [
                'status' => 'success',
                'message' => 'Order added successfully',
                'id' => $db->insertID(),
                'order_number' => $order_number
            ];
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Adds order items to the database for a given order.
     * 
     * This function inserts multiple order items into the 'order_products' table in the database.
     * It accepts an order ID and an array of order items, each containing product ID, price per unit, and quantity.
     * The function constructs the necessary data array and performs a batch insert. If the insertion is successful,
     * it returns a success status. If an error occurs during the database operation, it catches the exception
     * and returns an error status with the exception message.
     * 
     * @param int $order_id The ID of the order to which the items belong.
     * @param array $order_items An associative array of order items, with product IDs as keys and arrays containing 'price' and 'quantity' as values.
     * 
     * @return array An array containing the status ('success' or 'error') and a message.
     */
    public function add_order_items($order_id, $order_items)
    {
        try {
            $db = Database::connect();

            $data = [];
            foreach ($order_items as $id_product => $item) {
                $data[] = [
                    'id_order' => $order_id,
                    'id_product' => $id_product,
                    'price_per_unit' => $item['price'],
                    'quantity' => $item['quantity'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }

            $db->table('order_products')->insertBatch($data);

            return [
                'status' => 'success',
                'message' => 'Order items added successfully'
            ];
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Retrieves pending orders with a status of 'paid' for the current project.
     * 
     * This function connects to the database and fetches orders with a status of 'paid' that belong to the 
     * current project and have not been deleted. It joins the orders with the restaurants table to ensure 
     * the orders belong to the correct project. If successful, it returns a success status and the retrieved 
     * orders. If an error occurs, it catches the database exception and returns an error status with the 
     * exception message.
     * 
     * @return array The result of the operation, including status, message, and data (if successful).
     */
    public function get_pending_orders()
    {
        try {
            $db = Database::connect();

            $params = [
                'project_id' => $this->project_id
            ];
            $results = $db->query("
                SELECT orders.*, SUM(order_products.quantity) as total_items
                FROM orders
                INNER JOIN restaurants ON orders.id_restaurant = restaurants.id
                INNER JOIN order_products ON orders.id = order_products.id_order
                WHERE orders.order_status = 'paid'
                AND restaurants.project_id = :project_id:
                AND orders.deleted_at IS NULL
                AND order_products.deleted_at IS NULL
                GROUP BY orders.id
            ", $params)->getResult();

            return [
                'status' => 'success',
                'message' => 'Orders retrieved successfully',
                'data' => $results
            ];
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function get_last_order_number($id_restaurant)
    {
        try {
            $db = Database::connect();

            $params = [
                'id_restaurant' => $id_restaurant
            ];

            $results = $db->query("
                SELECT order_number
                FROM orders
                WHERE id_restaurant = :id_restaurant:
                ORDER BY order_number DESC
                LIMIT 1
            ", $params)->getResult();

            if (count($results) === 0) {
                return 0;
            }
            return $results[0]->order_number;
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Retrieves detailed information about an order.
     * 
     * This function connects to the database and retrieves detailed information about an order,
     * including the total quantity of items in the order. It performs a query that joins the orders
     * and order_products tables, filtering by the order ID and excluding deleted orders.
     * 
     * @param int $id The ID of the order to retrieve.
     * 
     * @return array An associative array containing the status, message, and order details. 
     *               If successful, 'data' contains the order details; if not, 'message' contains the error message.
     */
    public function get_order_details($id)
    {
        try {
            $db = Database::connect();

            $params = [
                'id' => $id
            ];

            $results = $db->query("
                SELECT orders.*, SUM(order_products.quantity) as total_items
                FROM orders
                INNER JOIN order_products ON orders.id = order_products.id_order
                WHERE orders.id = :id:
                AND orders.deleted_at IS NULL
            ", $params)->getResult();

            return [
                'status' => 'success',
                'message' => 'Order retrieved successfully',
                'data' => $results[0]
            ];
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancels and deletes an order from the database.
     * 
     * This function performs a soft delete operation by setting the 'deleted_at' timestamp
     * for the specified order ID in the 'orders' table. Additionally, it updates the order
     * status to 'canceled'. It returns a response indicating the success or failure of the
     * cancellation and deletion operation.
     * 
     * @param int $id The ID of the order to be canceled and deleted.
     * 
     * @return array An array containing the status of the cancellation and deletion operation
     *               and a corresponding message.
     */
    public function delete_order($id)
    {
        try {
            $db = Database::connect();

            $params = [
                'id' => $id
            ];

            $results = $db->query("
                UPDATE orders
                SET deleted_at = NOW(), order_status = 'canceled'
                WHERE id = :id:
            ", $params);

            return [
                'status' => 'success',
                'message' => 'Order deleted successfully'
            ];
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
