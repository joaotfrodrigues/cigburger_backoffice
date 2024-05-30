<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    public function get_sales($filter_date_interval = null)
    {
        $db = \Config\Database::connect();

        $params = [
            'id_restaurant' => session('user')['id_restaurant']
        ];

        $sql = "
            SELECT 
                DATE(order_date) as order_date,
                CONCAT(SUM(total_price), '€') as total_price
            FROM orders
            WHERE id_restaurant = :id_restaurant:
            AND order_status = 'finished'
            AND deleted_at IS NULL
        ";

        // date interval filter
        if (!empty($filter_date_interval)) {
            $params['start_date'] = $filter_date_interval['start_date']->format('Y-m-d H:i-s');
            $params['end_date'] = $filter_date_interval['end_date']->format('Y-m-d H:i-s');

            $sql .= " AND order_date BETWEEN :start_date: AND :end_date:";
        }

        // group by day
        $sql .= " GROUP BY DATE(order_date)";

        $results = $db->query($sql, $params)->getResult();

        return $results;
    }
}
