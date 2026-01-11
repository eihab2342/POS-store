<?php
namespace App\Services;

use Illuminate\Support\Str;
use App\Models\ProductVariant;

class SkuService
{
    public static function make(): string
    {
        do {
            $letters = Str::upper(Str::random(1)); // 3 حروف كابيتال
            $numbers = rand(0, 9999); // 0-9999
            $sku = $letters . str_pad($numbers, 4, '0', STR_PAD_LEFT); // ABC0001
        } while (ProductVariant::where('sku', $sku)->exists());

        return $sku;
    }
}
