<?php

if (!function_exists('display_error')) {
    /**
     * Display errors from form validation.
     *
     * @param string $field The field for which the error message is being displayed.
     * @param array $errors An array containing all the validation errors.
     * @return string|null HTML snippet with the error message, or null if no errors.
     */
    function display_error($field, $errors)
    {
        if (empty($errors)) {
            return;
        }

        if (array_key_exists($field, $errors)) {
            return '<div class="text-danger fw-bold">
                        <small>
                            <i class="fa-regular fa-circle-xmark me-1"></i>'
                . $errors[$field] .
                '</small>
                    </div>';
        }
    }
}

if (!function_exists('calculate_promotion')) {
    /**
     * Calculate product promotion.
     *
     * @param float $value The original price of the product.
     * @param float $discount The discount percentage.
     * @return float The discounted price of the product.
     */
    function calculate_promotion($value, $discount)
    {
        if ($discount === 0) {
            return $value;
        }

        // round to 2 decimal places
        return round($value - ($value * $discount) / 100, 2);
    }
}

if (!function_exists('normalize_price')) {
    /**
     * Replace . for , in price format.
     *
     * @param float $price The price to be formatted.
     * @return string The formatted price.
     */
    function normalize_price($price)
    {
        return number_format($price, 2, ',', '.');
    }
}

if (!function_exists('prefixed_product_file_name')) {
    /**
     * Create a prefix for the file name with the restaurant id.
     *
     * @param string $file_name The original file name.
     * @return string The prefixed file name.
     */
    function prefixed_product_file_name($file_name)
    {
        $prefix = 'rest_' . str_pad(session()->user['id_restaurant'], 5, '0', STR_PAD_LEFT);

        return $prefix . '_' . $file_name;
    }
}

if (!function_exists('stock_movement_select_filter')) {
    /**
     * Verify if option is selected.
     *
     * @param mixed $filter The current value of the dropdown.
     * @param mixed $option The specified option value.
     * @return string The HTML attribute "selected" if the values match, otherwise an empty string.
     */
    function stock_movement_select_filter($filter, $option)
    {
        if ($filter == $option) {
            return 'selected';
        } else {
            return '';
        }
    }
}

if (!function_exists('set_selected')) {
    /**
     * Sets the "selected" attribute for a dropdown option based on the comparison of two values.
     * 
     * This function compares the given value with the selected value. If they are equal, it returns 
     * "selected", indicating that the option should be selected in a dropdown menu.
     * 
     * @param mixed $value The value to compare.
     * @param mixed $selected The selected value.
     * 
     * @return string Returns "selected" if the given value matches the selected value, otherwise returns an empty string.
     */
    function set_selected($value, $selected)
    {
        if ($value === $selected) {
            return "selected";
        }

        return "";
    }
}
