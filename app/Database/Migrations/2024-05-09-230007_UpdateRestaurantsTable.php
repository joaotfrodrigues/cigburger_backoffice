<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateRestaurantsTable extends Migration
{
    public function up()
    {
        // update restaurants table
        $this->forge->addColumn('restaurants', [
            'project_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'email'
            ],
            'api_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'project_id'
            ]
        ]);
    }

    public function down()
    {
        // remove project_id and api_key columns
        $this->forge->dropColumn('restaurants', ['project_id', 'api_key']);
    }
}
