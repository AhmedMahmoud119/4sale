<?php

namespace App\Strategies;

class ServiceOnlyStrategy implements CheckoutStrategy
{
    public function calculateTotal(float $subtotal): float
    {
        $service = $subtotal * 0.15;
        return $subtotal + $service;
    }
}

