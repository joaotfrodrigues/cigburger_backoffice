<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StocksSeeder extends Seeder
{
    /**
     * Seeds the 'stocks' table with fake data.
     * 
     * This method populates the 'stocks' table with fake data for stock movements of various products. It includes both incoming and outgoing stock movements, with details such as product ID, stock quantity, stock in/out status, supplier, reason for movement, movement date, and creation timestamp.
     * 
     * @return void
     */
    public function run()
    {
        // add fake data to stocks table
        $data = [
            [
                'id_product' => 13, // coffee
                'stock_quantity' => 1000,
                'stock_in_out' => 'IN',
                'stock_supplier' => 'CafeeTop',
                'reason' => '',
                'movement_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_product' => 14, // cig coca
                'stock_quantity' => 1000,
                'stock_in_out' => 'IN',
                'stock_supplier' => 'Coca Cola',
                'reason' => '',
                'movement_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_product' => 13,
                'stock_quantity' => 1000,
                'stock_in_out' => 'IN',
                'stock_supplier' => 'CafeeTop',
                'reason' => '',
                'movement_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_product' => 14,
                'stock_quantity' => 1000,
                'stock_in_out' => 'IN',
                'stock_supplier' => 'Coca Cola',
                'reason' => '',
                'movement_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_product' => 13,
                'stock_quantity' => 800,
                'stock_in_out' => 'IN',
                'stock_supplier' => 'Desconhecido',
                'reason' => '',
                'movement_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_product' => 100,    // this product does not exist
                'stock_quantity' => 2000,
                'stock_in_out' => 'IN',
                'stock_supplier' => 'Starbucks',
                'reason' => '',
                'movement_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],

            // the next stock movements are OUT
            [
                'id_product' => 1,  // coffee - removed 20 units out of date for consumption
                'stock_quantity' => 20,
                'stock_in_out' => 'OUT',
                'stock_supplier' => 'Owner',
                'reason' => 'Out of date',
                'movement_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_product' => 1,  // coffee - removing 5 units out of date for consumption
                'stock_quantity' => 5,
                'stock_in_out' => 'OUT',
                'stock_supplier' => 'Owner',
                'reason' => 'Out of date',
                'movement_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        $this->db->table('stocks')->insertBatch($data);
    }
}
