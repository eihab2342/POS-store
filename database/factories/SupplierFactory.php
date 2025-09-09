<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'       => 'SUP' . strtoupper(Str::random(8)),
            'name'            => $this->faker->name(),
            'company_name'    => $this->faker->company(),
            'email'           => $this->faker->unique()->safeEmail(),
            'phone'           => $this->faker->unique()->phoneNumber(),
            'tax_number'      =>  'TAX' . strtoupper(Str::random(8)),
            'country'         => $this->faker->country(),
            'city'            => $this->faker->city(),
            'address'         => $this->faker->address(),
            'opening_balance' => $this->faker->randomFloat(2, 0, 10000),
            'current_balance' => $this->faker->randomFloat(2, 0, 20000),
            'is_active'       => $this->faker->boolean(),
            'notes'           => $this->faker->sentence(),
        ];
    }
}