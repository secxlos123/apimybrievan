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
        $customer = $event->customer->detail;
        $customer_data = $event->customer;
        $eform = $event->eform;

        $mail = [
            'email' => $customer_data->email,
            'name' => $customer_data->fullname,
            'url' => env( 'MAIN_APP_URL', 'https://mybri.stagingapps.net' ) . '/eform/' . $eform->token,
            'nik' => $customer->nik,
            'address'=> $customer->address,
            'city_id'=> $customer->city_id ? $customer->city->name : '',
            'phone'=> $customer_data->phone,
            'mobile_phone'=>$customer_data->mobile_phone,
            'mother_name'=>$customer->mother_name,
            'birth_date'=> $customer->birth_date,
            'birth_place_id'=> $customer->birth_place_city->name,
            'identity'=> $customer->identity,
            'gender'=>$customer_data->gender,
            'status'=>$customer->StatusHubungan,
            'address_status'=> $customer->StatusAddress,
            'citizenship_id'=>$customer->citizenship,
            'couple_nik'=>$customer->couple_nik,
            'couple_name'=>$customer->couple_name,
            'couple_identity'=>$customer->identity,
            'couple_birth_place_id'=> $customer->couple_birth_place_id ? $customer->couple_birth_place_city->name : '',
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
