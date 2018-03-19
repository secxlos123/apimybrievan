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
        $message = 'Berhasil proses prescreening E-Form';

        $this->eform->update(
            generate_data_prescreening(
                $this->eform
                , $this->request
                , break_pefindo( $this->eform, $this->request )
            )
        );

        set_action_date($this->eform->id, 'eform-prescreening-update');

        // auto approve for VIP
        if ( $this->eform->is_clas_ready ) {
            $message .= ' dan ' . autoApproveForVIP( array(), $this->eform->id );
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
