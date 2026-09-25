<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAndAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_boda_gallery_and_projection_pages_are_accessible(): void
    {
        Album::factory()->create();

        $this->get('/boda')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Boda/Show'));

        $this->get('/boda/fotos')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Boda/Photos/Index'));

        $this->get('/boda/proyeccion')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Boda/Projection'));
    }

    public function test_unauthenticated_users_are_redirected_from_administrative_routes(): void
    {
        $album = Album::factory()->create();
        $photo = Photo::factory()->for($album)->create();

        foreach (['/admin', '/admin/fotos', '/admin/qr', '/admin/proyeccion', '/admin/download'] as $route) {
            $this->get($route)->assertRedirect('/login');
        }

        $this->post('/admin/album/toggle-upload')->assertRedirect('/login');
        $this->delete("/admin/fotos/{$photo->id}")->assertRedirect('/login');
    }
}
