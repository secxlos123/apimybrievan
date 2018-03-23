<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GeneratePefindoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The variable has instance of App\Models\EForm.
     *
     * @var array
     */
    protected $eform;

    /**
     * The variable has instance of Illuminate\Http\Request.
     *
     * @var array
     */
    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $eform, $request )
    {
        $this->eform = $eform;
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->eform->delay_prescreening == 1 && ENV('DELAY_PRESCREENING', false) ) {
            $this->eform->update(
                array_merge(
                    [
                        'delay_prescreening' => 2
                        , 'prescreening_name' => $this->eform->ao_name
                        , 'prescreening_position' => $this->eform->ao_position
                    ]
                    , generate_data_prescreening(
                        $this->eform
                        , $this->request
                        , break_pefindo( $this->eform, $this->request )
                    )
                )

            );

            set_action_date($this->eform->id, 'eform-prescreening-update');
            $message = 'Berhasil proses prescreening E-Form';

            // auto approve for VIP
            if ( $this->eform->is_clas_ready ) {
                $message .= ' dan ' . autoApproveForVIP( array(), $this->eform->id );
            }

            $detail = $this->eform;
            generate_pdf('uploads/'. $detail->nik, 'prescreening.pdf', view('pdf.prescreening', compact('detail')));

        } else {
            $message = 'Prescreening sudah pernah di lakukan';

        }

        \Log::info("=================== Dispatch Event Prescreening ========================");
        \Log::info( $this->eform->ref_number . " : " . $message );
        \Log::info("=================== End ========================");
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        \Log::info("=================== Dispatch Event Prescreening ========================");
        \Log::info( $exception );
        \Log::info("=================== End ========================");
    }
}
