<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RestaurantModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function login()
    {
        // load restaurants
        $restaurants_model = new RestaurantModel();
        $restaurants = $restaurants_model->select('id, name')->findAll();

        // validation errors
        return view('auth/login_frm', [
            'restaurants' => $restaurants,
            'validation_errors' => session()->getFlashdata('validation_errors'),
            'select_restaurant' => session()->getFlashdata('select_restaurant'),
            'login_error' => session()->getFlashdata('login_error'),
        ]);
    }

    public function login_submit()
    {
        // form validation
        $validation = $this->validate([
            'text_username' => [
                'label' => 'utilizador',
                'rules' => 'required|min_length[6]|max_length[16]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.',
                    'min_length' => 'O campo {field} é deve ter, no mínimo, {param} caracteres.',
                    'max_length' => 'O campo {field} é deve ter, no máximo, {param} caracteres.',
                ]
            ],
            'text_password' => [
                'label' => 'senha',
                'rules' => 'required|min_length[6]|max_length[16]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.',
                    'min_length' => 'O campo {field} deve ter, no mínimo, {param} caracteres.',
                    'max_length' => 'O campo {field} deve ter, no máximo, {param} caracteres.',
                ]
            ],
            'select_restaurant' => [
                'label' => 'restaurante',
                'rules' => 'required',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.'
                ]
            ]
        ]);

        if (!$validation) {
            session()->setFlashdata('select_restaurant', Decrypt($this->request->getPost('select_restaurant')));

            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // verify login credentials
        $username = $this->request->getPost('text_username');
        $password = $this->request->getPost('text_password');
        $id_restaurant = Decrypt($this->request->getPost('select_restaurant'));

        $user_model = new UserModel();
        $user = $user_model->login_verify($username, $password, $id_restaurant);

        if (!$user) {
            session()->setFlashdata('select_restaurant', Decrypt($this->request->getPost('select_restaurant')));

            return redirect()->back()->withInput()->with('login_error', 'Utilizador ou senha inválidos.');
        }

        dd($user);
    }

    public function logout()
    {
        echo 'logout';
    }
}
