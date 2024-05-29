<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApiKeyToRestaurants extends Migration
{
    /**
     * Adds the 'api_key_openssl' column to the 'restaurants' table.
     * 
     * This migration function adds a new column named 'api_key_openssl' to the 'restaurants' 
     * table. The new column is of type VARCHAR with a length of 500, allows NULL values, and 
     * is placed after the existing 'api_key' column.
     * 
     * @return void
     */
    public function up()
    {
        // add column to restaurants
        $this->forge->addColumn('restaurants', [
            'api_key_openssl' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'api_key'
            ]
        ]);
    }

    /**
     * Removes the 'api_key_openssl' column from the 'restaurants' table.
     * 
     * This migration function drops the 'api_key_openssl' column from the 'restaurants' 
     * table, effectively reversing the changes made in the `up` method.
     * 
     * @return void
     */
    public function down()
    {
        // drop column
        $this->forge->dropColumn('restaurants', 'api_key_openssl');
    }
}
