<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UsersTable extends Migration
{
    /**
     * Creates the 'users' table.
     * 
     * This method defines the structure of the 'users' table, including fields like username, password, name, email, roles, and others.
     * It sets the primary key and creates the table in the database.
     * 
     * @return void
     */
    public function up()
    {
        // create users table fields
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'id_restaurant' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'passwrd' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'roles' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'active' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => true
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'blocked_until' => [
                'type' => 'DATETIME',
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
            ],
        ]);

        // add primary key
        $this->forge->addKey('id', true);

        // create table
        $this->forge->createTable('users');
    }

    /**
     * Drops the 'users' table.
     * 
     * This method removes the 'users' table from the database.
     * 
     * @return void
     */
    public function down()
    {
        $this->forge->dropTable('users');
    }
}
