<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateOrdersTable extends Migration
{
    /**
     * Adds new columns to the 'orders' table.
     * 
     * This migration method adds two new columns to the 'orders' table:
     * - 'machine_id': A VARCHAR field with a maximum length of 50 characters, which can be null.
     * - 'total_price': A DECIMAL field with a precision of 10 and scale of 2, which can be null.
     * 
     * These columns are added after the 'id_restaurant' and 'order_status' columns respectively.
     * 
     * @return void
     */
    public function up()
    {
        // add columns
        $this->forge->addColumn('orders', [
            'machine_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'id_restaurant'
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'order_status'
            ]
        ]);
    }

    /**
     * Reverts the changes made to the 'orders' table.
     * 
     * This migration method removes the 'machine_id' and 'total_price' columns from the 'orders' table.
     * This method is used to undo the changes made by the `up` method in this migration.
     * 
     * @return void
     */
    public function down()
    {
        // remove columns
        $this->forge->dropColumn('orders', [
            'machine_id',
            'total_price'
        ]);
    }
}
