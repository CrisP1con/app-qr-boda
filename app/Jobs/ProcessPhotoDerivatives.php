<?php

namespace App\Jobs;

use App\Events\PhotoDeleted;
use App\Events\PhotoUploaded;
use App\Models\Photo;
use App\Services\PhotoImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessPhotoDerivatives implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * @var array<int, int>
     */
    public array $backoff = [5, 15, 30];

    public function __construct(public int $photoId) {}

    public function handle(PhotoImageService $photoImageService): void
    {
        $photo = Photo::query()->find($this->photoId);

        if ($photo === null) {
            return;
        }

        $photoImageService->process($photo);
        PhotoUploaded::dispatch($photo->fresh());
    }

    public function failed(?Throwable $exception): void
    {
        $photo = Photo::query()->find($this->photoId);

        if ($photo === null) {
            return;
        }

        Storage::disk('public')->delete([
            $photo->original_path,
            $photo->thumbnail_path,
            $photo->preview_path,
        ]);
        $photo->delete();
        PhotoDeleted::dispatch($this->photoId);
    }
}
