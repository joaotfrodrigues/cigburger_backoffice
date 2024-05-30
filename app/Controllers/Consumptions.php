<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConsumptionsModel;
use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class Consumptions extends BaseController
{
    /**
     * Displays the consumption data for the current restaurant.
     * 
     * This method sets up the data required for the consumptions dashboard page. It checks 
     * for any date interval or category filters stored in the session and applies them 
     * when retrieving the consumption data. The method also fetches the available categories 
     * for filtering and prepares the data for the view.
     * 
     * @return View A view response displaying the consumptions data.
     */
    public function index()
    {
        $data = [
            'title' => 'Consumos',
            'page' => 'Consumos'
        ];

        // check if there's a date interval filter in session
        $filter_date_interval = session()->get('filter_date_interval');
        if (!empty($filter_date_interval)) {
            $data['filter_date_interval'] = $filter_date_interval['start_date']->format('Y-m-d') . ' to ' . $filter_date_interval['end_date']->format('Y-m-d');
        }

        // check if there's a category filter in session
        $filter_category = session()->get('filter_category');
        if (!empty($filter_category)) {
            $data['filter_category'] = $filter_category;
        } else {
            $data['filter_category'] = 'all';
        }

        // get consumptions information ( using filter if needed )
        $model = new ConsumptionsModel();

        // get products
        $data['products'] = $model->get_consumptions($data['filter_category'], $filter_date_interval);

        // load datatables from layout_main.php
        $data['datatables'] = true;

        // get categories
        $data['categories'] = $model->get_categories();

        return view('dashboard/consumptions/index', $data);
    }

    /**
     * Filters data based on a date interval and stores the interval in session flashdata.
     * 
     * This method retrieves a date interval from a POST request, processes it to 
     * create a start and end date, and then stores this interval in the session 
     * flashdata. The method supports both single-day and multi-day intervals, 
     * setting appropriate time boundaries for each day.
     * 
     * @return RedirectResponse A redirect response to the '/consumptions' page.
     */
    public function filter_date_interval()
    {
        // get flatpickr date interval

        // ex: 2024-05-01
        // ex: 2024-05-01 to 2024-05-29
        $tmp = explode(' to ', $this->request->getPost('text_date_interval'));

        $date_interval = [
            'start_date' => new DateTime($tmp[0]),
            'end_date' => null
        ];

        // check if only one day is selected
        if (isset($tmp[1])) {
            $date_interval['end_date'] = new DateTime($tmp[1]);
        } else {
            $date_interval['end_date'] = new DateTime($tmp[0]);
        }

        // set hours / minutes / seconds
        $date_interval['start_date']->setTime(0, 0, 0);
        $date_interval['end_date']->setTime(23, 59, 59);

        // placer filter in session
        session()->set('filter_date_interval', $date_interval);

        return redirect()->to('/consumptions');
    }

    /**
     * Resets the date interval filter for consumption data.
     * 
     * This method removes the 'filter_date_interval' session data, effectively 
     * clearing any previously set date range filter for consumption data. 
     * After resetting the filter, it redirects the user to the 'consumptions' page.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse A redirect response to the 'consumptions' page.
     */
    public function reset_date_interval()
    {
        session()->remove('filter_date_interval');

        return redirect()->to('/consumptions');
    }

    /**
     * Sets a category filter for consumption data.
     * 
     * This method decrypts the provided category and sets it in the session data as 
     * 'filter_category'. If the decrypted category is empty, it redirects the user to 
     * the 'consumptions' page without setting the filter. Otherwise, it sets the filter 
     * and redirects the user to the 'consumptions' page.
     * 
     * @param string $category The encrypted category to be set as a filter.
     * 
     * @return RedirectResponse A redirect response to the 'consumptions' page.
     */
    public function set_category($category)
    {
        $category = Decrypt($category);
        if (empty($category)) {
            return redirect()->to('/consumptions');
        }

        session()->set('filter_category', $category);
        return redirect()->to('/consumptions');
    }

    /**
     * Sets the date filter to the last seven days and redirects to the consumptions page.
     * 
     * This method sets a session variable 'filter_date_interval' with the start date as seven days ago 
     * and the end date as the current date. It then redirects the user to the consumptions page.
     * 
     * @return RedirectResponse A redirect response to the consumptions page.
     */
    public function last_seven_days()
    {
        session()->set('filter_date_interval', [
            'start_date' => new DateTime(date('Y-m-d', strtotime('-7 day'))),
            'end_date'   => new DateTime(date('Y-m-d'))
        ]);

        return redirect()->to('/consumptions');
    }

    /**
     * Resets all filters and redirects to the consumptions page.
     * 
     * This method removes the 'filter_date_interval' and 'filter_category' session variables, effectively
     * resetting any filters that were applied. It then redirects the user to the consumptions page.
     * 
     * @return RedirectResponse A redirect response to the consumptions page.
     */
    public function reset_all_filters()
    {
        session()->remove('filter_date_interval');
        session()->remove('filter_category');

        return redirect()->to('/consumptions');
    }

    public function product_details($enc_id)
    {
        // validate id
        $id = Decrypt($enc_id);
        if (empty($id)) {
            return redirect()->to('/consumptions');
        }

        $data = [
            'title' => 'Consumos',
        ];

        // get product information
        $model = new ProductModel();
        $data['product'] = $model->find($id);

        $data['page'] = 'Consumos - ' . $data['product']->name;

        // get consumptions information from the product
        $model = new ConsumptionsModel();
        $data['consumptions'] = $model->get_consumptions_by_id($id);

        // get total consumptions
        $consumption_column = array_column($data['consumptions'], 'quantity');
        $data['total_consumption'] = array_sum($consumption_column);

        // enable datatables library import
        $data['datatables'] = true;

        return view('dashboard/consumptions/product_details', $data);
    }
}
