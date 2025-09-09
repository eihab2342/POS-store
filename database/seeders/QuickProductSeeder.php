<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuickProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ProductVariant::factory()->count(100)->create();

    }
}



        // // منتج 1
        // $shirt = Product::create([
        //     'name' => 'تيشيرت قطن',
        //     'brand' => 'LocalBrand',
        //     'tax_rate' => 14,
        //     'is_active' => true,
        // ]);

        // ProductVariant::create([
        //     'product_id' => $shirt->id,
        //     'sku' => 'TSH-RED-M',
        //     'size' => 'M',
        //     'color' => 'أحمر',
        //     'cost' => 100,
        //     'price' => 200,
        //     'stock_qty' => 10,
        //     'reorder_level' => 2,
        // ]);

        // ProductVariant::create([
        //     'product_id' => $shirt->id,
        //     'sku' => 'TSH-BLU-L',
        //     'size' => 'L',
        //     'color' => 'أزرق',
        //     'cost' => 110,
        //     'price' => 220,
        //     'stock_qty' => 8,
        //     'reorder_level' => 2,
        // ]);

        // // منتج 2
        // $pants = Product::create([
        //     'name' => 'بنطلون جينز',
        //     'brand' => 'LocalBrand',
        //     'tax_rate' => 14,
        //     'is_active' => true,
        // ]);

        // ProductVariant::create([
        //     'product_id' => $pants->id,
        //     'sku' => 'PANT-BLK-32',
        //     'size' => '32',
        //     'color' => 'أسود',
        //     'cost' => 150,
        //     'price' => 300,
        //     'stock_qty' => 5,
        //     'reorder_level' => 2,
        // ]);