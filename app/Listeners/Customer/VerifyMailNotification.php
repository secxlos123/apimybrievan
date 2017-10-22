<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CustomerVerify;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEFormCustomer;

class VerifyMailNotification
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
     * @param  CustomerVerify   $event
     * @return void
     */
    public function handle( CustomerVerify $event )
    {
        $customer = $event->customer;
        $eform = $event->eform;
        $user = $event->user;
        $nikah = '';
        $tempat= '';

        switch ($customer->status) {
            case 1:
                 $nikah = 'Belum Menikah';
                break;
            case 2:
                $nikah = 'Menikah';
                break;
            case 3:
                $nikah = 'Janda/Duda';
                break;
            default:
                # code...
                break;
        }

        switch ($customer->address_status) {
            case '0':
                 $tempat = 'Milik Sendiri';
                break;
            case '1':
                $tempat = 'Milik Orang Tua/Mertua atau Rumah Dinas';
                break;
            case '3':
                $tempat = 'Tinggal di Rumah Kontrakan';
                break;
            default:
                # code...
                break;
        }

        $mail = [
            'email' => $customer->email,
            'name' => $customer->fullname,
            'url' => env( 'MAIN_APP_URL', 'https://mybri.stagingapps.net' ) . '/eform/' . $eform->token,
            'nik' => $customer->nik,
            'address'=> $customer->address,
            'phone'=> $user->phone,
            'mobile_phone'=>$user->mobile_phone,
            'mother_name'=>$customer->mother_name,
            'birth_place_id'=> $customer->birth_place_city->name,
            'identity'=> $customer->identity,
            'gender'=>$user->gender,
            'status'=>$nikah,
            'address_status'=>$tempat,
            'citizenship_id'=>$customer->citizenship_id,
            'couple_nik'=>$customer->couple_nik,
            'couple_name'=>$customer->couple_name,
            'couple_identity'=>$customer->couple_identity,
            'couple_birth_place_id'=> $customer->couple_birth_place_city->name,
            'job_field_id'=>$customer->job_field_id,
            'job_type_id'=>$customer->job_type_id,
            'job_id'=>$customer->job_id,
            'company_name'=>$customer->company_name,
            'position'=>$customer->position,
            'work_duration'=>$customer->work_duration,
            'office_address'=>$customer->office_address,
            'salary'=>$customer->salary,
            'other_salary'=>$customer->other_salary,
            'loan_installment'=>$customer->loan_installment,
            'dependent_amount'=>$customer->dependent_amount,
            'couple_salary'=>$customer->couple_salary,
            'couple_other_salary'=>$customer->couple_other_salary,
            'couple_loan_installment'=>$customer->couple_loan_installment,
            'emergency_name'=>$customer->emergency_name,
            'emergency_contact'=>$customer->emergency_contact,
            'emergency_relation'=>$customer->emergency_relation
        ];
        
        Mail::to( $mail[ 'email' ] )->send( new VerificationEFormCustomer( $mail ) );
    }
}
