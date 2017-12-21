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

class CustomerRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer;
    public $password;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( User $customer, $password , $role)
    {
        $this->customer = $customer;
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel( 'channel-name' );
    }
}
