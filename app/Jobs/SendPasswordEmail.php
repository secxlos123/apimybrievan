<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The variable has instance of App\Models\User.
     *
     * @var array
     */
    public $user;

    /**
     * This unique password.
     *
     * @var string
     */
    public $password;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = ['email' => $this->user->email, 'password' => $this->password];
        
        return Mail::to($mail['email'])
            ->send(new ResetPassword($mail));
    }
}
