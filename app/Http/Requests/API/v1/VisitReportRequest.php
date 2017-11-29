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
            'source' => 'required|in:fixed,nonfixed',
            'income' => 'required_if:source,nonfixed',
            'income_salary' => 'required_if:source,fixed',
            'income_allowance' => 'required_if:source,fixed',
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
            'photo_with_customer' => 'required|image|mimes:jpg,jpeg,png',
            'seller_name' => '',
            'seller_address' => '',
            'seller_phone' => '',
            'selling_price' => '',
            'reason_for_sale' => '',
            'relation_with_seller' => '',
            'npwp'=> 'required|file',
            'salary_slip'=> 'required_if:source,fixed|file',
            'family_card'=> 'required|file',
            'marrital_certificate'=> 'file',
            'divorce_certificate'=> 'file',
            'offering_letter'=> 'required|file',
            'proprietary'=> 'file',
            'building_permit'=> 'file',
            'down_payment'=> 'file',
            'building_tax'=> 'file',
            'recommended'=> 'required|in:yes,no',
            'recommendation'=> '',
            'legal_bussiness_document'=>'required_if:source,non-fixed|file',
            'license_of_practice'=>'file',
            'work_letter'=>'required_if:source,fixed|file'
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
