<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;

class Stocks extends BaseController
{
    public function index()
    {
        // load all productsind
        $products_model = new ProductModel();
        $products = $products_model->where('id_restaurant', session()->user['id_restaurant'])
            ->findAll();

        return view('dashboard/stocks/index', [
            'title' => 'Stocks',
            'page' => 'Stocks',
            'products' => $products
        ]);
    }
}
