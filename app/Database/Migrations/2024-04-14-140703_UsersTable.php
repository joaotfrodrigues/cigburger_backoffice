<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UsersTable extends Migration
{
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
                'constraint' => 11
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'passwrd' => [
                'type' => 'VARCHAR',
                'constraint' => 250
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'roles' => [
                'type' => 'VARCHAR',
                'constraint' => 250
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'active' => [
                'type' => 'INT',
                'constraint' => 1
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 20
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

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
