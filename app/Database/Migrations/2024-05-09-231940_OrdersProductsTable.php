<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrdersProductsTable extends Migration
{
    /**
     * Creates the 'order_products' table.
     * 
     * This method defines the structure of the 'order_products' table, including columns for storing order product information such as ID, order ID, product ID, price per unit, quantity, and timestamps for creation, update, and soft deletion.
     * 
     * @return void
     */
    public function up()
    {
        // create table order_products
        $this->forge->addField([
            'id' => [
                'type' => 'bigint',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_order' => [
                'type' => 'bigint',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'id_product' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'price_per_unit' => [
                'type' => 'decimal',
                'constraint' => '10,2',
                'null' => true,
            ],
            'quantity' => [
                'type' => 'tinyint',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->createTable('order_products');
    }

    /**
     * Drops the 'order_products' table.
     * 
     * This method removes the 'order_products' table from the database, reverting the changes made during the migration.
     * 
     * @return void
     */
    public function down()
    {
        // drop table
        $this->forge->dropTable('order_products');
    }
}
