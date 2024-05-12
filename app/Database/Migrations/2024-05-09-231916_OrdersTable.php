<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrdersTable extends Migration
{
    /**
     * Creates the 'orders' table.
     * 
     * This method defines the structure of the 'orders' table, including columns for storing order information such as ID, restaurant ID, order number, order date, order status, and timestamps for creation, update, and soft deletion.
     * 
     * @return void
     */
    public function up()
    {
        // create order table
        $this->forge->addField([
            'id' => [
                'type' => 'bigint',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_restaurant' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'order_number' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'order_date' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'order_status' => [
                'type' => 'varchar',
                'constraint' => 50,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'datetime',
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
        ]);

        // primary key
        $this->forge->addKey('id', true);

        // create table
        $this->forge->createTable('orders');
    }

    /**
     * Drops the 'orders' table.
     * 
     * This method removes the 'orders' table from the database, reverting the changes made during the migration.
     * 
     * @return void
     */
    public function down()
    {
        // drop table
        $this->forge->dropTable('orders');
    }
}
