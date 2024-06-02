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
    protected $protectFields    = false;
    protected $allowedFields    = [];

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
     * Verifies user login credentials.
     * 
     * This method verifies the login credentials provided by the user against the database records.
     * It checks if the username, restaurant ID, and other conditions are met to authenticate the user.
     * If the user is found and the password matches the hashed password stored in the database,
     * the method updates the last login timestamp and returns the user object.
     * 
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     * @param int $id_restaurant The ID of the restaurant associated with the user.
     * 
     * @return mixed Returns the user object if authentication is successful, otherwise returns false.
     */
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
