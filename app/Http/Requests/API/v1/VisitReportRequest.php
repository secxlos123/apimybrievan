<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\BaseRequest;

class VisitReportRequest extends BaseRequest
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
        return [
            'eform_id' => 'required|exists:eforms,id|unique:visit_reports,eform_id',
            // 'visitor_name' => 'required',
            // 'place' => 'required',
            // 'date' => 'required|date|after_or_equal:today',
            // 'name' => 'required',
            // 'job' => 'required',
            // 'phone' => 'required',
            // 'account' => 'required',
            // 'amount' => 'required',
            'npwp_number' => 'required',
            'purpose_of_visit' => 'required',
            'visit_result' => 'required',
            //'source_income'=>'required',
            'source' => 'required|in:fixed,nonfixed',
            'income' => 'required_if:source,nonfixed',
            'income_salary' => 'required_if:source,fixed',
            'income_allowance' => '',
            'mutations' => 'required_if:source,nonfixed',
            'mutations.*.bank' => 'required_if:source,nonfixed',
            'mutations.*.number' => 'required_if:source,nonfixed',
            'mutations.*.file' => 'required_if:source,nonfixed',
            'mutations.*.tables' => 'required_if:source,nonfixed',
            'mutations.*.tables.*.date' => 'required_if:source,nonfixed',
            'mutations.*.tables.*.amount' => 'required_if:source,nonfixed',
            'mutations.*.tables.*.type' => 'required_if:source,nonfixed',
            'mutations.*.tables.*.note' => '',
            'status_id'=>'required|in:1,2,3',
            'other_document'=>'mimes:jpeg,png,jpg,zip,pdf',
            'pros' => 'required',
            'cons' => 'required',
            'photo_with_customer' => 'required|image|mimes:jpg,jpeg,png',
            'seller_name' => '',
            'seller_address' => '',
            'seller_phone' => '',
            'selling_price' => '',
            'reason_for_sale' => '',
            'relation_with_seller' => '',
            'npwp'=> 'required|mimes:jpeg,png,jpg,zip,pdf',
            'salary_slip'=> 'required_if:source,fixed|mimes:jpeg,png,jpg,zip,pdf',
            'family_card'=> 'required|mimes:jpeg,png,jpg,zip,pdf',
            'marrital_certificate'=> 'mimes:jpeg,png,jpg,zip,pdf',
            'divorce_certificate'=> 'mimes:jpeg,png,jpg,zip,pdf',
            'offering_letter'=> 'required|mimes:jpeg,png,jpg,zip,pdf',
            'proprietary'=> 'mimes:jpeg,png,jpg,zip,pdf',
            'building_permit'=> 'mimes:jpeg,png,jpg,zip,pdf',
            'down_payment'=> 'mimes:jpeg,png,jpg,zip,pdf',
            'building_tax'=> 'mimes:jpeg,png,jpg,zip,pdf',
            'recommended'=> 'required|in:yes,no',
            'recommendation'=> '',
            'legal_bussiness_document'=>'required_if:source,non-fixed|mimes:jpeg,png,jpg,zip,pdf',
            'license_of_practice'=>'mimes:jpeg,png,jpg,zip,pdf',
            'work_letter'=>'required_if:source,fixed|mimes:jpeg,png,jpg,zip,pdf',
            'title' => 'required',
            'employment_status' => 'required',
            'age_of_mpp' => 'required',
            'loan_history_accounts' => 'required',
            'religion' => 'required',
            'office_phone' => 'required|string|regex:/^[0-9]+$/|min:9|max:12'
        ];
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        $this->merge(  [ 'eform_id' => $this->eform_id ] );
        return $this->all();
    }
}
