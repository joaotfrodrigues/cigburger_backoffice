<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiRestaurantModel extends Model
{
    protected $table            = 'restaurants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'api_key',
        'api_key_openssl',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Retrieves a list of distinct machine IDs associated with a specific restaurant.
     * 
     * This function queries the `orders` table to fetch all unique `machine_id` values 
     * for a given restaurant ID, excluding orders that have been marked as deleted.
     * 
     * @param int $id_restaurant The ID of the restaurant for which to retrieve machine IDs.
     * 
     * @return array An array of distinct machine IDs associated with the specified restaurant.
     */
    public function get_machines_from_restaurant($id_restaurant)
    {
        // get all distinct machines from orders table
        return $this->db->table('orders')
            ->select('machine_id')
            ->distinct()
            ->where('id_restaurant', $id_restaurant)
            ->where('deleted_at', null)
            ->get()
            ->getResult();
    }
}
