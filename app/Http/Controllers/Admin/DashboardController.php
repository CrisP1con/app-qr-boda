<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function index(): InertiaResponse
    {
        $album = Album::query()->first();

        return Inertia::render('Admin/Dashboard', [
            'album' => $album,
            'photoCount' => $album?->photos()->count() ?? 0,
            'storageUsedBytes' => $this->storageUsedBytes($album),
        ]);
    }

    private function storageUsedBytes(?Album $album): int
    {
        if ($album === null) {
            return 0;
        }

        $totalBytes = 0;

        foreach ($album->photos()->select(['original_path', 'thumbnail_path', 'preview_path'])->cursor() as $photo) {
            foreach ([$photo->original_path, $photo->thumbnail_path, $photo->preview_path] as $path) {
                if (Storage::disk('public')->exists($path)) {
                    $totalBytes += Storage::disk('public')->size($path);
                }
            }
        }

        return $totalBytes;
    }
}
