<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\BaseRequest;
use App\Models\CustomerDetail;
use App\Models\User;

class CustomerRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ( strtolower( $this->method() ) ) {
            case 'post':
                return [
                    'product_leads' => '',
                    'mobile_phone_couple' =>'required_if:product_leads,briguna,required|string|regex:/^[0-9]+$/|min:9|max:12',
                    'nik' => 'required|numeric|digits:16|unique:customer_details,nik',
                    'first_name' => 'required',
                    'last_name' => '',
                    'email' => 'required|email|unique:users,email',
                    'mobile_phone' => 'required|string|regex:/^[0-9]+$/|min:9|max:12',
                    'status' => 'required|in:1,2,3',
                    'mother_name' => 'required',
                    'birth_place_id' => 'required|numeric|exists:cities,id',
                    'birth_date' => 'required|date',
                    'identity' => 'required|image|mimes:jpg,jpeg,png',
                    'couple_nik' => 'required_if:status,2|numeric|digits:16',
                    'couple_name' => 'required_if:status,2',
                    'couple_birth_place_id' => 'required_if:status,2',
                    'couple_birth_date' => 'required_if:status,2|date',
                    // 'couple_identity' => 'required_if:status,2|image'
                ];
                break;

            case 'put':
                if( $this->segment( 6 ) == 'verify' ) {
                    return [
                        'cif_number' => '',
                        'form_id'=>'required_if:verify_status,verify',
                        'birth_place_id'=>'required_if:verify_status,verify|exists:cities,id',
                        'birth_date'=>'required_if:verify_status,verify',
                        'city_id'=>'required_if:verify_status,verify|exists:cities,id',
                        'status'=>'required_if:verify_status,verify|in:1,2,3',
                        'address_status'=>'required_if:verify_status,verify|in:0,1,3',
                        'citizenship_id'=>'required_if:verify_status,verify',
                        'citizenship_name'=>'required_if:verify_status,verify',
                        'email'=>'email',
                        'address'=>'required_if:verify_status,verify',
                        'mother_name'=>'required_if:verify_status,verify',
                        // 'phone'=>'string|regex:/^[0-9]+$/|max:15',
                        'mobile_phone'=>'required|string|regex:/^[0-9]+$/|min:9|max:12',
                        'couple_nik'=>'required_if:status,2',
                        'couple_name'=>'required_if:status,2',
                        'couple_birth_place_id'=>'required_if:status,2|exists:cities,id',
                        'couple_birth_date'=>'required_if:status,2',
                        'job_field_id'=>'required_if:verify_status,verify',
                        'job_field_name'=>'required_if:verify_status,verify',
                        'job_type_id'=>'required_if:verify_status,verify',
                        'job_type_name'=>'required_if:verify_status,verify',
                        'job_id'=>'required_if:verify_status,verify',
                        'job_name'=>'required_if:verify_status,verify',
                        'company_name'=>'required_if:verify_status,verify',
                        'position'=>'required_if:verify_status,verify',
                        'position_name'=>'required_if:verify_status,verify',
                        'work_duration'=>'required_if:verify_status,verify',
                        'work_duration_month' => 'required_if:verify_status,verify',
                        'office_address'=>'required_if:verify_status,verify',
                        'salary'=>'required_if:verify_status,verify',
                        //'other_salary'=>'required_if:verify_status,verify',
                        //'loan_installment'=>'required_if:verify_status,verify',
                        //'dependent_amount'=>'required_if:verify_status,verify',
                        'couple_salary'=>'',
                        'couple_other_salary'=>'',
                        'couple_loan_installment'=>'',
                        'couple_identity'=>'image|mimes:jpg,jpeg,png',
                        'emergency_name'=>'required_if:verify_status,verify',
                        'emergency_mobile_phone'=>'required_if:verify_status,verify',
                        'emergency_relation'=>'required_if:verify_status,verify',
                        'identity'=>'image|mimes:jpg,jpeg,png',
                        'first_name'=>'',
                        'last_name'=>'',
                        'verify_status'=>'required|in:verify,verified',
                        'gender'=>'required_if:verify_status,verify'

                        // 'verify_status' => 'required|in:verify,verified',
                        // 'cif_number' => '',
                        // 'first_name' => 'required_if:verify_status,verify',
                        // 'last_name' => '',
                        // 'gender' => 'required_if:verify_status,verify',
                        // // 'birth_place_id' => 'required_if:verify_status,verify',
                        // 'birth_date' => 'required_if:verify_status,verify',
                        // 'phone' => 'required_if:verify_status,verify',
                        // 'mobile_phone' => 'required_if:verify_status,verify|regex:(08)',
                        // 'address' => 'required_if:verify_status,verify',
                        // // 'citizenship_id' => 'required_if:verify_status,verify',
                        // // 'status' => 'required_if:verify_status,verify|in:1,2,3',
                        // 'address_status' => 'required_if:verify_status,verify',
                        // 'mother_name' => 'required_if:verify_status,verify'
                    ];
                } else {
                    return [
                        'nik' => 'required|numeric|digits:16|unique:customer_details,nik,' . $this->customer . ',user_id',
                        'email' => 'required|email|unique:users,email,' . $this->customer,
                        'first_name' => 'required',
                        'birth_place_id' => 'required',
                        'birth_date' => 'required|date',
                        'address' => 'required',
                        'gender' => 'required|in:L,P',
                        'city_id' => 'required|numeric|exists:cities,id',
                        //'phone' => 'required',
                        'citizenship_id' => 'required',
                        'citizenship_name' => 'required',
                        'status' => 'required|in:1,2,3',
                        'couple_nik' => 'required_if:status,2|numeric|digits:16',
                        'couple_name' => 'required_if:status,2',
                        'couple_birth_place_id' => 'required_if:status,2',
                        'couple_birth_date' => 'required_if:status,2|date',
                        // 'couple_identity' => 'required_if:status,2|image',
                        'address_status' => 'required',
                        'mother_name' => 'required',
                        'mobile_phone' => 'required|string|regex:/^[0-9]+$/|min:9|max:12',
                        'emergency_contact' => 'required',
                        'emergency_relation' => 'required',
                        'emergency_name' => 'required',
                        'job_type_id' => 'required',
                        'job_type_name' => 'required',
                        'job_id' => 'required',
                        'job_name' => 'required',
                        'company_name' => 'required',
                        'job_field_id' => 'required',
                        'job_field_name' => 'required',
                        'position' => 'required',
                        'work_duration' => 'required',
                        'office_address' => 'required',
                        'salary' => 'required|numeric',
                        //'other_salary' => 'required|numeric',
                        //'loan_installment' => 'required',
                        //'dependent_amount' => 'required',
                        'salary_slip' => 'required|file',
                        'identity' => 'image|mimes:jpg,jpeg,png',
                        'image' => 'image|mimes:jpg,jpeg,png',
                        'npwp' => 'required|file',
                        'bank_statement' => 'required|file',
                        'family_card' => 'required|file',
                        'marrital_certificate' => 'required_if:status,2|file',
                        'diforce_certificate' => 'required_if:status,2|file'
                    ];
                }
                break;

            default:
                return [
                    //
                ];
                break;
        }
    }

     /**
     * [messages description]
     * @author erwan.akse@wgs.co.id
     * @return [type] [description]
     */
    public function messages()
    {
        $email = '';
        $nik =  isset($this->nik)?$this->nik:NULL;
        if ($nik != NULL) {
            $detail = CustomerDetail::where('nik','=',$nik)->first();
            if (count($detail) != 0) {
                $user = User::find($detail->user_id);
                $email = $user->email;
                if (count($user->eforms) > 0) {
                    return [
                        'nik.unique' => 'Nomor Induk Kartu Penduduk Sedang Melakukan Pengajuan Dengan No Ref '.$user->eforms->ref_number,
                    ];
                }
            }

        }
        return [
            'nik.unique' => 'Nomor Induk Kartu Penduduk Telah Digunakan Oleh Email '.$email,
        ];
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        if( $this->has( 'status' ) & $this->status != '2' ) {
            return $this->except( [ 'couple_nik', 'couple_name', 'couple_birth_place_id', 'couple_birth_date', 'couple_identity' ] );
        }
        return $this->all();
    }
}
