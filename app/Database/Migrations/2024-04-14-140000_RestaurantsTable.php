<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RestaurantsTable extends Migration
{
    /**
     * Creates the 'restaurants' table.
     * 
     * This method defines the structure of the 'restaurants' table, including fields like name, address, phone, and email.
     * It sets the primary key and creates the table in the database.
     * 
     * @return void
     */
    public function up()
    {
        // create restaurants table fields
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 250
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50
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
            ],
        ]);

        // add primary key
        $this->forge->addKey('id', true);

        // create table
        $this->forge->createTable('restaurants');
    }

    /**
     * Drops the 'restaurants' table.
     * 
     * This method removes the 'restaurants' table from the database.
     * 
     * @return void
     */
    public function down()
    {
        $this->forge->dropTable('restaurants');
    }
}
