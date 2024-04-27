<?php

// display errors from the forms
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

// calculate product promotion
function calculate_promotion($value, $discount)
{
    if ($discount === 0) {
        return $value;
    }

    // round to 2 decimal places
    return round($value - ($value * $discount ) / 100, 2);
}

// replace . for ,
function normalize_price($price)
{
    return number_format($price, 2, ',', '.');
}

// create a prefix for the file name with the restaurant id
function prefixed_product_file_name($file_name)
{
    $prefix = 'rest_' . str_pad(session()->user['id_restaurant'], 5, '0', STR_PAD_LEFT);

    return $prefix . '_' . $file_name;
}

?>
