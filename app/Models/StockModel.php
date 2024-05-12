<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table            = 'stocks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_product',
        'stock_quantity',
        'stock_in_out',
        'stock_supplier',
        'reason',
        'movement_date',
        'created_at',
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
     * Retrieves distinct suppliers from the stocks table for a specific restaurant.
     * 
     * This method retrieves distinct suppliers from the stocks table that belong to the provided restaurant ID.
     * It performs an inner join with the products table to ensure that the stocks are associated with products
     * from the specified restaurant. Only stocks with 'IN' movement are considered.
     * 
     * @param int $id_restaurant The ID of the restaurant.
     * @return array An array containing distinct suppliers' names.
     */
    public function getStocksSupplier($id_restaurant)
    {
        // get distinct suppliers within stocks table that belongs to the id restaurant
        return $this->db->table('stocks')
            ->distinct()
            ->select('stocks.stock_supplier')
            ->join('products', 'products.id = stocks.id_product')
            ->where('products.id_restaurant', $id_restaurant)
            ->where('stocks.stock_in_out', 'IN')
            ->get()
            ->getResult();
    }
}
