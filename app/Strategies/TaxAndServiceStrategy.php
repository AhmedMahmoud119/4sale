<?php

namespace App\Strategies;

class TaxAndServiceStrategy implements CheckoutStrategy
{
    public function calculateTotal(float $subtotal): float
    {
        $tax = $subtotal * 0.14;
        $service = $subtotal * 0.20;
        return $subtotal + $tax + $service;
    }
}

