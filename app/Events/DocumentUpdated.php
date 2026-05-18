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

    public $user;

    public function __construct($document, $user)
    {
        $this->document = $document;

        $this->user = $user;
    }

    public function broadcastOn(): array
    {
        return [

            new Channel(
                'document.' .
                $this->document->id
            )

        ];
    }

    public function broadcastAs(): string
    {
        return 'document.updated';
    }
}