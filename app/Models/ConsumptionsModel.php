<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsumptionsModel extends Model
{
    /**
     * Retrieves consumption data for products of the current restaurant, with optional date and category filtering.
     * 
     * This method connects to the database and retrieves all products for the restaurant associated with the 
     * current session user. It gathers the total quantities of each product from finished orders, combining this 
     * data with the product information. If a date interval filter is provided, it only includes orders within 
     * the specified date range. Additionally, if a category filter is provided, it only includes products 
     * belonging to the specified category. The final result includes a list of products with their respective 
     * consumption quantities.
     * 
     * @param string|null $filter_category Optional. A string representing the category to filter the products. 
     *                                     If set to 'all', no category filtering is applied.
     * @param array|null  $filter_date_interval Optional. An associative array with 'start_date' and 'end_date' 
     *                                           keys as DateTime objects to filter the orders within this date range.
     * 
     * @return array An array of products with consumption data. Each product includes all its attributes and a 
     *               'quantity' key representing the total quantity consumed from finished orders within the 
     *               specified date range and category.
     */
    public function get_consumptions($filter_category, $filter_date_interval)
    {
        $db = \Config\Database::connect();

        $params = [
            'id_restaurant' => session()->user['id_restaurant'],
        ];

        // filter date interval
        if (!empty($filter_date_interval)) {
            $params['start_date'] = $filter_date_interval['start_date']->format('Y-m-d H:i:s');
            $params['end_date'] = $filter_date_interval['end_date']->format('Y-m-d H:i:s');
        }

        // filter category
        if ($filter_category !== 'all') {
            $params['category'] = $filter_category;
        }

        // get all products from the restaurant
        $sql = "
            SELECT *
            FROM products
            WHERE id_restaurant = :id_restaurant:
        ";

        // filters - category
        if ($filter_category !== 'all') {
            $sql .= "AND category = :category:";
        }

        $sql .= " ORDER BY id ASC";

        $restaurant_products = $db->query($sql, $params)->getResultArray();

        // get all products from finished orders
        $sql = "
            SELECT products.id, SUM(order_products.quantity) as quantity
            FROM products
            LEFT JOIN order_products ON products.id = order_products.id_product
            LEFT JOIN orders ON orders.id = order_products.id_order
            WHERE orders.id_restaurant = :id_restaurant: 
            AND orders.order_status = 'finished'
        ";

        // filters - date interval
        if (!empty($filter_date_interval)) {
            $sql .= " AND orders.order_date BETWEEN :start_date: AND :end_date:";
        }

        // filters - category
        if ($filter_category !== 'all') {
            $sql .= "AND products.category = :category:";
        }

        $sql .= " GROUP BY products.id";

        $orders_results = $db->query($sql, $params)->getResultArray();

        // prepare results
        $results = [];
        foreach ($restaurant_products as $product) {
            $temp = $product;
            $temp['quantity'] = 0;
            foreach ($orders_results as $order) {
                if ($order['id'] === $product['id']) {
                    $temp['quantity'] += $order['quantity'];
                }
            }
            $results[] = $temp;
        }

        return $results;
    }

    /**
     * Retrieves distinct product categories for the current restaurant.
     * 
     * This method connects to the database and retrieves a list of distinct 
     * product categories for the restaurant associated with the current session 
     * user. It only includes categories for products that have not been deleted 
     * and sorts the categories in ascending order.
     * 
     * @return array An array of distinct categories for the restaurant's products.
     */
    public function get_categories()
    {
        $db = \Config\Database::connect();

        $params = [
            'id_restaurant' => session()->user['id_restaurant'],
        ];

        $sql = "
            SELECT DISTINCT category
            FROM products
            WHERE id_restaurant = :id_restaurant:
            AND deleted_at IS NULL
            ORDER BY category ASC
        ";

        return $db->query($sql, $params)->getResultArray();
    }

    /**
     * Retrieves daily consumption data for a specific product in the current restaurant.
     * 
     * This method connects to the database and retrieves the daily consumption quantities 
     * of the specified product from finished orders within the current restaurant. It groups 
     * the consumption data by date and returns an array where each element represents a day, 
     * containing the date and the total quantity of the specified product consumed on that day.
     * 
     * @param int $id_product The ID of the product to retrieve consumption data for.
     * 
     * @return array An array of consumption data grouped by date. Each element of the array 
     *               represents a day and includes the date in 'Y-m-d' format and the total 
     *               quantity of the specified product consumed on that day.
     */
    public function get_consumptions_by_id($id_product)
    {
        $db = \Config\Database::connect();

        $params = [
            'id_restaurant' => session('user')['id_restaurant'],
            'id_product'    => $id_product
        ];

        $sql = "
            SELECT
                DATE_FORMAT(orders.order_date, '%Y-%m-%d') as order_date,
                SUM(order_products.quantity) as quantity
            FROM products
            LEFT JOIN order_products ON products.id = order_products.id_product
            LEFT JOIN orders ON orders.id = order_products.id_order
            WHERE orders.id_restaurant = :id_restaurant:
            AND products.id = :id_product:
            GROUP BY DATE_FORMAT(orders.order_date, '%Y-%m-%d')
        ";

        $results = $db->query($sql, $params)->getResultArray();

        return $results;
    }
}
