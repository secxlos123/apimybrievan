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

class PrescreeningPefindo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The variable has instance of App\Models\EForm.
     *
     * @var array
     */
    public $eform;

    /**
     * The variable has instance of Illuminate\Http\Request.
     *
     * @var array
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( EForm $eform, $request )
    {
        $this->eform = $eform;
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
    }
}
