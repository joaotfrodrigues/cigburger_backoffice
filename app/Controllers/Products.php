<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;

class Products extends BaseController
{
    public function index()
    {
        //  get products
        $product_model = new ProductModel();
        $products = $product_model->where('id_restaurant', session()->user['id_restaurant'])
            ->findAll();

        return view('dashboard/products/index', [
            'title' => 'Produtos',
            'page' => 'Produtos',
            'products' => $products,
        ]);
    }

    // -------------------------------------------------------------------------
    // new product
    // -------------------------------------------------------------------------
    public function new_product()
    {
        // get distinct categories
        $product_model = new ProductModel();

        $categories = $product_model->where('id_restaurant', session()->user['id_restaurant'])
            ->select('category')
            ->distinct()
            ->findAll();

        return view('dashboard/products/new_product_frm', [
            'title' => 'Produtos',
            'page' => 'Novo produto',
            'validation_errors' => session()->getFlashdata('validation_errors'),
            'categories' => $categories
        ]);
    }

    public function new_submit()
    {
        // form validation
        $validation = $this->validate([
            // product image
            'file_image' => [
                'label' => 'imagem do produto',
                'rules' => [
                    'uploaded[file_image]',
                    'mime_in[file_image,image/png]',
                    'max_size[file_image,200]'
                ],
                'errors' => [
                    'uploaded' => 'O campo {field} é obrigatório',
                    'mime_in' => 'O campo {field} deve ser uma imagem PNG',
                    'max_size' => 'O campo {field} deve ter no máximo 200KB'
                ]
            ],

            // input fields
            'text_name' => [
                'label' => 'nome do produto',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no mínimo 3 caracteres',
                    'max_length' => 'O campo {field} deve ter no máximo 100 caracteres'
                ]
            ],
            'text_description' => [
                'label' => 'descrição do produto',
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no mínimo 3 caracteres',
                    'max_length' => 'O campo {field} deve ter no máximo 200 caracteres'
                ]
            ],
            'text_category' => [
                'label' => 'categoria do produto',
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no mínimo 3 caracteres',
                    'max_length' => 'O campo {field} deve ter no máximo 50 caracteres'
                ]
            ],
            'text_price' => [
                'label' => 'preço do produto',
                'rules' => 'required|regex_match[/^\d+\,\d{2}$/]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'regex_match' => 'O campo {field} deve ser um número com o formato x,xx',
                ]
            ],
            'text_promotion' => [
                'label' => 'promoção do produto',
                'rules' => 'required|greater_than[-1]|less_than[100]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'greater_than' => 'O campo {field} deve ser um número maior que {param}',
                    'less_than' => 'O campo {field} deve ser um número menor que {param}',
                ]
            ],
            'text_initial_stock' => [
                'label' => 'estoque inicial do produto',
                'rules' => 'required|greater_than[99]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'greater_than' => 'O campo {field} deve ser um número maior que {param}',
                ]
            ],
            'text_stock_minimum_limit' => [
                'label' => 'limite mínimo de estoque do produto',
                'rules' => 'required|greater_than[99]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'greater_than' => 'O campo {field} deve ser um número maior que {param}',
                ]
            ]
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // validates if the image file is not equal to 'no_image.png'
        if ($this->request->getFile('file_image')->getName() === 'no_image.png') {
            return redirect()->back()->withInput()->with('validation_errors', ['file_image' => 'O campo imagem do produto é obrigatório']);
        }

        // verify if product already exists
        $product_model = new ProductModel();
        $product = $product_model->where('name', $this->request->getPost('text_name'))
            ->where('id_restaurant', session()->user['id_restaurant'])
            ->first();

        if ($product) {
            return redirect()->back()->withInput()->with('validation_errors', ['text_name' => 'Já existe outro produto com o mesmo nome.']);
        }

        // upload image
        $file_image = $this->request->getFile('file_image');
        $final_file_name = prefixed_product_file_name($file_image->getName());
        $file_image->move(ROOTPATH . 'public/assets/images/products', $final_file_name, true);

        // prepare data to insert
        $data = [
            'id_restaurant' => session()->user['id_restaurant'],
            'name' => $this->request->getPost('text_name'),
            'description' => $this->request->getPost('text_description'),
            'category' => $this->request->getPost('text_category'),
            'price' => preg_replace('/\,/', '.', $this->request->getPost('text_price')),
            'promotion' => $this->request->getPost('text_promotion'),
            'stock' => $this->request->getPost('text_initial_stock'),
            'stock_min_limit' => $this->request->getPost('text_stock_minimum_limit'),
            'image' => $final_file_name
        ];

        // insert data
        $product_model->insert($data);

        // redirect
        return redirect()->to('/products');
    }

    // -------------------------------------------------------------------------
    // edit product
    // -------------------------------------------------------------------------
    public function edit($id)
    {
        $id = Decrypt($id);
        if (empty($id)) {
            return redirect()->to('/products');
        }

        // get product data
        $product_model = new ProductModel();
        $product = $product_model->find($id);

        // get distinct categories
        $categories = $product_model->where('id_restaurant', session()->user['id_restaurant'])
            ->select('category')
            ->distinct()
            ->findAll();

        // check if the product image exists
        if (!file_exists('./assets/images/products/' . $product->image)) {
            $product->image = 'no_image.png';
        }

        return view('dashboard/products/edit_product_frm', [
            'title' => 'Produtos',
            'page' => 'Editar produto',
            'product' => $product,
            'categories' => $categories,
            'validation_errors' => session()->getFlashdata('validation_errors'),
            'server_error' => session()->getFlashdata('server_error'),
        ]);
    }

    public function edit_submit()
    {
        // form validation
        $validation = $this->validate([
            // input fields
            'text_name' => [
                'label' => 'nome do produto',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no mínimo 3 caracteres',
                    'max_length' => 'O campo {field} deve ter no máximo 100 caracteres'
                ]
            ],
            'text_description' => [
                'label' => 'descrição do produto',
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no mínimo 3 caracteres',
                    'max_length' => 'O campo {field} deve ter no máximo 200 caracteres'
                ]
            ],
            'text_category' => [
                'label' => 'categoria do produto',
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'min_length' => 'O campo {field} deve ter no mínimo 3 caracteres',
                    'max_length' => 'O campo {field} deve ter no máximo 50 caracteres'
                ]
            ],
            'text_price' => [
                'label' => 'preço do produto',
                'rules' => 'required|regex_match[/^\d+\,\d{2}$/]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'regex_match' => 'O campo {field} deve ser um número com o formato x,xx',
                ]
            ],
            'text_promotion' => [
                'label' => 'promoção do produto',
                'rules' => 'required|greater_than[-1]|less_than[100]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'greater_than' => 'O campo {field} deve ser um número maior que {param}',
                    'less_than' => 'O campo {field} deve ser um número menor que {param}',
                ]
            ],
            'text_stock_minimum_limit' => [
                'label' => 'limite mínimo de estoque do produto',
                'rules' => 'required|greater_than[99]',
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'greater_than' => 'O campo {field} deve ser um número maior que {param}',
                ]
            ]
        ]);

        // check if product id is OK
        $id = Decrypt($this->request->getPost('id_product'));
        if (empty($id)) {
            return redirect()->to('/products');
        }

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // validates if the image file is not equal to 'no_image.png'
        if ($this->request->getFile('file_image')->getName() === 'no_image.png') {
            return redirect()->back()->withInput()->with('validation_errors', ['file_image' => 'O campo imagem do produto é obrigatório']);
        }

        // check if the product already exists
        $product_model = new ProductModel();
        $product = $product_model->where('name', $this->request->getPost('text_name'))
            ->where('id_restaurant', session()->user['id_restaurant'])
            ->where('id !=', $id)
            ->first();
        if ($product) {
            return redirect()->back()->withInput()->with('server_error', 'Já existe outro produto com o mesmo nome');
        }

        // prepare data to update product
        $data = [
            'name' => $this->request->getPost('text_name'),
            'description' => $this->request->getPost('text_description'),
            'category' => $this->request->getPost('text_category'),
            'price' => preg_replace('/\,/', '.', $this->request->getPost('text_price')),
            'availability' => $this->request->getPost('check_available') ? 1 : 0, // checkbox
            'promotion' => $this->request->getPost('text_promotion'),
            'stock_min_limit' => $this->request->getPost('text_stock_minimum_limit')
        ];

        // check if the product image was changed
        $file_image = $this->request->getFile('file_image');
        if ($file_image->getName() !== '') {
            // prefix the image name
            $final_file_name = prefixed_product_file_name($file_image->getName());

            // upload image
            $file_image->move(ROOTPATH . 'public/assets/images/products', $final_file_name, true);

            // update image
            $data['image'] = $final_file_name;
        }

        // update product
        $product_model->update($id, $data);

        // redirect
        return redirect()->to('/products');
    }

    // -------------------------------------------------------------------------
    // delete product// -------------------------------------------------------------------------
    public function delete($enc_id)
    {
        $id = Decrypt($enc_id);

        if (empty($id)) {
            return redirect()->to('/products');
        }

        // check if product exists
        $product_model = new ProductModel();
        $product = $product_model->find($id);

        if (!$product) {
            return redirect()->to('/products');
        }

        // show delete confirmation
        return view('dashboard/products/delete_product', [
            'title' => 'Produtos',
            'page' => 'Eliminar produto',
            'product' => $product
        ]);
    }

    public function delete_confirm($enc_id)
    {
        $id = Decrypt($enc_id);

        if (empty($id)) {
            return redirect()->to('/products');
        }

        // check if product exists
        $product_model = new ProductModel();
        $product = $product_model->find($id);

        if (!$product) {
            return redirect()->to('/products');
        }

        // delete product
        $product_model->delete($id);

        // redirect
        return redirect()->to('/products');
    }
}
