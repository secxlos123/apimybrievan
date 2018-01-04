<?php

namespace App\Listeners\EForm;

use App\Events\EForm\VerifyEForm;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmationEFormCustomer;

class VerifyEFormCustomer
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  VerifyEForm  $event
     * @return void
     */
    public function handle( VerifyEForm $event )
    {
        $eform = $event->eform;
        $customer = $eform->customer;

        $mail = [
            'email' => $customer->email,
            'name' => $customer->fullname,
            'ref_number' => $eform->ref_number,
            'status' => $eform->response_status == 'approve' ? 'persetujuan' : 'konfirmasi'
        ];
        
        Mail::to( $mail[ 'email' ] )->send( new ConfirmationEFormCustomer( $mail ) );
    }
}
