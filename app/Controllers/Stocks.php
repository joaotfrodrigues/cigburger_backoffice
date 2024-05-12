<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\StockModel;
use CodeIgniter\HTTP\ResponseInterface;

class Stocks extends BaseController
{
    /**
     * Displays the stock management page in the dashboard.
     * 
     * This method retrieves all products associated with the current restaurant from the database
     * using the ProductModel class and renders the stock management page in the dashboard. It passes
     * the title, page name, and list of products to the view.
     * 
     * @return View The stock management page in the dashboard.
     */
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

    // -----------------------------------------------------------------------------------------------------------------
    // ADD STOCK
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Renders the form for adding stock to a product in the dashboard.
     * 
     * This method decrypts the encrypted ID of the product for which stock is to be added and
     * checks if it's valid. If the ID is empty, it redirects to the stocks page. Otherwise, it
     * retrieves the distinct suppliers within the stocks table that belong to the current restaurant
     * using the StockModel class. It also loads the product details from the database using the
     * ProductModel class. Finally, it renders the form for adding stock, passing the title, page name,
     * product details, list of stock suppliers, and any validation errors or server errors to the view.
     * 
     * @param string $enc_id The encrypted ID of the product for which stock is to be added.
     * @return View The form for adding stock to a product in the dashboard.
     */
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

    /**
     * Processes the submission of the form for adding stock to a product in the dashboard.
     * 
     * This method validates the form submission based on the defined validation rules using the
     * _stock_add_form_validation helper method. If validation fails, it redirects back to the add
     * stock form with the input data and validation errors. Otherwise, it retrieves the decrypted ID
     * of the product from the form data and checks if it's valid. If the ID is empty, it redirects
     * back with a server error message. Otherwise, it stores the stock movement in the database using
     * the StockModel class and increments the product stock using the ProductModel class. Finally, it
     * redirects to the stocks page.
     * 
     * @return Redirect Redirects to the stocks page after processing the form submission.
     */
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

    // -----------------------------------------------------------------------------------------------------------------
    // REMOVE STOCK
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Renders the page for removing stock from a product in the dashboard.
     * 
     * This method decrypts the provided ID of the product to be removed from the stock.
     * If the ID is empty, it redirects to the stocks page. Otherwise, it loads the product
     * information using the ProductModel class and renders the remove stock form view, passing
     * necessary data such as the product details, validation errors, and server errors.
     * 
     * @param string $enc_id The encrypted ID of the product whose stock is to be removed.
     * @return View|Redirect Renders the remove stock page or redirects to the stocks page if the ID is empty.
     */
    public function remove($enc_id)
    {
        $id = Decrypt($enc_id);

        if (empty($id)) {
            return redirect()->to('/stocks');
        }

        // load product
        $products_model = new ProductModel();
        $product = $products_model->find($id);

        return view('dashboard/stocks/remove_frm', [
            'title' => 'Stock',
            'page' => 'Remover stock',
            'product' => $product,
            'validation_errors' => session()->getFlashdata('validation_errors'),
            'server_error' => session()->getFlashdata('server_error')
        ]);
    }

    /**
     * Handles the submission of the form to remove stock from a product in the dashboard.
     * 
     * This method validates the form input using the _stock_remove_form_validation method.
     * If validation fails, it redirects back to the remove stock page with input data and validation errors.
     * It then checks if the decrypted ID of the product is valid. If not, it redirects back with a server error message.
     * The method retrieves post data such as the stock quantity, reason, and date.
     * It checks if the requested stock quantity to remove is available in the product's current stock.
     * If the requested quantity exceeds the available stock, it redirects back with a server error message.
     * Otherwise, it stores the stock movement in the StockModel and updates the product stock quantity accordingly.
     * Finally, it redirects to the stocks page.
     * 
     * @return Redirect Redirects to the stocks page after successfully removing stock or redirects back with errors.
     */
    public function remove_submit()
    {
        // form validation
        $validation = $this->validate($this->_stock_remove_form_validation());
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
        $text_reason = $this->request->getPost('text_reason');
        $text_date = $this->request->getPost('text_date');

        // check if stock is available
        $products_model = new ProductModel();
        $product = $products_model->find($id_product);

        if ($product->stock < intval($text_stock)) {
            return redirect()->back()->withInput()->with('server_error', 'O stock atual é inferior à quantidade que pretende remover');
        }

        // store stock movement
        $stocks_model = new StockModel();
        $stocks_model->insert([
            'id_product' => $id_product,
            'stock_quantity' => intval($text_stock),
            'stock_in_out' => 'OUT',
            'stock_supplier' => 'Owner',
            'reason' => $text_reason,
            'movement_date' => $text_date
        ]);

        // increment product stock
        $products_model->where('id', $id_product)
            ->set('stock', 'stock - ' . intval($text_stock), false)
            ->update();

        return redirect()->to('/stocks');
    }

    // -----------------------------------------------------------------------------------------------------------------
    // STOCK MOVEMENTS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Renders the stock movements page for a specific product in the dashboard.
     * 
     * This method decrypts the ID of the product.
     * If the decrypted ID is empty, it redirects to the stocks page.
     * It then loads the product data using the ProductModel.
     * Next, it retrieves distinct suppliers within the stocks table that belong to the current restaurant.
     * The method renders the movements view with data including the product and uses DataTables for displaying the movements from the product stock.
     * 
     * @param string $enc_id The encrypted ID of the product.
     * @param string|null $filter The optional filter parameter for filtering stock movements.
     * 
     * @return View Renders the movements view with the required data.
     */
    public function movements($enc_id, $filter = null)
    {
        $id = Decrypt($enc_id);

        if (empty($id)) {
            return redirect()->to('/stocks');
        }

        // load product
        $products_model = new ProductModel();
        $product = $products_model->find($id);

        // get distinct suppliers within stocks table that belongs to this restaurant
        $stocks_model = new StockModel();
        $stock_suppliers = $stocks_model->getStocksSupplier(session()->user['id_restaurant']);

        return view('dashboard/stocks/movements', [
            'title' => 'Stock',
            'page' => 'Movimentos de stock',
            'product' => $product,
            'datatables' => true,
            'movements' => $this->_stock_movements($id, $filter),
            'stock_suppliers' => $stock_suppliers,
            'filter' => empty($filter) ? '' : Decrypt($filter)
        ]);
    }

    /**
     * Exports the stock movements for a specific product to a CSV file.
     * 
     * This method decrypts the ID of the product.
     * If the decrypted ID is empty, it redirects to the stocks page.
     * It then retrieves stock movements for the specified product and exports them to a CSV file.
     * The CSV file contains columns for the movement date, quantity, operation, supplier, and observations.
     * 
     * @param string $enc_id The encrypted ID of the product.
     * 
     * @return void Downloads a CSV file with the stock movements.
     */
    public function export_csv($enc_id)
    {
        $id = Decrypt($enc_id);

        if (empty($id)) {
            return redirect()->to('/stocks');
        }

        // get stock movements for this product and export them to CSV
        $stocks_model = new StockModel();
        $movements = $stocks_model->where('id_product', $id)
            ->orderBy('movement_date', 'DESC')
            ->findAll();

        // download CSV file with stock movements
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="movimentos.csv"');

        $output = fopen('php://output', 'w');

        // header
        fputcsv($output, ['Data do movimento', 'Quantidade', 'Operação', 'Fornecedor', 'Observações']);

        // data
        foreach ($movements as $movement) {
            fputcsv($output, [
                $movement->movement_date,
                $movement->stock_quantity,
                $movement->stock_in_out,
                $movement->stock_supplier,
                $movement->reason,
            ]);
        }

        fclose($output);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PRIVATE FUNCTIONS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Defines the form validation rules for adding stock.
     * 
     * This method returns an array of validation rules for adding stock.
     * It specifies rules for the product ID, stock quantity, supplier, and movement date.
     * The product ID must be provided and numeric.
     * The stock quantity must be provided, numeric, and greater than zero.
     * The supplier must be provided.
     * The movement date must be provided and in a valid date format (Y-m-d H:i).
     * 
     * @return array The form validation rules for adding stock.
     */
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

    /**
     * Defines the form validation rules for removing stock.
     * 
     * This method returns an array of validation rules for removing stock.
     * It specifies rules for the product ID, stock quantity, and movement date.
     * The product ID must be provided and numeric.
     * The stock quantity must be provided, numeric, and greater than zero.
     * The movement date must be provided and in a valid date format (Y-m-d H:i).
     * 
     * @return array The form validation rules for removing stock.
     */
    private function _stock_remove_form_validation()
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

    /**
     * Retrieves the stock movements for a specific product with optional filtering.
     * 
     * This method loads the stock movements for the specified product ID.
     * It supports optional filtering based on different criteria such as 'IN', 'OUT', or a specific supplier.
     * The method decrypts the provided filter if it's not empty and applies the appropriate filter.
     * If no filter is provided, it retrieves all stock movements for the product.
     * 
     * @param int $id_product The ID of the product for which stock movements are retrieved.
     * @param string $filter The optional filter parameter for filtering stock movements.
     * 
     * @return array An array containing the stock movements filtered according to the provided criteria.
     */
    private function _stock_movements($id_product, $filter)
    {
        // load product stock movements with 10000 records limit
        $stocks_model = new StockModel();
        $movements = [];

        $filter = Decrypt($filter);

        // filters       
        switch ($filter) {
            case '':
                $movements = $stocks_model->where('id_product', $id_product)
                    ->orderBy('movement_date', 'DESC')
                    ->findAll(10000);
                break;
            case 'IN':
                $movements = $stocks_model->where('id_product', $id_product)
                    ->where('stock_in_out', 'IN')
                    ->orderBy('movement_date', 'DESC')
                    ->findAll(10000);
                break;
            case 'OUT':
                $movements = $stocks_model->where('id_product', $id_product)
                    ->where('stock_in_out', 'OUT')
                    ->orderBy('movement_date', 'DESC')
                    ->findAll(10000);
                break;
            case substr($filter, 0, 6) === 'stksup':
                $supplier = substr($filter, 7);
                $movements = $stocks_model->where('id_product', $id_product)
                    ->where('stock_supplier', $supplier)
                    ->orderBy('movement_date', 'DESC')
                    ->findAll(10000);
                break;
        }

        return $movements;
    }
}
