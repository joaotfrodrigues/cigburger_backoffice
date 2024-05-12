<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Seeds the 'users' table with fake data.
     * 
     * This method populates the 'users' table with fake data for users associated with different restaurants. It includes details such as restaurant ID, username, hashed password, user's name, email, phone number, roles, activation status, and creation timestamp.
     * 
     * @return void
     */
    public function run()
    {
        // create users fake data
        $users = [
            [
                'id_restaurant' => 1,
                'username' => 'admin_rest1',
                'passwrd' => password_hash('admin_rest1', PASSWORD_DEFAULT),
                'name' => 'Administrador Restaurante 1',
                'email' => 'admin_rest1@gmail.com',
                'phone' => '990000101',
                'roles' => '["admin"]',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_restaurant' => 1,
                'username' => 'user_rest1',
                'passwrd' => password_hash('user_rest1', PASSWORD_DEFAULT),
                'name' => 'Colaborador Restaurante 1',
                'email' => 'user_rest1@gmail.com',
                'phone' => '990001101',
                'roles' => '["user"]',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],

            // restaurant 2
            [
                'id_restaurant' => 2,
                'username' => 'admin_rest2',
                'passwrd' => password_hash('admin_rest2', PASSWORD_DEFAULT),
                'name' => 'Administrador Restaurante 2',
                'email' => 'admin_rest2@gmail.com',
                'phone' => '990000202',
                'roles' => '["admin"]',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_restaurant' => 2,
                'username' => 'user_rest2',
                'passwrd' => password_hash('user_rest2', PASSWORD_DEFAULT),
                'name' => 'Colaborador Restaurante 2',
                'email' => 'user_rest2@gmail.com',
                'phone' => '990002202',
                'roles' => '["user"]',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],

            // restaurant 3
            [
                'id_restaurant' => 3,
                'username' => 'admin_rest3',
                'passwrd' => password_hash('admin_rest3', PASSWORD_DEFAULT),
                'name' => 'Administrador Restaurante 3',
                'email' => 'admin_rest3@gmail.com',
                'phone' => '990000303',
                'roles' => '["admin"]',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_restaurant' => 3,
                'username' => 'user_rest3',
                'passwrd' => password_hash('user_rest3', PASSWORD_DEFAULT),
                'name' => 'Colaborador Restaurante 3',
                'email' => 'user_rest3@gmail.com',
                'phone' => '990003303',
                'roles' => '["user"]',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        // insert users
        $this->db->table('users')->insertBatch($users);
    }
}
