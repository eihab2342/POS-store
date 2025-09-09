<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'supplier_id' => Supplier::factory(),
            'name'          => $this->faker->word(),
            'sku'           => strtoupper($this->faker->bothify('???###')), 
            'color'         => $this->faker->safeColorName(),
            'size'          => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'cost'          => $this->faker->randomFloat(2, 50, 200),
            'price'         => $this->faker->randomFloat(2, 100, 300),
            'stock_qty'     => $this->faker->numberBetween(0, 50),
            'reorder_level' => $this->faker->numberBetween(5, 15),
        ];
    }
}