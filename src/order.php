<?php

function validateDeliveryForm(array $form): bool
{
    $validations = [
        'email' => [
            'required' => true,
            'maxlength' => 191,
        ],
        'lastname' => [
            'required' => true,
            'maxlength' => 150,
        ],
        'firstname' => [
            'required' => true,
            'maxlength' => 150,
        ],
        'street' => [
            'required' => true,
            'maxlength' => 191,
        ],
        'postal' => [
            'required' => true,
            'maxlength' => 20,
        ],
        'city' => [
            'required' => true,
            'maxlength' => 150,
        ],
        'country' => [
            'required' => true,
            'maxlength' => 150,
        ],
    ];

    return validateForm($form, $validations);
}

function validateForm(array $form, array $validations): bool
{
    foreach ($validations as $name => $validation) {
        if (!empty($form[$name])) {
            if (strlen($form[$name]) > $validation['maxlength']) {
                return false;
            }
        } elseif ($validation['required']) {
            return false;
        }
    }
    return true;
}
