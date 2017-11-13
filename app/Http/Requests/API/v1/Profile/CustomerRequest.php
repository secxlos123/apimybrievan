<?php

namespace App\Http\Requests\API\v1\Profile;

use App\Http\Requests\BaseRequest as FormRequest;

class CustomerRequest extends FormRequest
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

        if( $this->segment( 6 ) == 'personal' )
        {

        return [
             'nik' => 'required',
             'name' => 'required|alpha_spaces',
             'birth_place_id' => 'required|exists:cities,id',
             'birth_date' => 'required|date',
             'address' => 'required',
             'city_id' => 'required|numeric|exists:cities,id',
             'gender' => 'required|in:L,P',
             'citizenship_id' => 'required',
             'status' => 'required|in:0,1,2',
             'address_status'=>'required|in:0,1,3',
             // 'phone' => 'digits:12|numeric',
             'mobile_phone' => 'required|string|regex:/^[0-9]+$/|min:9|max:16',
             'identity' => 'image|mimes:jpg,jpeg,png',
             'mother_name'=>''
            ];
        }
        else if ($this->segment( 6 ) == 'work')
        {
             return [
            'type_id' => 'required',
            'work_id' => 'required',
            'company_name' => 'required',
            'position_id' => 'required',
            'work_field_id' => 'required',
            'work_duration'=>'required',
            'work_duration_month'=>'',
            'office_address'=>'required'
            ];
        }
        else if ($this->segment( 6 ) == 'avatar')
        {
             return [
            'image' => 'required|image|mimes:jpg,jpeg,png'
            ];
        }
         else if ($this->segment( 6 ) == 'financial')
        {
            return [
            'salary' => 'required',
            'other_salary' => 'required',
            'loan_installment' => 'required',
            'dependent_amount' => 'required',
            ];
            
        }
        else if ($this->segment( 6 ) == 'contact')
        {
            return [
            'emergency_contact' => 'required',
            'emergency_relation' => 'required',
            'emergency_name' => 'required',
            ];
        }
        else if ($this->segment( 6 ) == 'other')
        {
            return[
             // 'npwp'=>'required',
             // 'family_card'=>'required',
             // 'couple_identity'=>'required_if:status_id,2',
             // 'marrital_certificate'=>'required_if:status_id,2',
             // 'diforce_certificate'=>'required_if:status_id,3',
             // 'status_id'=>'required'   
            ];
        }
        elseif ($this->user()->inRole('developer')) 
        {    
            return[
             'name' => 'required|alpha_spaces',
            ];
        }


    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        
            if ( null !== $this->input('name') ) {
                    list($first_name, $last_name) = name_separator($this->input('name'));
                    $this->merge( compact( 'first_name', 'last_name' ) );
                } 

        return parent::getValidatorInstance();
    }

}
