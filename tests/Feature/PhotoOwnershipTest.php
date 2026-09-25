<?php

namespace Tests\Feature;

use App\Events\PhotoDeleted;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PhotoOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_delete_a_photo_belonging_to_the_current_upload_token(): void
    {
        Storage::fake('public');
        Event::fake([PhotoDeleted::class]);
        $album = Album::factory()->create();
        $token = Str::random(64);
        $photo = Photo::factory()->for($album)->create(['upload_token' => $token]);
        Storage::disk('public')->put($photo->original_path, 'original');
        Storage::disk('public')->put($photo->thumbnail_path, 'thumbnail');
        Storage::disk('public')->put($photo->preview_path, 'preview');

        $response = $this->withCookie('upload_token', $token)
            ->delete("/boda/fotos/{$photo->id}");

        $response->assertRedirect('/boda/fotos');
        $this->assertModelMissing($photo);
        Storage::disk('public')->assertMissing($photo->original_path);
        Storage::disk('public')->assertMissing($photo->thumbnail_path);
        Storage::disk('public')->assertMissing($photo->preview_path);
        Event::assertDispatched(PhotoDeleted::class, fn (PhotoDeleted $event): bool => $event->photoId === $photo->id);
    }

    public function test_guest_cannot_delete_a_photo_owned_by_another_upload_token(): void
    {
        $album = Album::factory()->create();
        $photo = Photo::factory()->for($album)->create(['upload_token' => Str::random(64)]);

        $response = $this->withCookie('upload_token', Str::random(64))
            ->delete("/boda/fotos/{$photo->id}");

        $response->assertForbidden();
        $this->assertModelExists($photo);
    }

    public function test_guest_can_delete_owned_photo_after_reception_is_closed(): void
    {
        $album = Album::factory()->create(['upload_enabled' => false]);
        $token = Str::random(64);
        $photo = Photo::factory()->for($album)->create(['upload_token' => $token]);

        $response = $this->withCookie('upload_token', $token)
            ->delete("/boda/fotos/{$photo->id}");

        $response->assertRedirect('/boda/fotos');
        $this->assertModelMissing($photo);
    }
}
