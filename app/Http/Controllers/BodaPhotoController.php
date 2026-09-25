<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use App\Services\PhotoDeletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BodaPhotoController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $album = Album::query()->first();
        $photos = $album === null
            ? collect()
            : $album->photos()->latest()->get();

        return Inertia::render('Boda/Photos/Index', [
            'photos' => $photos->map(fn (Photo $photo): array => $this->photoPayload($photo, $request))->values(),
            'upload_enabled' => $album?->upload_enabled ?? false,
        ]);
    }

    public function show(Request $request, Photo $photo): InertiaResponse
    {
        $album = Album::query()->first();

        abort_if($album === null || $photo->album_id !== $album->id, 404);

        return Inertia::render('Boda/Photos/Show', [
            'photo' => $this->photoPayload($photo, $request),
        ]);
    }

    public function destroy(Request $request, Photo $photo, PhotoDeletionService $photoDeletionService): RedirectResponse
    {
        $album = Album::query()->first();

        abort_if($album === null || $photo->album_id !== $album->id, 404);
        abort_unless($this->belongsToUploadToken($request, $photo), 403);

        $photoDeletionService->delete($photo);

        return redirect()
            ->route('boda.photos.index')
            ->with('status', 'La fotografía fue eliminada.');
    }

    /**
     * @return array<string, int|string|null>
     */
    private function photoPayload(Photo $photo, Request $request): array
    {
        $storage = Storage::disk('public');

        return [
            'id' => $photo->id,
            'thumbnail_url' => $storage->url($photo->thumbnail_path),
            'preview_url' => $storage->url($photo->preview_path),
            'created_at' => $photo->created_at?->toIso8601String(),
            'can_delete' => $this->belongsToUploadToken($request, $photo),
        ];
    }

    private function belongsToUploadToken(Request $request, Photo $photo): bool
    {
        $uploadToken = $request->cookie('upload_token');

        return is_string($uploadToken)
            && $uploadToken !== ''
            && hash_equals($photo->upload_token, $uploadToken);
    }
}
