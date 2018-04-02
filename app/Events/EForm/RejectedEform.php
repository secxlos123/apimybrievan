<?php

namespace App\Events\EForm;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\EForm;

class RejectedEform
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $eform;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( EForm $eform )
    {
        $this->eform = $eform;
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
