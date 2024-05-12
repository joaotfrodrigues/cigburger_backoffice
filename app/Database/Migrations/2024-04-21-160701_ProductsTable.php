<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductsTable extends Migration
{
    /**
     * Creates the 'products' table.
     * 
     * This method defines the structure of the 'products' table, including fields like name, description, category, price, availability, and others.
     * It sets default values for certain fields and creates the table in the database.
     * 
     * @return void
     */
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_restaurant' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0.00
            ],
            'availability' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
                'default' => 1
            ],
            'promotion' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'default' => 0.00
            ],
            'stock' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => true,
                'default' => 0
            ],
            'stock_min_limit' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 100
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        // add primary key
        $this->forge->addKey('id', true);

        // create table
        $this->forge->createTable('products');
    }

    /**
     * Drops the 'products' table.
     * 
     * This method removes the 'products' table from the database.
     * 
     * @return void
     */
    public function down()
    {
        // drop table
        $this->forge->dropTable('products');
    }
}
