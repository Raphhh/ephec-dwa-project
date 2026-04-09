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
    saveCSRFTokenIntoSession(uniqid());
    return retrieveCSRFToken();
}

function retrieveCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        saveCSRFTokenIntoSession('');
    }
    return $_SESSION['csrf_token'];
}

function saveCSRFTokenIntoSession(string $token): void
{
    $_SESSION['csrf_token'] = $token;
}
