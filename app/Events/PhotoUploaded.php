<?php

namespace App\Events;

use App\Models\Photo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PhotoUploaded implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Photo $photo) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel('wedding')];
    }

    public function broadcastAs(): string
    {
        return 'photo.uploaded';
    }

    /**
     * @return array<string, array<string, int|string|null>>
     */
    public function broadcastWith(): array
    {
        $storage = Storage::disk('public');

        return [
            'photo' => [
                'id' => $this->photo->id,
                'thumbnail_url' => $storage->url($this->photo->thumbnail_path),
                'preview_url' => $storage->url($this->photo->preview_path),
                'created_at' => $this->photo->created_at?->toIso8601String(),
                'can_delete' => false,
            ],
        ];
    }
}
