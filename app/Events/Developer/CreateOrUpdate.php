<?php

namespace App\Events\Developer;

use App\Models\Developer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateOrUpdate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * [$developer description]
     * @var [type]
     */
    public $developer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Developer $developer)
    {
        $this->developer = $developer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel( "channel-name" );
    }
}
