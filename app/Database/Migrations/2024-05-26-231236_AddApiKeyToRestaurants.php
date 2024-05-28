<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApiKeyToRestaurants extends Migration
{
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

    public function down()
    {
        // drop column
        $this->forge->dropColumn('restaurants', 'api_key_openssl');
    }
}
