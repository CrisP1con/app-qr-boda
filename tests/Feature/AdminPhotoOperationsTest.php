<?php

namespace Tests\Feature;

use App\Events\PhotoDeleted;
use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPhotoOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_delete_any_photo(): void
    {
        Storage::fake('public');
        Event::fake([PhotoDeleted::class]);
        $user = User::factory()->create();
        $album = Album::factory()->create();
        $photo = Photo::factory()->for($album)->create();
        Storage::disk('public')->put($photo->original_path, 'original');
        Storage::disk('public')->put($photo->thumbnail_path, 'thumbnail');
        Storage::disk('public')->put($photo->preview_path, 'preview');

        $response = $this->actingAs($user)->delete("/admin/fotos/{$photo->id}");

        $response->assertRedirect('/admin/fotos');
        $this->assertModelMissing($photo);
        Storage::disk('public')->assertMissing($photo->original_path);
        Event::assertDispatched(PhotoDeleted::class);
    }

    public function test_authenticated_admin_can_download_only_originals_as_zip(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $album = Album::factory()->create();
        $photo = Photo::factory()->for($album)->create(['original_filename' => 'recuerdo.jpg']);
        Storage::disk('public')->put($photo->original_path, 'original');
        Storage::disk('public')->put($photo->thumbnail_path, 'thumbnail');
        Storage::disk('public')->put($photo->preview_path, 'preview');

        $response = $this->actingAs($user)->get('/admin/download');

        $response->assertDownload('boda-originales.zip');
        $response->assertHeader('Content-Type', 'application/zip');
        $this->assertStringContainsString('recuerdo.jpg', $response->streamedContent());
        $this->assertStringNotContainsString('thumbnail', $response->streamedContent());
        $this->assertStringNotContainsString('preview', $response->streamedContent());
    }

    public function test_authenticated_admin_can_generate_png_and_svg_qr_codes(): void
    {
        User::factory()->create();
        $this->actingAs(User::query()->first());

        $this->get('/admin/qr')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/QrCode'));

        $this->get('/admin/qr/png')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->get('/admin/qr/svg')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml');
    }
}
