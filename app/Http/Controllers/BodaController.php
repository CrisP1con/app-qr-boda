<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BodaController extends Controller
{
    public function show(): InertiaResponse
    {
        $album = Album::query()->first();

        return Inertia::render('Boda/Show', [
            'album' => $album === null ? null : [
                'name' => $album->name,
                'event_date' => $album->event_date?->toDateString(),
                'cover_image_url' => $album->cover_image === null
                    ? null
                    : Storage::disk('public')->url($album->cover_image),
                'message' => $album->message,
                'primary_color' => $album->primary_color,
                'upload_enabled' => $album->upload_enabled,
            ],
        ]);
    }
}
