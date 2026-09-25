<?php

namespace App\Services;

use App\Events\PhotoDeleted;
use App\Models\Photo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoDeletionService
{
    public function delete(Photo $photo): void
    {
        $photoId = $photo->id;
        $paths = [
            $photo->original_path,
            $photo->thumbnail_path,
            $photo->preview_path,
        ];

        DB::transaction(function () use ($photo): void {
            $photo->delete();
        });

        Storage::disk('public')->delete($paths);
        PhotoDeleted::dispatch($photoId);
    }
}
