<?php

namespace App\DTOs;

class CartItem
{

    public function __construct(
        public int $variant_id,
        public string $name,
        public float $price,
        public ?string $sku = null,
        public int $qty = 1,
    ) {}
}