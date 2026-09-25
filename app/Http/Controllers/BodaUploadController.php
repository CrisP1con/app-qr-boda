<?php

namespace App\Http\Controllers;

use App\Events\PhotoUploaded;
use App\Http\Requests\UploadPhotosRequest;
use App\Jobs\ProcessPhotoDerivatives;
use App\Models\Album;
use App\Services\PhotoDeletionService;
use App\Services\PhotoImageService;
use App\Services\UploadTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

class BodaUploadController extends Controller
{
    public function create(Request $request, UploadTokenService $uploadTokenService): InertiaResponse
    {
        $album = Album::query()->first();
        $uploadTokenService->remember($uploadTokenService->resolve($request));

        return Inertia::render('Boda/Upload', [
            'album' => $album === null ? null : [
                'name' => $album->name,
                'upload_enabled' => $album->upload_enabled,
            ],
        ]);
    }

    public function store(
        UploadPhotosRequest $request,
        PhotoImageService $photoImageService,
        PhotoDeletionService $photoDeletionService,
        UploadTokenService $uploadTokenService,
    ): RedirectResponse {
        $album = Album::query()->firstOrFail();

        if (! $album->upload_enabled) {
            return back()->withErrors([
                'photos' => 'La recepción de fotografías está cerrada.',
            ]);
        }

        $uploadToken = $uploadTokenService->resolve($request);

        foreach ($request->file('photos') as $photoFile) {
            $storedPhoto = $photoImageService->storeOriginal($photoFile);

            $photo = $album->photos()->create([
                ...$storedPhoto,
                'upload_token' => $uploadToken,
                'original_filename' => $photoFile->getClientOriginalName(),
            ]);

            try {
                if (config('queue.default') === 'sync') {
                    $photoImageService->process($photo);
                    PhotoUploaded::dispatch($photo->fresh());
                } else {
                    ProcessPhotoDerivatives::dispatch($photo->id);
                }
            } catch (Throwable $exception) {
                $photoDeletionService->delete($photo);

                throw $exception;
            }
        }

        $uploadTokenService->remember($uploadToken);

        return redirect()->route('boda.upload.thank-you');
    }

    public function thankYou(): InertiaResponse
    {
        $album = Album::query()->firstOrFail();

        return Inertia::render('Boda/UploadThankYou', [
            'name' => $album->name,
            'message' => $album->thank_you_message,
            'upload_enabled' => $album->upload_enabled,
        ]);
    }
}
