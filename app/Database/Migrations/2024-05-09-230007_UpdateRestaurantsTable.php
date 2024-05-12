<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateRestaurantsTable extends Migration
{
    /**
     * Updates the 'restaurants' table.
     * 
     * This method adds two new columns, 'project_id' and 'api_key', to the 'restaurants' table.
     * These columns are intended for storing additional information related to projects and API keys.
     * 
     * @return void
     */
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

    /**
     * Reverts the changes made in the 'up()' method.
     * 
     * This method removes the 'project_id' and 'api_key' columns from the 'restaurants' table, effectively rolling back the changes made during the migration.
     * 
     * @return void
     */
    public function down()
    {
        // remove project_id and api_key columns
        $this->forge->dropColumn('restaurants', ['project_id', 'api_key']);
    }
}
