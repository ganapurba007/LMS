<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionReactionToggled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $materialId;
    public int $discussionId;
    public string $action;
    public int $reactionsCount;
    public array $reactors;

    public function __construct(int $materialId, int $discussionId, string $action, int $reactionsCount, array $reactors)
    {
        $this->materialId = $materialId;
        $this->discussionId = $discussionId;
        $this->action = $action;
        $this->reactionsCount = $reactionsCount;
        $this->reactors = $reactors;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('material.' . $this->materialId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reaction.toggled';
    }
}
