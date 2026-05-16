<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function broadcastOn(): array
    {
        return [

            new Channel(
                'document.' . $this->document->id
            )

        ];
    }

    public function broadcastAs(): string
    {
        return 'document.updated';
    }
}