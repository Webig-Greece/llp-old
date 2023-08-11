<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            // 'vat_number' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{9}'), // Example of a custom VAT number format
            'vat_number' => $this->faker->unique()->regexify('[0-9]{9}'),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'billing_address' => $this->faker->address,
            'subscription_plan_id' => null, // You can set this to a specific value or use a factory if you have a SubscriptionPlan model
            'subscription_expiry' => $this->faker->dateTimeBetween('now', '+1 year'), // Example of a timestamp within the next year
        ];
    }
}
