<?php

session_start();

function retrieveBasketFromSession(): array
{
    if (empty($_SESSION['basket'])) {
        saveBasketIntoSession([]);
    }
    return $_SESSION['basket'];
}

function saveBasketIntoSession(array $basket): void
{
    $_SESSION['basket'] = $basket;
}
