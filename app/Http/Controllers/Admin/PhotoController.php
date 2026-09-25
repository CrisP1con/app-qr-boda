<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Photo;
use App\Services\PhotoDeletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PhotoController extends Controller
{
    public function index(): InertiaResponse
    {
        $album = Album::query()->first();
        $photos = $album === null
            ? collect()
            : $album->photos()->latest()->get();

        return Inertia::render('Admin/Photos/Index', [
            'photos' => $photos->map(function (Photo $photo): array {
                return [
                    'id' => $photo->id,
                    'preview_url' => Storage::disk('public')->url($photo->preview_path),
                    'original_filename' => $photo->original_filename,
                    'size' => $photo->size,
                    'created_at' => $photo->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function destroy(Photo $photo, PhotoDeletionService $photoDeletionService): RedirectResponse
    {
        $album = Album::query()->first();

        abort_if($album === null || $photo->album_id !== $album->id, 404);

        $photoDeletionService->delete($photo);

        return redirect()
            ->route('admin.photos.index')
            ->with('status', 'La fotografía fue eliminada.');
    }
}
