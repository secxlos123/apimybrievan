<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\Customer;
use App\Models\EForm;
use App\Models\User;

class CustomerVerify
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer;
    public $eform;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( Customer $customer, EForm $eform )
    {
        $this->customer = $customer;
        $this->eform = $eform;
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
