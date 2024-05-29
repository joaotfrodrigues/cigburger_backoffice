<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ApiRestaurantModel;
use CodeIgniter\HTTP\ResponseInterface;

class ApiRestaurant extends BaseController
{
    /**
     * Loads the API dashboard page with restaurant and machine information.
     * 
     * This function sets up the data required for the API dashboard view. It retrieves the 
     * restaurant's project ID and decrypted API key from the `ApiRestaurantModel` based on 
     * the restaurant ID stored in the session. It also fetches a list of all machines 
     * associated with the restaurant. The data is then passed to the view for rendering.
     * 
     * @return View The View renders the API dashboard view.
     */
    public function index()
    {
        $data = [
            'title' => 'API',
            'page' => 'API'
        ];

        // get information from the restaurant API data
        $model = new ApiRestaurantModel();
        $id_restaurant = session('user')['id_restaurant'];

        $results = $model->select('project_id, api_key_openssl')
            ->where('id', $id_restaurant)
            ->first();

        $data['project_id'] = $results->project_id;
        $data['api_key_openssl'] = Decrypt($results->api_key_openssl);

        // get list of all cigrequest machines operating for this restaurant
        $data['machines'] = $model->get_machines_from_restaurant($id_restaurant);

        return view('dashboard/api/index', $data);
    }

    /**
     * Downloads a configuration file for a specified machine.
     * 
     * This method decrypts the provided machine ID and retrieves the API credentials and 
     * project details for the restaurant associated with the current session. It prepares 
     * the configuration data and triggers a download of a `config.json` file containing 
     * this information.
     * 
     * @param string $enc_machine_id The encrypted machine ID.
     * 
     * @return ResponseInterface A response triggering the download of the `config.json` file.
     */
    public function download($enc_machine_id)
    {
        // decrypt the machine id
        $machine_id = Decrypt($enc_machine_id);
        if (empty($machine_id)) {
            return redirect()->to('/api_restaurant');
        }

        $model = new ApiRestaurantModel();

        $id_restaurant = session('user')['id_restaurant'];
        $results = $model->select('project_id, api_key_openssl')
            ->where('id', $id_restaurant)
            ->first();

        if (empty($results)) {
            return redirect()->to('/api_restaurant');
        }

        // prepare config.json data
        $data['api_url'] = base_url() . 'api/';
        $data['project_id'] = $results->project_id;
        $data['api_key'] = Decrypt($results->api_key_openssl);
        $data['machine_id'] = $machine_id;

        // download the config.json file, without escaping the data
        return $this->response->download('config.json', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * Creates a new machine ID and triggers the download of its configuration file.
     * 
     * This method generates a new machine ID consisting of 8 randomly shuffled uppercase letters.
     * It then encrypts this machine ID and calls the `download` method to generate and download 
     * the configuration file (`config.json`) for the newly created machine.
     * 
     * @return ResponseInterface A response triggering the download of the `config.json` file.
     */
    public function create_new_machine()
    {
        // create a new machine_id
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $machine_id = substr(str_shuffle($chars), 0, 8);

        // download the file
        return $this->download(Encrypt($machine_id));
    }

    /**
     * Changes the API key for the restaurant.
     * 
     * This method generates a new 32-character API key, encrypts it, and updates the 
     * `api_key` and `api_key_openssl` fields in the `restaurants` table for the 
     * currently logged-in restaurant. It then redirects the user to the 
     * `/api_restaurant` page.
     * 
     * @return RedirectResponse A redirect response to the `/api_restaurant` page.
     */
    public function change_api_key()
    {
        // change the restaurant API KEY 
        $id_restaurant = session('user')['id_restaurant'];

        // create a new API KEY
        $chars = str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 3);
        $api_key = substr(str_shuffle($chars), 0, 32);

        // update the API KEY
        $model = new ApiRestaurantModel();
        $model->update($id_restaurant, [
            'api_key' => password_hash($api_key, PASSWORD_DEFAULT),
            'api_key_openssl' => Encrypt($api_key),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // redirect to the API restaurant page
        return redirect()->to('/api_restaurant');
    }
}
