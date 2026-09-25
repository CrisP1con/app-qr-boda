<?php

namespace Tests\Feature;

use App\Events\PhotoUploaded;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_photographs_are_stored_processed_and_published(): void
    {
        Storage::fake('public');
        Config::set('queue.default', 'sync');
        Event::fake([PhotoUploaded::class]);
        $album = Album::factory()->create();
        $file = UploadedFile::fake()->image('moment.jpg', 20, 20);

        $response = $this->post('/boda/fotos', ['photos' => [$file]]);

        $photo = Photo::query()->firstOrFail();
        $response->assertRedirect('/boda/gracias')->assertCookie('upload_token');
        Storage::disk('public')->assertExists($photo->original_path);
        Storage::disk('public')->assertExists($photo->thumbnail_path);
        Storage::disk('public')->assertExists($photo->preview_path);
        $this->assertSame($album->id, $photo->album_id);
        $this->assertSame('moment.jpg', $photo->original_filename);
        Event::assertDispatched(PhotoUploaded::class, fn (PhotoUploaded $event): bool => $event->photo->is($photo));
    }

    public function test_invalid_format_is_rejected_without_creating_a_photo(): void
    {
        Storage::fake('public');
        $album = Album::factory()->create();
        $file = UploadedFile::fake()->create('document.gif', 10, 'image/gif');

        $response = $this->post('/boda/fotos', ['photos' => [$file]]);

        $response->assertSessionHasErrors('photos.0');
        $this->assertDatabaseCount('photos', 0);
        $this->assertTrue($album->exists);
    }

    public function test_more_than_twenty_photographs_are_rejected(): void
    {
        Album::factory()->create();
        $files = [];

        for ($index = 0; $index < 21; $index++) {
            $files[] = UploadedFile::fake()->image("photo-{$index}.jpg", 10, 10);
        }

        $response = $this->post('/boda/fotos', ['photos' => $files]);

        $response->assertSessionHasErrors('photos');
        $this->assertDatabaseCount('photos', 0);
    }

    public function test_photograph_over_twenty_megabytes_is_rejected(): void
    {
        Album::factory()->create();
        $file = UploadedFile::fake()->create('large.jpg', 20 * 1024 + 1, 'image/jpeg');

        $response = $this->post('/boda/fotos', ['photos' => [$file]]);

        $response->assertSessionHasErrors('photos.0');
        $this->assertDatabaseCount('photos', 0);
    }

    public function test_closed_reception_rejects_new_photographs_in_backend(): void
    {
        Album::factory()->create(['upload_enabled' => false]);
        $file = UploadedFile::fake()->image('closed.jpg', 10, 10);

        $response = $this->post('/boda/fotos', ['photos' => [$file]]);

        $response->assertSessionHasErrors(['photos' => 'La recepción de fotografías está cerrada.']);
        $this->assertDatabaseCount('photos', 0);
    }
}
