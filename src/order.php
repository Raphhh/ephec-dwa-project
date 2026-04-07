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

function processToOrder(PDO $pdo, array $deliveryForm, array $basket): int
{
    $pdo->beginTransaction();

    try {
        $customerId = retrieveCustomerIdByEmail($pdo, $deliveryForm['email']);

        if (!$customerId) {
            $customerId = createCustomer(
                $pdo,
                $deliveryForm['email'],
                $deliveryForm['firstname'],
                $deliveryForm['lastname']
            );
        }

        $addressId = createAddress(
            $pdo,
            $customerId,
            $deliveryForm['street'],
            $deliveryForm['postal'],
            $deliveryForm['city'],
            $deliveryForm['country']
        );

        $orderId = createOrder(
            $pdo,
            $customerId,
            $addressId,
            $basket['total']['htva'],
            $basket['total']['tvac']
        );

        foreach ($basket['items'] as $item) {
            createOrderLine(
                $pdo,
                $orderId,
                $item['product']['id'],
                $item['quantity'],
                $item['product']['price_htva'],
                $item['total_htva']
            );
            decreaseProductStock($pdo, $item['product']['id'], $item['quantity']);
        }

        $pdo->commit();
        return $orderId;

    } catch (Throwable $exception) {
        $pdo->rollBack();
        return 0;
    }
}
