<?php

namespace Database\Factories;

use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Album>
 */
class AlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name().' & '.fake()->name(),
            'slug' => fake()->unique()->slug(),
            'event_date' => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'cover_image' => null,
            'message' => fake()->optional()->sentence(),
            'primary_color' => '#'.fake()->regexify('[A-Fa-f0-9]{6}'),
            'thank_you_message' => fake()->optional()->sentence(),
            'upload_enabled' => true,
        ];
    }
}
