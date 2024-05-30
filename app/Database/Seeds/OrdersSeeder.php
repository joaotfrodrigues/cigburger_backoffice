<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use DateInterval;
use DateTime;

class OrdersSeeder extends Seeder
{
    /**
     * Generates sample data for orders and associated products.
     * 
     * This method generates sample data for orders and their associated products based on the provided configuration. 
     * It creates orders with random product selections, quantities, and statuses within the specified date range.
     * 
     * @return void
     */
    public function run()
    {
        // -------------------------------------------------------------------------------------------------------------
        // CONFIG
        // -------------------------------------------------------------------------------------------------------------

        // start id
        $id_start = 0;

        // total orders
        $total_orders = 5000;

        // max products per order
        $max_products_per_order = 5;

        // order start date
        $start_date = DateTime::createFromFormat('Y-m-d H:i:s', '2023-11-01 09:00:00');

        // possible status
        $possible_status = ['paid', 'canceled', 'finished'];

        // restaurant id
        $id_restaurant = 1;

        // start order number
        $order_number = 1;

        // remove previous data from table
        $truncate_old_data = true;

        // machine id's
        $machine_ids = [
            ['5V084O27', 'PZURTST9', '4I65YPFI'],
            ['2S205JSL', '8Z5JUCAD', 'M5K0DAZY']
        ];
        $machine_ids = $machine_ids[$id_restaurant - 1];

        // -------------------------------------------------------------------------------------------------------------
        if ($truncate_old_data) {
            $this->db->table('orders')->truncate();
            $this->db->table('order_products')->truncate();

            // get all restaurant products
            $products = $this->db->table('products')
                ->where('id_restaurant', $id_restaurant)
                ->get()
                ->getResult();

            // create orders
            for ($i = $id_start; $i < $id_start + $total_orders; $i++) {

                // define order products
                $total_products = rand(1, $max_products_per_order);
                $order_products = [];
                $unique_products = [];

                // define unique products
                while (count($unique_products) < $total_products) {

                    $product = $products[array_rand($products)];
                    if (!in_array($product, $unique_products)) {
                        $unique_products[] = $product;
                    }
                }

                for ($j = 0; $j < $total_products; $j++) {

                    $product = $unique_products[$j];
                    $order_products[] = [
                        'id_order'       => $i + 1,
                        'id_product'     => $product->id,
                        'quantity'       => rand(1, 5),
                        'price_per_unit' => $product->price,
                        'created_at'     => $start_date->format('Y-m-d H:i:s'),
                        'updated_at'     => null,
                        'deleted_at'     => null
                    ];
                }

                // total price
                $total_price = 0;
                foreach ($order_products as $order_product) {
                    $total_price += $order_product['price_per_unit'] * $order_product['quantity'];
                }

                // define order
                $order = [
                    'id'            => $i + 1,
                    'id_restaurant' => $id_restaurant,
                    'machine_id'    => $machine_ids[array_rand($machine_ids)],
                    'order_number'  => $order_number,
                    'order_date'    => $start_date->format('Y-m-d H:i:s'),
                    'order_status'  => $possible_status[array_rand($possible_status)],
                    'total_price'   => $total_price,
                    'created_at'    => $start_date->format('Y-m-d H:i:s'),
                    'updated_at'    => null,
                    'deleted_at'    => null
                ];

                $this->db->table('orders')->insert($order);
                $this->db->table('order_products')->insertBatch($order_products);

                // update start date
                $minutes_to_add = rand(1, 120); // 2 hours max between orders
                $start_date->add(new DateInterval('PT' . $minutes_to_add . 'M'));

                // update order number
                $order_number++;
            }
        }
    }
}
