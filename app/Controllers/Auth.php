<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RestaurantModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    /**
     * Displays the login form.
     * 
     * This method is responsible for displaying the login form to the user. It loads
     * the list of restaurants from the database using the RestaurantModel class. It then
     * renders the login form view along with any validation errors or login errors stored
     * in the session flash data.
     * 
     * @return View The login form view containing the list of restaurants and any validation errors.
     */
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

    /**
     * Processes the login form submission.
     * 
     * This method handles the submission of the login form. It first validates the form
     * data, including the username, password, and selected restaurant, using the validation
     * rules defined. If validation fails, it redirects back to the login form with the
     * validation errors and input data preserved. If validation succeeds, it verifies
     * the login credentials with the UserModel class. If the credentials are valid,
     * it sets the user data in the session and redirects to the home page. If the credentials
     * are invalid, it redirects back to the login form with a login error message.
     * 
     * @return RedirectResponse Redirects to the home page on successful login, or back to the
     *                         login form with validation errors or a login error message.
     */
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

        // set session
        $restaurant = new RestaurantModel();
        $restaurant_name = $restaurant->select('name')->find($user->id_restaurant)->name;

        $user_data = [
            'id' => $user->id,
            'name' => $user->name,
            'id_restaurant' => $user->id_restaurant,
            'restaurant_name' => $restaurant_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'roles' => $user->roles,
        ];

        session()->set('user', $user_data);

        return redirect()->to('/');
    }

    /**
     * Logs out the user and destroys the session.
     * 
     * This method logs out the user by destroying the session, effectively logging
     * the user out of the system. It then redirects the user to the login page.
     * 
     * @return RedirectResponse Redirects to the login page after logging out.
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/auth/login');
    }
}
