<?php

namespace Modules\Payment\Infrastruture\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_in_cents' => fake()->numberBetween(100, 10000),
            'status' => fake()->randomElement(['pending', 'paid', 'failed']),
            'payment_gateway' => fake()->randomElement(['PayBuddy', 'Stripe']),
            'payment_id' => fake()->uuid(),
        ];
    }
}
