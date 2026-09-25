<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReceptionControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_close_and_reopen_reception(): void
    {
        $user = User::factory()->create();
        $album = Album::factory()->create(['upload_enabled' => true]);

        $closeResponse = $this->actingAs($user)->post('/admin/album/toggle-upload');

        $closeResponse->assertRedirect('/admin');
        $this->assertDatabaseHas('albums', [
            'id' => $album->id,
            'upload_enabled' => false,
        ]);

        $this->post('/boda/fotos', [
            'photos' => [UploadedFile::fake()->image('closed.jpg', 10, 10)],
        ])->assertSessionHasErrors('photos');

        $openResponse = $this->actingAs($user)->post('/admin/album/toggle-upload');

        $openResponse->assertRedirect('/admin');
        $this->assertDatabaseHas('albums', [
            'id' => $album->id,
            'upload_enabled' => true,
        ]);
    }
}
