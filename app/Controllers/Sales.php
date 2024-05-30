<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SalesModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class Sales extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Vendas',
            'page'  => 'Vendas'
        ];

        // check if there's a date interval filter in session
        $filter_date_interval = session()->get('filter_date_interval');
        if (!empty($filter_date_interval)) {
            $data['filter_date_interval'] = $filter_date_interval['start_date']->format('Y-m-d') . ' to ' . $filter_date_interval['end_date']->format('Y-m-d');
        }

        // get sales
        $sales_model = new SalesModel();
        $data['sales'] = $sales_model->get_sales($filter_date_interval);

        // datatables and apex charts
        $data['datatables'] = true;
        $data['apexcharts'] = true;

        // prepare data for apexcharts
        $data['sales_chart'] = $this->_prepare_sales_for_chart($data['sales'], 10);
        $data['sales_chart_columns'] = $this->_prepare_sales_for_chart($data['sales'], 20);

        return view('dashboard/sales/index', $data);
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

        return redirect()->to('/sales');
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

        return redirect()->to('/sales');
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

        return redirect()->to('/sales');
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PRIVATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Prepares sales data for chart display.
     * 
     * This method processes the provided sales data to be displayed on a chart. 
     * It ensures that only the last specified number of days of data are shown, 
     * and structures the data into labels (order dates) and series (total prices) for the chart.
     * 
     * @param array $data An array of sales data, where each element contains 'order_date' and 'total_price'.
     * @param int $limit The maximum number of days to display on the chart.
     * @return array An array with 'labels' and 'series' keys, ready for chart consumption.
     */
    private function _prepare_sales_for_chart($data, $limit)
    {
        // show only the last $limit days
        if (count($data) === 0) {
            return [
                'labels' => [],
                'series' => []
            ];
        }

        if (count($data) > $limit) {
            $data = array_slice($data, -$limit);
        }

        return [
            'labels' => array_column($data, 'order_date'),
            'series' => array_column($data, 'total_price')
        ];
    }
}
