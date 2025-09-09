<?php 

namespace App\Interfaces;

use App\DTOs\CartItem;

interface ProductVarientRepoInterface{
    public function findCartItemBySku(string $sku): ?CartItem;
    public function ensureStockAvailable(int $variantId, int $qty): void;
    public function decrementStock(int $variantId, int $qty): void; 
}