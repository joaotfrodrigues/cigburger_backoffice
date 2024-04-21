<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['last_login'];

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

    public function login_verify($username, $password, $id_restaurant)
    {
        // where clauses
        $where = [
            'username' => $username,
            'id_restaurant' => $id_restaurant,
            'blocked_until' => null,
            'active' => 1,
            'deleted_at' => null
        ];

        $user = $this->where($where)->first();

        if (empty($user)) {
            return false;
        }

        if (!password_verify($password, $user->passwrd)) {
            return false;
        }

        // update last login
        $this->update($user->id, [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        return $user;
    }
}