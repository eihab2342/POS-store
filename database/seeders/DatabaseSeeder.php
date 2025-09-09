<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductVariant;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        Supplier::factory(10)->create();
        Product::factory(20)->create();

        ProductVariant::factory(200)->make()->each(function ($variant) {
            $variant->product_id  = Product::inRandomOrder()->first()->id;
            $variant->supplier_id = Supplier::inRandomOrder()->first()->id;
            $variant->save();
        });
    }
}