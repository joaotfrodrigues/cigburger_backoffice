<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\StockModel;
use CodeIgniter\HTTP\ResponseInterface;

class Stocks extends BaseController
{
    public function index()
    {
        // load all products
        $products_model = new ProductModel();
        $products = $products_model->where('id_restaurant', session()->user['id_restaurant'])
            ->findAll();

        return view('dashboard/stocks/index', [
            'title' => 'Stocks',
            'page' => 'Stocks',
            'products' => $products
        ]);
    }

    public function add($enc_id)
    {
        $id = Decrypt($enc_id);

        if (empty($id)) {
            return redirect()->to('/stocks');
        }

        // get distinct suppliers within stocks table that belongs to this restaurant
        $stocks_model = new StockModel();
        $stock_suppliers = $stocks_model->getStocksSupplier(session()->user['id_restaurant']);

        // load product
        $products_model = new ProductModel();
        $product = $products_model->find($id);

        return view('dashboard/stocks/add_frm', [
            'title' => 'Stock',
            'page' => 'Adicionar stock',
            'product' => $product,
            'stock_suppliers' => $stock_suppliers,
            'validation_errors' => session()->getFlashdata('validation_errors'),
            'server_error' => session()->getFlashdata('server_error')
        ]);
    }

    public function add_submit()
    {
        // form validation
        $validation = $this->validate($this->_stock_add_form_validation());

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // check if id_producti is valid
        $id_product = Decrypt($this->request->getPost('id_product'));
        if (empty($id_product)) {
            return redirect()->back()->withInput()->with('server_error', 'Ocorreu um erro. Tente novamente');
        }

        // get post data
        $text_stock = $this->request->getPost('text_stock');
        $text_supplier = $this->request->getPost('text_supplier');
        $text_reason = $this->request->getPost('text_reason');
        $text_date = $this->request->getPost('text_date');

        // store stock movement
        $stocks_model = new StockModel();
        $stocks_model->insert([
            'id_product' => $id_product,
            'stock_quantity' => intval($text_stock),
            'stock_in_out' => 'IN',
            'stock_supplier' => $text_supplier,
            'reason' => $text_reason,
            'movement_date' => $text_date
        ]);

        // increment product stock
        $products_model = new ProductModel();
        $products_model->where('id', $id_product)
            ->set('stock', 'stock + ' . intval($text_stock), false)
            ->update();
        
        return redirect()->to('/stocks');
    }

    private function _stock_add_form_validation()
    {
        // stock form validation rules
        return [
            'id_product' => [
                'rules' => 'required'
            ],
            'text_stock' => [
                'label' => 'Quantidade',
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.',
                    'numeric' => 'O campo {field} deve conter apenas números.',
                    'greater_than' => 'O campo {field} deve conter um valor maior que {param}'
                ]
            ],
            'text_supplier' => [
                'label' => 'Fornecedor',
                'rules' => 'required',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.',
                ]
            ],
            // text_reason not required
            'text_date' => [
                'label' => 'Data do movimento',
                'rules' => 'required|valid_date[Y-m-d H:i]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório.',
                    'valid_date' => 'O campo {field} deve conter uma data válida (Y-m-d H:i)'
                ]
            ]
        ];
    }
}
