<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\User;

class CustomerRegister
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $activation_code;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( User $user, $activation_code )
    {
        $this->user = $user;
        $this->activation_code = $activation_code;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
