<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->optional()->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'billing_cycle' => $this->faker->randomElement(['monthly', 'quarterly', 'yearly']),
            'trial_days' => $this->faker->numberBetween(0, 30),
            'is_for_company' => $this->faker->boolean,
            'price_per_user' => $this->faker->optional()->randomFloat(2, 5, 50),
            'allows_additional_professional_accounts' => $this->faker->boolean,
        ];
    }
}
