<?php

namespace App\Filters;

use App\Libraries\ApiResponse;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use function PHPUnit\Framework\throwException;

class ApiKeyAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // check if the request header contains an encrypted hash key
        $data = $request->getHeaderLine('X-API-CREDENTIALS');
        $project_id = null;
        $api_key = null;

        try {
            // decrypt the hash key
            $encrypter = \Config\Services::encrypter();
            $credentials = json_decode($encrypter->decrypt(hex2bin($data)), true);

            $project_id = $credentials['project_id'];
            $api_key = $credentials['api_key'];

            // check in the database if the hash key is valid
            $db = \Config\Database::connect();
            $builder = $db->table('restaurants')
                ->where('project_id', $project_id)
                ->where('deleted_at', null)
                ->get();

            if ($builder->getNumRows() === 0) {
                // if the hash key is invalid, return a 404 Unauthorized response
                $response = new ApiResponse();
                echo $response->set_response_error(401, 'Unauthorized request');
                die(1);
            }

            // check if the api key is valid
            $row = $builder->getRow();
            if (!password_verify($api_key, $row->api_key)) {
                // if the api key is invalid, return a 404 Unauthorized response
                $response = new ApiResponse();
                echo $response->set_response_error(401, 'Unauthorized request');
                die(1);
            }
        } catch (\Exception $e) {
            // if the hash key is invalid, return a 404 Unauthorized response
            $response = new ApiResponse();
            echo $response->set_response_error(401, $e->getMessage());
            die(1);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
