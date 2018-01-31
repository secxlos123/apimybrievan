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
            'pros' => 'required',
            'cons' => 'required',
            'seller_name' => '',
            'seller_address' => '',
            'seller_phone' => '',
            'selling_price' => '',
            'reason_for_sale' => '',
            'relation_with_seller' => '',

            'npwp' => 'required_unless:use_reason,13|mimes:jpg,jpeg,png,gif,svg,pdf',
            'salary_slip' => 'required_unless:use_reason,13|mimes:jpg,jpeg,png,gif,svg,pdf',
            'legal_bussiness_document' => 'required_unless:use_reason,13|mimes:jpg,jpeg,png,gif,svg,pdf',
            'licence_of_practice' => 'required_unless:use_reason,13|mimes:jpg,jpeg,png,gif,svg,pdf',
            'work_letter' => 'required_unless:use_reason,13|mimes:jpg,jpeg,png,gif,svg,pdf',
            'family_card' => 'required_unless:use_reason,13|mimes:jpg,jpeg,png,gif,svg,pdf',
            'offering_letter' => 'required_unless:use_reason,13|mimes:jpg,jpeg,png,gif,svg,pdf',
            'photo_with_customer' => 'required_unless:use_reason,13|mimes:jpg,jpeg,png,gif,svg,pdf',

            'marrital_certificate' => 'mimes:jpg,jpeg,png,gif,svg,pdf',
            'divorce_certificate' => 'mimes:jpg,jpeg,png,gif,svg,pdf',
            'down_payment' => 'mimes:jpg,jpeg,png,gif,svg,pdf',
            'building_tax' => 'mimes:jpg,jpeg,png,gif,svg,pdf',
            'license_of_practice'=>'mimes:jpeg,png,jpg,zip,pdf',
            'other_document'=>'mimes:jpeg,png,jpg,zip,pdf',

            'building_permit' => 'required_if:use_reason,2,18|mimes:jpg,jpeg,png,gif,svg,pdf',
            'proprietary' => 'required_if:use_reason,2,18|mimes:jpg,jpeg,png,gif,svg,pdf',

            'recommended'=> 'required|in:yes,no',
            'recommendation'=> '',
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
