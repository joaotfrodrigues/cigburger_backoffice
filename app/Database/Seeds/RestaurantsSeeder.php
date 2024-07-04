<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RestaurantsSeeder extends Seeder
{
    /**
     * Seeds the 'restaurants' table with fake data.
     * 
     * This method populates the 'restaurants' table with fake data for two restaurants. Each restaurant entry includes details such as name, address, phone number, email, and creation timestamp.
     * 
     * @return void
     */
    public function run()
    {
        // create restaurants fake data
        $restaurants = [
            [
                'name' => 'Restaurante 1',
                'address' => 'Rua do Restaurante 1',
                'phone' => '990000100',
                'email' => 'restaurante_1@gmail.com',
                'project_id' => '100',
                'api_key' => '$2y$10$Lk9Mb62.VnKz7ekX6Ik0ieizA0Zsjo8Xj5muJAgzPok1NRyJE8FP2',
                'api_key_openssl' => '089489f0a0c452f691eee3d183f57924d80e089c4d429f5733da1e3e34b8780acad54275d3b85baa36aecba89e3c61f08fcd535369c5f68c9160ddd31a5d1627574b114819f69c288b86bdaf2f94a7c0827afc98c1d1927de78c413bd16c16ac4135c9885fcddbc42d3b2e527cbcf958',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Restaurante 2',
                'address' => 'Rua do Restaurante 2',
                'phone' => '990000200',
                'email' => 'restaurante_2@gmail.com',
                'project_id' => '200',
                'api_key' => '$2y$10$maFA042zRVi63yWGsM/GEOiKeL4HcRv1jAkb18rT/Dwy8GTfup.0S',
                'api_key_openssl' => '7aeabc6e7e0fae8cfea502e04312539f60aa3cdef7947ebff824b3f6aa8f03bbf665a158d20f79bf5319e03b48ba373f0908eccbfef02650b83acedf04bed1f0e9bcb2feada523b0cbc8ff9dacaf5dd5b50e905a180272157f1eff0815131cd8bbc9dfd7e2ce15124f741f6b5009cc94',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        // insert restaurants
        $this->db->table('restaurants')->insertBatch($restaurants);
    }
}
