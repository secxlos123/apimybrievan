<?php

namespace App\Listeners\EForm;

use App\Events\EForm\PrescreeningPefindo;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneratePefindo
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
     * @param  PrescreeningPefindo  $event
     * @return void
     */
    public function handle( PrescreeningPefindo $event )
    {
        $message = 'Berhasil proses prescreening E-Form';
        $eform = $event->eform;

        $eform->update(
            generate_data_prescreening(
                $eform
                , $event->request
                , break_pefindo( $eform, $event->request )
            )
        );

        set_action_date($eform->id, 'eform-prescreening-update');

        // auto approve for VIP
        if ( $eform->is_clas_ready ) {
            $message .= ' dan ' . autoApproveForVIP( array(), $eform->id );
        }

        \Log::info("=================== Dispatch Event Prescreening ========================");
        \Log::info( $eform->ref_number . " : " . $message );
        \Log::info("=================== End ========================");
    }
}
