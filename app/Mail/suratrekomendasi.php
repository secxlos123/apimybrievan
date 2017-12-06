<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class suratrekomendasi extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
//	public $mail;
    public function __construct()
    {

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {
            
		$file = storage_path('app/PDF/Surat_Kuasa_Potong_Upah.pdf');
		$file2 = storage_path('app/PDF/Surat_Rekomendasi_Atasan.pdf');
		 $k = $this->view('mails.suratrekomendasi')->attach($file, [
        'as' => 'Surat Kuasa Potong Gaji',
        'mime' => 'application/pdf',
    ])->attach($file2, [
        'as' => 'Surat Rekomendasi',
        'mime' => 'application/pdf',
    ]);
print_r($k);die();
 //       if (env('APP_ENV') == 'production') {
 //           return $this->view( 'mails.example', $this->mail );
 //       }

  //      return $this->view( 'mails.example', $this->mail );
            } catch (Exception $e) {
            return $e;
        }

    }
}
