<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Products extends BaseController
{
    public function index()
    {
        return view('dashboard/products/index', [
            'title' => 'Produtos',
            'page' => 'Produtos'
        ]);
    }

    public function new_product()
    {
        return view('dashboard/products/new_product_frm', [
            'title' => 'Produtos',
            'page' => 'Novo produto'
        ]);
    }
}
