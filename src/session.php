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

function renewCSRFToken(): string
{
    $_SESSION['csrf_token'] = uniqid();
    return retrieveCSRFToken();
}

function retrieveCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        renewCSRFToken();
    }
    return $_SESSION['csrf_token'];
}
