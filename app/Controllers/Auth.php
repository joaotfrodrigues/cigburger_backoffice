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

    /**
     * Displays the user profile page.
     * 
     * This method retrieves the currently logged-in user's data from the database, validates the user, and prepares the data 
     * for display on the profile page. If the user data is not found, it redirects to the home page. The user's roles are 
     * converted from JSON to a string for easier display. It also includes any form validation errors that may have 
     * occurred during previous requests.
     * 
     * @return View A view containing the profile page
     */
    public function profile()
    {
        // get user data
        $user_model = new UserModel();
        $user = $user_model->find(session('user')['id']);

        // check if user is valid
        if (!$user) {
            return redirect()->to('/');
        }

        // transform the json to string | ['admin'] => 'admin'
        $user->role = json_decode($user->roles)[0];

        // display profile page
        $data = [
            'title' => 'Perfil do Utilizador',
            'page'  => 'Perfil do Utilizador',
            'user'  => $user
        ];

        // form validation
        $data['validation_errors'] = session()->getFlashdata('validation_errors');

        // server error
        $data['server_error'] = session()->getFlashdata('server_error');

        // success profile message
        $data['profile_success'] = session()->getFlashdata('profile_success');

        // success password message
        $data['password_success'] = session()->getFlashdata('password_success');

        return view('auth/profile', $data);
    }

    /**
     * Handles the form submission for updating the user profile.
     * 
     * This method validates the profile form inputs, checks for duplicate email addresses,
     * updates the user data in the database, updates the session data, and redirects back
     * to the profile page with appropriate messages.
     *
     * @return RedirectResponse
     */
    public function profile_submit()
    {
        // form validatiom
        $validation = $this->validate($this->_profile_form_validation());

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        $user_model = new UserModel();

        // check if there's another user with the same e-mail address
        $check_email = $user_model->where('id !=', session('user')['id'])
            ->where('email', $this->request->getPost('text_email'))
            ->get()
            ->getResultArray();

        if ($check_email) {
            return redirect()->back()->withInput()->with('server_error', 'Já existe outro utilizador com o mesmo e-mail');
        }

        // update user data
        $user_model->update(session('user')['id'], [
            'name'       => $this->request->getPost('text_name'),
            'email'      => $this->request->getPost('text_email'),
            'phone'      => $this->request->getPost('text_phone'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // update session
        $user = session('user');
        $user['name']  = $this->request->getPost('text_name');
        $user['email'] = $this->request->getPost('text_email');
        $user['phone'] = $this->request->getPost('text_phone');

        session()->set('user', $user);

        // redirect to profile page
        return redirect()->to('/auth/profile')->with('profile_success', true);
    }

    /**
     * Handles the form submission for changing the user password.
     * 
     * This method validates the change password form inputs, checks if the current password is correct,
     * updates the user password in the database, and redirects back to the profile page with appropriate messages.
     *
     * @return RedirectResponse
     */
    public function change_password_submit()
    {
        // form validatiom
        $validation = $this->validate($this->_profile_change_password_form_validation());

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // check if current password is correct
        $user_model = new UserModel();

        $user = $user_model->find(session('user')['id']);
        $password = $this->request->getPost('text_password');

        if (!password_verify($password, $user->passwrd)) {
            return redirect()->back()->withInput()->with('validation_errors', ['text_password' => 'Senha atual está incorreta']);
        }

        // update user password
        $new_password = $this->request->getPost('text_new_password');
        $user_model->update(session('user')['id'], [
            'passwrd' => password_hash($new_password, PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // redirect to profile page
        return redirect()->to('/auth/profile')->with('password_success', true);
    }

    /**
     * Displays the forgot password form.
     * 
     * This method loads the forgot password form, allowing users to reset their password.
     * It also loads the list of restaurants to populate the dropdown menu for restaurant selection.
     * 
     * @return View The forgot password form view with restaurant data and validation errors, if any.
     */
    public function forgot_password()
    {
        // load restaurants
        $restaurants_model = new RestaurantModel();
        $restaurants = $restaurants_model->select('id, name')
            ->findAll();

        $data['restaurants'] = $restaurants;

        // form validation
        $data['validation_errors'] = session()->getFlashdata('validation_errors');

        return view('/auth/forgot_password_frm', $data);
    }

    /**
     * Handles the submission of the forgot password form.
     * 
     * This method validates the input data from the forgot password form.
     * If the validation fails, it redirects back to the form with validation errors.
     * If the email exists, it generates a random hash code for the password reset link (purl) 
     * and sends an email to the user with instructions to reset the password.
     * If the email does not exist, it still displays a success message to avoid revealing 
     * information about the existence of the email.
     * 
     * @return View The view for the forgot password success page.
     */
    public function forgot_password_submit()
    {
        // form validation
        $validation = $this->validate($this->_forgot_password_validation_rules());

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // check if e-mail exists
        $id_restaurant = Decrypt($this->request->getPost('select_restaurant'));
        $email = $this->request->getPost('text_email');

        $user_model = new UserModel();
        $user = $user_model->where('email', $email)
            ->where('id_restaurant', $id_restaurant)
            ->first();

        // always show success message to avoid give information about the existance of the e-mail
        if (!$user) {
            return view('auth/forgot_password_success');
        }

        // generate random hash code for purl
        $code = bin2hex(random_bytes(16));

        // update user code
        $user_model->update($user->id, [
            'code'       => $code,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $data = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'code'  => $code
        ];

        // send e-mail to recover the password
        $this->_send_email($data);

        return view('auth/forgot_password_success');
    }

    /**
     * Displays the redefine password form if the purl code is valid.
     * 
     * This method checks if a purl code is provided and if it exists in the database.
     * If the purl code is missing or invalid, it redirects to the login page.
     * If the purl code is valid, it displays the redefine password form.
     * 
     * @param string|null $purl_code The unique password reset code sent to the user's email.
     * 
     * @return View The view for the redefine password form.
     */
    public function redefine_password($purl_code = null)
    {
        // check if purl code is empty
        if (empty($purl_code)) {
            return redirect()->to('/auth/login');
        }

        // check if purl code exists
        $user_model = new UserModel();
        $user = $user_model->where('code', $purl_code)
            ->first();

        if (!$user) {
            return redirect()->to('/auth/login');
        }

        // display redefine password page
        $data['purl_code'] = $purl_code;
        $data['validation_errors'] = session()->getFlashdata('validation_errors');

        return view('auth/redefine_password_frm', $data);
    }

    /**
     * Handles the submission of the redefine password form.
     * 
     * Validates the form inputs, checks the reset password code,
     * updates the user password if the code is valid, and displays the success page.
     * If validation fails or the code is invalid, redirects back with appropriate error messages.
     * 
     * @return View
     */
    public function redefine_password_submit()
    {
        // form validation
        $validation = $this->validate($this->_define_reset_password_validation_rules());

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // update user password
        $user_model = new UserModel();

        $code = $this->request->getPost('purl_code');
        $password = $this->request->getPost('text_password');

        $user_model->where('code', $code)
            ->set('passwrd', password_hash($password, PASSWORD_DEFAULT))
            ->set('code', null)
            ->set('updated_at', date('Y-m-d H:i:s'))
            ->update();

        // display success page
        return view('auth/redefine_password_success');
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

    /**
     * Defines the validation rules for the profile form.
     * 
     * This method returns an array of validation rules for the profile form fields. It ensures that the user's name, email,
     * and phone number meet specific requirements such as being required, having valid lengths, and matching certain patterns.
     * 
     * @return array An array containing the validation rules for the profile form.
     */
    private function _profile_form_validation()
    {
        return [
            'text_name' => [
                'label'  => 'Nome do utilizador',
                'rules'  => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required'   => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no mínimo {param caracteres}',
                    'max_length' => 'O campo {field} deve ter no máximo {param caracteres}',
                ]
            ],
            'text_email' => [
                'label'  => 'E-mail',
                'rules'  => 'required|valid_email|min_length[3]|max_length[50]',
                'errors' => [
                    'required'    => 'O campo {field} é obrigatório',
                    'valid_email' => 'O campo {field} deve conter um e-mail válido',
                    'min_length'  => 'O campo {field} deve ter no mínimo {param caracteres}',
                    'max_length'  => 'O campo {field} deve ter no máximo {param caracteres}',
                ]
            ],
            'text_phone' => [
                'label'  => 'Telefone',
                'rules'  => 'required|regex_match[/^[9]{1}\d{8}$/]',
                'errors' => [
                    'required'    => 'O campo {field} é obrigatório',
                    'regex_match' => 'O campo {field} deve conter um número de telefone válido',
                ]
            ],
        ];
    }

    /**
     * Defines the validation rules for the profile change password form.
     * 
     * This method returns an array of validation rules for changing the password in the profile form.
     * It ensures that the passwords meet specific requirements such as being required, having valid lengths,
     * matching certain patterns, and ensuring the new password confirmation matches the new password.
     * 
     * @return array An array containing the validation rules for the profile change password form.
     */
    private function _profile_change_password_form_validation()
    {
        return [
            'text_password' => [
                'label' => 'Senha',
                'rules' => 'required|min_length[8]|max_length[16]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*/]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.',
                    'min_length' => 'O campo {field} deve ter no mínimo {param} caracteres.',
                    'max_length' => 'O campo {field} deve ter no máximo {param} caracteres.',
                    'regex_match' => 'O campo {field} deve conter pelo menos uma letra maiúscula, uma minúscula e um algarismo.'
                ]
            ],
            'text_new_password' => [
                'label' => 'Nova senha',
                'rules' => 'required|min_length[8]|max_length[16]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*/]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.',
                    'min_length' => 'O campo {field} deve ter no mínimo {param} caracteres.',
                    'max_length' => 'O campo {field} deve ter no máximo {param} caracteres.',
                    'regex_match' => 'O campo {field} deve conter pelo menos uma letra maiúscula, uma minúscula e um algarismo.'
                ]
            ],
            'text_new_password_confirm' => [
                'label' => 'Confirmar nova senha',
                'rules' => 'required|matches[text_new_password]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.',
                    'matches' => 'O campo {field} deve ser igual ao campo Nova senha.'
                ]
            ]
        ];
    }

    /**
     * Defines the validation rules for the forgot password form.
     * 
     * This method specifies the validation rules for the forgot password form fields,
     * including the selection of a restaurant and the input of an email address.
     * 
     * @return array An array containing validation rules for each form field.
     */
    private function _forgot_password_validation_rules()
    {
        return [
            'select_restaurant' => [
                'label'  => 'Restaurante',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório'
                ]
            ],
            'text_email' => [
                'label'  => 'E-mail',
                'rules'  => 'required|valid_email|min_length[5]|max_length[50]',
                'errors' => [
                    'required'    => 'O campo {field} é obrigatório',
                    'valid_email' => 'O campo {field} deve conter um e-mail válido',
                    'min_length'  => 'O campo {field} deve ter no mínimo {param} caracteres',
                    'max_length'  => 'O campo {field} deve ter no máximo {param} caracteres',
                ]
            ]
        ];
    }

    /**
     * Sends a registration completion email to the user.
     * 
     * This method prepares a personal URL (purl) for the user to complete their registration 
     * and configures an email with the provided data. It uses the CodeIgniter email service 
     * to send the email.
     * 
     * @param array $data An associative array containing the necessary data for the email.
     *                    Expected keys:
     *                    - 'code'  : The unique code for the user's registration.
     *                    - 'email' : The recipient's email address.
     *                    - 'name'  : The name of the recipient.
     * 
     * @return bool Returns true if the email was sent successfully, false otherwise.
     */
    private function _send_email($data)
    {
        // prepare purl - personal url
        $data['purl'] = site_url('/auth/redefine_password/' . $data['code']);

        // config e-mail
        $email = \Config\Services::email();
        $email->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $email->setTo($data['email']);
        $email->setSubject('CigBurger - Recuperação de senha');
        $email->setMessage(view('emails/email_recover_password', $data));

        // send e-mail and return true or false
        return $email->send();
    }

    /**
     * Defines validation rules for the reset password form.
     * 
     * This private method returns an array of validation rules and error messages
     * for the reset password form. The rules ensure that the provided password 
     * meets the required criteria and that the confirmation password matches.
     * 
     * @return array An array of validation rules and error messages.
     */
    private function _define_reset_password_validation_rules()
    {
        return [
            'purl_code' => [
                'label'  => '',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Aconteceu um erro na submissão do formulário'
                ]
            ],
            'text_password' => [
                'label'  => 'Senha',
                'rules'  => 'required|min_length[8]|max_length[16]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*/]',
                'errors' => [
                    'required'    => 'O campo {field} é obrigatório',
                    'min_length'  => 'O campo {field} deve ter no mínimo {param} caracteres',
                    'max_length'  => 'O campo {field} deve ter no máximo {param} caracteres',
                    'regex_match' => 'O campo {field} deve conter pelo menos uma letra maiúscula, uma minúscula e um algarimo',
                ]
            ],
            'text_password_confirm' => [
                'label'  => 'Confirmar Senha',
                'rules'  => 'required|matches[text_password]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'matches' => 'O campo {field} deve ser igual ao campo Senha'
                ]
            ]
        ];
    }
}
