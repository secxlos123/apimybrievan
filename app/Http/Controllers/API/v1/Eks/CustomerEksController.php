<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\EForm;
use RestwsHc;

class CustomerEksController extends Controller
{
	public function __construct(Customer $customer, User $user, EForm $eform)
    {
        $this->user = $user;
        $this->customer = $customer;
        $this->eform = $eform;
    }
   
     /**
     * [customer description]
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getDataCustomer($ref_number, $ids)
    {
        $eform = $this->eform->where('ref_number',$ref_number)->where('id',$ids)->first();

        $customer = $this->customer->find($eform->user_id)->detail;
        $customer_data = $this->customer->find($eform->user_id);
        //$eform = $this->eform->where('user_id',$id)->first();

        $data = [
            'no_ref' => $eform->ref_number,
            'email' => $customer_data->email,
            'name' => $customer_data->fullname,
            'url' => env( 'MAIN_APP_URL', 'http://mybri.bri.co.id' ) . 'eform/' . $eform->token,
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
            'status_id'=>$customer->status_id,
            'status'=>$customer->status,
            'address_status'=> $customer->address_status,
            'citizenship_name'=>$customer->citizenship_name,
            'couple_nik'=>$customer->couple_nik,
            'couple_name'=>$customer->couple_name,
            'couple_identity'=>$customer->couple_identity,
            'couple_birth_date'=> $customer->couple_birth_date,
            'couple_birth_place_id'=> $customer->couple_birth_place_id ? $customer->couple_birth_place_city->name : '',
            'job_field_name'=>$customer->job_field_name,
            'job_type_name'=>$customer->job_type_name,
            'job_name'=>$customer->job_name,
            'company_name'=>$customer->company_name,
            'position_name'=>$customer->position_name,
            'work_duration'=>$customer->work_duration,
            'work_duration_month'=>$customer->work_duration_month,
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
            'emergency_relation'=>$customer->emergency_relation,
            'kpr'=>$eform->kpr,
            'source_income'=>$customer->source_income
        ];
      
    	return response()->success(['contents' => $data]);
    }
}
