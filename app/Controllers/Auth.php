<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RestaurantModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use EmptyIterator;

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

    /**
     * Handles the completion of user registration.
     * 
     * This method processes the provided personal URL (PURL) code to verify the user's registration process. 
     * If the code is valid, it retrieves the user's information, including their name, username, email, phone, 
     * roles, and associated restaurant name, and places this data into the session. Finally, it redirects the 
     * user to the password definition page to complete the registration process.
     * 
     * @param string|null $purl_code The personal URL (PURL) code used to identify the user's registration process.
     * @return RedirectResponse Redirects the user to the password definition page if the PURL code is valid, 
     *                                               or to the login page if the code is invalid or not provided.
     */
    public function finish_registration($purl_code = null)
    {
        if (empty($purl_code)) {
            return redirect()->to('/auth/login');
        }

        // check if purl code is valid
        $users_model = new UserModel();

        // get information from purl (including restaurant name)
        $user = $users_model->select('users.*, restaurants.name as restaurant_name')
            ->where('users.code', $purl_code)
            ->join('restaurants', 'restaurants.id = users.id_restaurant')
            ->first();

        if (!$user) {
            return redirect()->to('/auth/login');
        }

        // place user data in session
        $new_user_data = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'roles' => $user->roles,
            'id_restaurant' => $user->id_restaurant,
            'restaurant_name' => $user->restaurant_name
        ];

        session()->set('new_user', $new_user_data);

        // redirect to the finish registration form
        return redirect()->to('/auth/define_password');
    }

    /**
     * Displays the form for defining a new password.
     * 
     * This method checks if the new user data exists in the session. If the new user data is not found,
     * the user is redirected to the login page. If the new user data is present, it retrieves any
     * validation errors from the session flash data and passes them to the view. The method then
     * renders the form for defining a new password.
     * 
     * @return View If new user data is not in the session, redirects to the login page.
     * Otherwise, returns the view for defining a new password.
     */
    public function define_password()
    {
        // check if new user exists in session
        if (!session()->has('new_user')) {
            return redirect()->to('/auth/login');
        }

        $data['validation_errors'] = session()->getFlashdata('validation_errors');

        return view('auth/define_password_frm', $data);
    }

    /**
     * Handles the submission of the new password form.
     * 
     * This method validates the form input for defining a new password. If the validation fails, it 
     * redirects back to the form with validation errors. If the validation passes, it updates the 
     * user's password and sets the user as active. The new user data is then removed from the session,
     * and the user is redirected to the welcome page with a success message.
     * 
     * @return RedirectResponse Redirects back to the form with validation errors if validation fails, 
     * or redirects to the welcome page with a success message if the password update is successful.
     */
    public function define_password_submit()
    {
        // form validation
        $validation = $this->validate($this->_define_new_password_validation_rules());

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // update user password and active state
        $user_model = new UserModel();

        $id_user  = session('new_user')['id'];
        $password = $this->request->getPost('text_password');

        $user_model->update($id_user, [
            'passwrd'    => password_hash($password, PASSWORD_DEFAULT),
            'code'       => null,
            'active'     => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // remove new user from session
        session()->remove('new_user');

        // redirect to welcome page
        return redirect()->to('/auth/welcome')->with('success', true);
    }

    /**
     * Displays the welcome page after successful password definition.
     * 
     * This method checks if a success message is present in the session flashdata.
     * If not, it redirects the user to the login page. If the success message is present,
     * it displays the welcome page.
     * 
     * @return View Redirects to the login page if the success message is not found,
     * or displays the welcome page if the success message is present.
     */
    public function welcome()
    {
        // check if success is in session as flashdata
        if (!session()->getFlashdata('success')) {
            return redirect()->to('/auth/login');
        }

        // display welcome page
        return view('auth/welcome');
    }


    // -----------------------------------------------------------------------------------------------------------------
    // PRIVATE METHODS
    // -----------------------------------------------------------------------------------------------------------------


    /**
     * Defines validation rules for setting a new password.
     * 
     * This method specifies the validation rules required for setting a new password, including the criteria 
     * for password length and complexity. It ensures that the password field is required and must be between 
     * 8 and 16 characters long, containing at least one lowercase letter, one uppercase letter, and one digit. 
     * Additionally, it validates the confirmation of the password to ensure it matches the original password entry.
     * 
     * @return array An associative array containing the validation rules for setting a new password.
     */
    private function _define_new_password_validation_rules()
    {
        return [
            'text_password' => [
                'label'  => 'Senha',
                'rules'  => 'required|min_length[8]|max_length[16]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/]',
                'errors' => [
                    'required'    => 'O campo {field} é obrigatório',
                    'min_length'  => 'O campo {field} deve ter no mínimo {param} caracteres',
                    'max_length'  => 'O campo {field} deve ter no máximo {param} caracteres',
                    'regex_match' => 'O campo {field} deve ter pelo menos uma letra minúscula, uma maiúscula e um algarismo'
                ],
            ],
            'text_password_confirm' => [
                'label'  => 'Confirmar senha',
                'rules'  => 'required|matches[text_password]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'matches'  => 'O campo {field} deve ser igual ao campo Senha',
                ],
            ],
        ];
    }
}
