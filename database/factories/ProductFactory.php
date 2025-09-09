<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->words(2, true), // اسم المنتج
            'brand'      => $this->faker->company(),      // اسم البراند
            'category_id' => $this->faker->numberBetween(1, 5), // ID كاتيجوري (ممكن تغيّره)
            'is_active'  => $this->faker->boolean(),      // 0 أو 1
            'tax_rate'   => $this->faker->randomFloat(2, 0, 0.2), // نسبة الضريبة (0% - 20%)
        ];
    }
}