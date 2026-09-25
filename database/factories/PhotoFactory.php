<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'album_id' => Album::factory(),
            'upload_token' => Str::random(64),
            'original_path' => 'boda/originals/'.Str::uuid().'.jpg',
            'thumbnail_path' => 'boda/thumbnails/'.Str::uuid().'.jpg',
            'preview_path' => 'boda/previews/'.Str::uuid().'.jpg',
            'original_filename' => fake()->word().'.jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(1_024, 20 * 1_024 * 1_024),
        ];
    }
}
