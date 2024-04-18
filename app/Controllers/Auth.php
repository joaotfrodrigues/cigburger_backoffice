<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RestaurantModel;
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
            'validation_errors' => session()->getFlashdata('validation_errors')
        ]);
    }

    public function login_submit()
    {
        // show restaurant id
        // $restaurant_id = Decrypt($this->request->getPost('select_restaurant'));
        // dd($restaurant_id);

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
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        echo 'ok';
    }

    public function logout()
    {
        echo 'logout';
    }
}
