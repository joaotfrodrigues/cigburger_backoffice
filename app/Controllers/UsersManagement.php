<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersManagementModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class UsersManagement extends BaseController
{
    /**
     * Displays the user management page.
     * 
     * This method prepares the data needed for displaying the user management page. 
     * It retrieves all users associated with the current restaurant, processes the user 
     * data for display, and includes the necessary settings for the DataTables library.
     * 
     * @return ResponseInterface Renders the user management page view with the provided data.
     */
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


    // -----------------------------------------------------------------------------------------------------------------
    // NEW USER
    // -----------------------------------------------------------------------------------------------------------------


    /**
     * Displays the form for creating a new user.
     * 
     * This method sets up the data needed for the "New User" form, including the title, 
     * page information, and any validation or server errors from the previous submission attempt.
     * 
     * @return RedirectResponse Renders the new user form view with the provided data.
     */
    public function new_user()
    {
        $data = [
            'title' => 'Novo Utilizador',
            'page'  => 'Utilizadores'
        ];

        // look for form validation errors
        $data['validation_errors'] = session()->getFlashdata('validation_errors');

        // look for server errors
        $data['server_error'] = session()->getFlashdata('server_error');

        return view('dashboard/users_management/new_user_frm', $data);
    }

    /**
     * Handles the submission of a new user registration form.
     * 
     * This method performs form validation, checks for existing users with the same username 
     * or email within the same restaurant, inserts a new user into the database if validation 
     * passes, and sends an email to the user to complete their registration.
     * 
     * @return RedirectResponse Redirects back to the form with errors if validation fails or 
     *                                           to the users management page if successful.
     */
    public function new_user_submit()
    {
        // form validation
        $validation = $this->validate($this->_new_user_validation());

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // check if username already exists
        $users_model = new UsersManagementModel();

        $id_restaurant = session('user')['id_restaurant'];
        $username = $this->request->getPost('text_username');
        $email = $this->request->getPost('text_email');

        $result = $users_model->where('id_restaurant', $id_restaurant)
            ->where('username', $username)
            ->orWhere('email', $email)
            ->findAll();

        if (!empty($result)) {
            return redirect()->back()->withInput()->with('server_error', 'Já existe um utilizador com o mesmo nome ou e-mail');
        }

        // insert new user
        $code = bin2hex(random_bytes(16));

        $data = [
            'id_restaurant' => $id_restaurant,
            'username' => $username,
            'name' => $this->request->getPost('text_name'),
            'email' => $email,
            'phone' => $this->request->getPost('text_phone'),
            'roles' => json_encode([$this->request->getPost('select_role')]),
            'active' => 0,
            'code' => $code,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $users_model->insert($data);

        // send e-mail to user to finish the registration process, by setting a password
        $this->_send_email($data);

        return redirect()->to('/users_management');
    }


    // -----------------------------------------------------------------------------------------------------------------
    // EDIT USER
    // -----------------------------------------------------------------------------------------------------------------


    /**
     * Displays the user editing form and handles form submission.
     * 
     * This method retrieves user data based on the provided encoded ID, decodes it,
     * and fetches the corresponding user information from the database. If the user
     * is found, it populates the user editing form with the retrieved data. If there
     * are any validation errors from a previous form submission, they are also passed
     * to the view for display. Additionally, it enables the Flatpickr library for
     * date input fields in the form.
     * 
     * @param string $enc_id The encoded ID of the user to be edited.
     * @return View The user editing form view or a redirect response.
     */
    public function edit($enc_id)
    {
        // decode id
        $id = Decrypt($enc_id);
        if (empty($id)) {
            return redirect()->to('/users_management');
        }

        // get user data
        $users_model = new UsersManagementModel();
        $user = $users_model->find($id);

        if (!$user) {
            return redirect()->to('/users_management');
        }

        // get user roles
        $user->roles = json_decode($user->roles)[0];

        $data = [
            'title' => 'Utilizadores',
            'page'  => 'Editar utilizador',
            'user'  => $user,
            'validation_errors' => session()->getFlashdata('validation_errors')
        ];

        // flatpickr
        $data['flatpickr'] = true;

        return view('dashboard/users_management/edit_user_frm', $data);
    }

    /**
     * Handles the submission of the user editing form.
     * 
     * This method validates the form input based on predefined validation rules.
     * If the validation fails, it redirects back to the editing form with the input data
     * and validation errors. Upon successful validation, it retrieves the user ID from
     * the hidden input field, processes the input data, and updates the user information
     * in the database. Finally, it redirects to the users management page.
     * 
     * @return RedirectResponse A redirect response to the users management page.
     */
    public function edit_user_submit()
    {
        // form validation
        $validation = $this->validate($this->_edit_user_validation());

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // get user id
        $id = Decrypt($this->request->getPost('hidden_id'));
        if (!$id) {
            return redirect()->to('/users_management');
        }

        // get input data
        $role = $this->request->getPost('select_role');
        $active = $this->request->getPost('radio_active');
        $blocked_until = $this->request->getPost('date_blocked_until');

        // prepare blocked until, if not empty
        if (!empty($blocked_until)) {
            $tmp = new DateTime($blocked_until);
            $tmp->setTime(23, 59, 59);

            $blocked_until = $tmp->format('Y-m-d H:i:s');
        } else {
            $blocked_until = null;
        }

        // update user
        $users_model = new UsersManagementModel();
        $users_model->update($id, [
            'roles'         => json_encode([$role]),
            'active'        => $active,
            'blocked_until' => $blocked_until,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        // redirect to users management
        return redirect()->to('/users_management');
    }

    /**
     * Deletes a user from the system.
     * 
     * This method deletes a user by marking their record as deleted in the database.
     * It first checks if the user ID is valid. If the ID is valid, it updates the user's
     * record in the database by setting the 'deleted_at' timestamp to the current date and time.
     * Finally, it redirects to the users management page.
     * 
     * @param string $enc_id The encrypted ID of the user to be deleted.
     * @return RedirectResponse A redirect response to the users management page.
     */
    public function delete_user($enc_id)
    {
        // check if user id is valid
        $id = Decrypt($enc_id);
        if (!$id) {
            return redirect()->to('/users_management');
        }

        $users_model = new UsersManagementModel();
        $users_model->update($id, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/users_management');
    }

    /**
     * Recovers a deleted user.
     * 
     * This method recovers a previously deleted user by removing the 'deleted_at' timestamp from their record
     * in the database. It first checks if the provided user ID is valid. If the ID is valid and corresponds to
     * a deleted user, it updates the user's record in the database by setting the 'deleted_at' field to null,
     * effectively marking the user as active again. Finally, it redirects to the users management page.
     * 
     * @param string $enc_id The encrypted ID of the user to be recovered.
     * @return RedirectResponse A redirect response to the users management page.
     */
    public function recover_user($enc_id)
    {
        // check if user id is valid
        $id = Decrypt($enc_id);
        if (!$id) {
            return redirect()->to('/users_management');
        }

        $users_model = new UsersManagementModel();
        $users_model->update($id, [
            'deleted_at' => null
        ]);

        return redirect()->to('/users_management');
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

    /**
     * Returns validation rules for creating a new user.
     * 
     * This method defines and returns an array of validation rules and corresponding 
     * error messages for creating a new user. It includes rules for username, name, 
     * email, phone, and role.
     * 
     * @return array An associative array of validation rules and error messages.
     */
    private function _new_user_validation()
    {
        return [
            'text_username' => [
                'label'  => 'Utilizador',
                'rules'  => 'required|min_length[3]|max_length[20]',
                'errors' => [
                    'required'   => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no minímo {param} caracteres',
                    'max_length' => 'O campo {field} deve ter no máximo {param} caracteres'
                ]
            ],
            'text_name' => [
                'label'  => 'Nome do utilizador',
                'rules'  => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required'   => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no minímo {param} caracteres',
                    'max_length' => 'O campo {field} deve ter no máximo {param} caracteres'
                ]
            ],
            'text_email' => [
                'label'  => 'E-mail',
                'rules'  => 'required|valid_email|min_length[5]|max_length[50]',
                'errors' => [
                    'required'    => 'O campo {field} é obrigatório',
                    'valid_email' => 'O campo {field} deve ter um e-mail válido',
                    'min_length'  => 'O campo {field} deve ter no minímo {param} caracteres',
                    'max_length'  => 'O campo {field} deve ter no máximo {param} caracteres'
                ]
            ],
            'text_phone' => [
                'label'  => 'Telefone',
                'rules'  => 'required|regex_match[/^[9]{1}\d{8}$/]',
                'errors' => [
                    'required'    => 'O campo {field} é obrigatório',
                    'regex_match' => 'O campo {field} deve ter um número de telefone válido',
                ]
            ],
            'select_role' => [
                'label'  => 'Cargo',
                'rules'  => 'required|in_list[admin,user]',
                'errors' => [
                    'required'   => 'O campo {field} é obrigatório',
                    'in_list'    => 'O campo {field} deve ter um valor válido',
                ]
            ],
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
        $data['purl'] = site_url('/auth/finish_registration/' . $data['code']);

        // config e-mail
        $email = \Config\Services::email();
        $email->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $email->setTo($data['email']);
        $email->setSubject('CigBurger - Conclusão de registo de utilizador');
        $email->setMessage(view('emails/email_finish_registration', $data));

        // send e-mail and return true or false
        return $email->send();
    }

    /**
     * Retrieves validation rules for editing user information.
     * 
     * This method defines validation rules for editing user information, including
     * the user's role, active status, and optional blocked until date. The rules are
     * defined for form validation purposes to ensure the integrity of user data updates.
     * 
     * @return array An array containing validation rules for editing user information.
     */
    private function _edit_user_validation()
    {
        return [
            'select_role' => [
                'label'  => 'Cargo',
                'rules'  => 'required|in_list[admin,user]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'in_list'  => 'O campo {field} deve conter um valor válido'
                ]
            ],
            'radio_active' => [
                'label'  => 'Utilizador ativo',
                'rules'  => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'in_list'  => 'O campo {field} deve conter um valor válido'
                ]
            ],
            'date_blocked_until' => [
                'label'  => 'Bloquear utilizador até',
                'rules'  => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'O campo {field} deve conter uma data válida no formato {param}'
                ]
            ]
        ];
    }
}
