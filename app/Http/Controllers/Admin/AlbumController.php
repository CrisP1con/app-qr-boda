<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AlbumController extends Controller
{
    public function edit(): InertiaResponse
    {
        return Inertia::render('Admin/Configuration', [
            'album' => Album::query()->first(),
        ]);
    }

    public function update(UpdateAlbumRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $album = Album::query()->first();
        $oldCoverImage = $album?->cover_image;
        $coverImage = $request->file('cover_image');

        unset($validated['cover_image']);

        if ($coverImage !== null) {
            $validated['cover_image'] = $coverImage->store('boda/cover', 'public');
        }

        if ($album === null) {
            $validated['slug'] = Str::slug($validated['name']);
            Album::query()->create($validated);
        } else {
            $album->update($validated);
        }

        if ($coverImage !== null && $oldCoverImage !== null) {
            Storage::disk('public')->delete($oldCoverImage);
        }

        return redirect()
            ->route('admin.configuration.edit')
            ->with('success', 'La configuración del evento fue actualizada.');
    }

    public function toggleUpload(): RedirectResponse
    {
        $album = Album::query()->firstOrFail();
        $album->update([
            'upload_enabled' => ! $album->upload_enabled,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', $album->upload_enabled
                ? 'La recepción de fotografías fue abierta.'
                : 'La recepción de fotografías fue cerrada.');
    }
}
