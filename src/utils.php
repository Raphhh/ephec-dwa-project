<?php

function addTva($price): float
{
    return $price * (1 + TVA);
}

function fomatTva(): string
{
    return (TVA * 100) . '%';
}

function formatPrice($price): string
{
    return number_format($price, 0, ',', ' ') . ' €';
}

function formatProductImageUrl($imagePath): string 
{
    return 'resources/products/' . $imagePath;
}
