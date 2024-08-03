<?php

namespace App\Strategies;

interface CheckoutStrategy
{
    public function calculateTotal(float $subtotal): float;
}

