<?php

namespace Database\Factories;

use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsCategorySubscription>
 */
class NewsCategorySubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'news_category_id' => NewsCategory::factory(),
            'user_email' => fake()->unique()->safeEmail(),
            'user_name' => fake()->name(),
            'unsubscription_key' => Str::random(),
        ];
    }
}
