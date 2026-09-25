<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProjectionController extends Controller
{
    public function showPublic(): InertiaResponse
    {
        return $this->renderProjection(false);
    }

    public function showAdmin(): InertiaResponse
    {
        return $this->renderProjection(true);
    }

    private function renderProjection(bool $isAdmin): InertiaResponse
    {
        $album = Album::query()->first();
        $storage = Storage::disk('public');
        $photos = $album === null ? collect() : $album->photos()->latest()->get();

        return Inertia::render('Boda/Projection', [
            'is_admin' => $isAdmin,
            'photos' => $photos->map(fn (Photo $photo): array => [
                'id' => $photo->id,
                'preview_url' => $storage->url($photo->preview_path),
                'created_at' => $photo->created_at?->toIso8601String(),
            ])->values(),
        ]);
    }
}
