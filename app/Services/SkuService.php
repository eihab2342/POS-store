<?php

// app/Services/SkuService.php
namespace App\Services;

use Illuminate\Support\Number;
use Illuminate\Support\Str;
use App\Models\ProductVariant;

class SkuService
{
    public static function make(ProductVariant $pv): string
    {
        $rand = Str::upper(Str::random(5));
        $rand_num = str_pad(rand(0, 999),3);
        return substr("$rand-$rand_num", 0, );
    }
}


// $cat = Str::upper(Str::slug(optional($pv->category)->code ?? 'GEN', '-'));
// $col = Str::upper(Str::slug($pv->color ?? 'N/A', '-'));
// $size = Str::upper(Str::slug($pv->size ?? 'ONE', '-'));
// $year = now()->format('Y');
