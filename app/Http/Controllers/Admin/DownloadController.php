<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class DownloadController extends Controller
{
    public function originals(): StreamedResponse
    {
        $album = Album::query()->first();
        $photos = $album === null
            ? collect()
            : $album->photos()->select(['id', 'original_path', 'original_filename'])->orderBy('id')->get();

        return response()->streamDownload(function () use ($photos): void {
            $storage = Storage::disk('public');
            $zip = new ZipStream(
                outputName: 'boda-originales.zip',
                sendHttpHeaders: false,
            );

            foreach ($photos as $photo) {
                if (! $storage->exists($photo->original_path)) {
                    continue;
                }

                $zip->addFileFromPath(
                    fileName: $this->archiveFilename($photo->id, $photo->original_filename),
                    path: $storage->path($photo->original_path),
                );
            }

            $zip->finish();
        }, 'boda-originales.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }

    private function archiveFilename(int $photoId, string $originalFilename): string
    {
        $safeFilename = preg_replace('/[^\pL\pN._-]+/u', '-', str_replace('\\', '-', $originalFilename));
        $safeFilename = trim((string) $safeFilename, '.-');

        return $photoId.'-'.($safeFilename !== '' ? $safeFilename : 'fotografia');
    }
}
