<?php

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

function calculate_promotion($value, $discount) {
    if ($discount === 0) {
        return $value;
    }

    // round to 2 decimal places
    return round($value - ($value * $discount ) / 100, 2);
}

?>
