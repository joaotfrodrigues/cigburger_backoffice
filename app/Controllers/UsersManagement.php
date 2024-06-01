<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersManagementModel;
use CodeIgniter\HTTP\ResponseInterface;

class UsersManagement extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Gestão de Utilizadores',
            'page'  => 'Utilizadores'
        ];

        // get all users
        $users_model = new UsersManagementModel();
        $users = $users_model->where('id_restaurant', session('user')['id_restaurant'])->findAll();

        $data['users'] = $this->_prepare_users_data($users);

        $data['datatables'] = true;

        return view('dashboard/users_management/index', $data);
    }

    public function edit($enc_id)
    {
        echo 'editar utilizador';
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PRIVATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Prepares user data for further processing.
     * 
     * This method processes an array of user objects, extracting relevant attributes 
     * and structuring them into an array format suitable for further use, such as 
     * display or export. It also includes a flag indicating whether the user has a password set.
     * 
     * @param array $users An array of user objects. Each object is expected to have 
     *                     properties such as 'id', 'has_password', 'username', 'name', 
     *                     'email', 'phone', 'roles', 'blocked_until', 'active', 
     *                     'last_login', 'created_at', 'updated_at', and 'deleted_at'.
     * @return array An array of associative arrays, where each associative array 
     *               represents a user with their respective attributes.
     */
    private function _prepare_users_data($users)
    {
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id'            => $user->id,
                'has_password'  => !empty($user->passwrd),
                'username'      => $user->username,
                'name'          => $user->name,
                'email'         => $user->email,
                'phone'         => $user->phone,
                'roles'         => json_decode($user->roles),
                'blocked_until' => $user->blocked_until,
                'active'        => $user->active,
                'last_login'    => $user->last_login,
                'created_at'    => $user->created_at,
                'update_at'     => $user->updated_at,
                'deleted_at'    => $user->deleted_at,
            ];
        }

        return $data;
    }
}
