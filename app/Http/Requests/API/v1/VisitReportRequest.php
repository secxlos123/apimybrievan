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
            'visitor_name' => 'required',
            'place' => 'required',
            'date' => 'required|date|after_or_equal:today',
            'name' => 'required',
            'job' => 'required',
            'phone' => 'required',
            'account' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'purpose_of_visit' => 'required',
            'result' => 'required',
            'source' => 'required|in:fixed,not_fixed',
            'income' => 'required_if:source,fixed',
            'income_salary' => 'required_if:source,fixed',
            'income_allowance' => 'required_if:source,fixed',
            'income_mutation_type' => 'required_if:source,fixed',
            'income_mutation_number' => 'required_if:source,fixed',
            'income_salary_image' => 'required_if:source,fixed',
            'business_income' => 'required_if:source,not_fixed',
            'business_mutation_type' => 'required_if:source,not_fixed',
            'bussiness_mutation_number' => 'required_if:source,not_fixed',
            'bussiness_other' => 'required_if:source,not_fixed',
            'mutation_bank' => 'required',
            'mutation_number' => 'required',
            'photo_with_customer' => 'required',
            'pros' => 'required',
            'cons' => 'required',
            'seller_name' => 'required',
            'seller_address' => 'required',
            'seller_phone' => 'required',
            'selling_price' => 'required|numeric',
            'reason_for_sale' => 'required',
            'relation_with_seller' => 'required',
            'mutations' => 'required|array',
            'mutations.*.date' => 'required|date',
            'mutations.*.amount' => 'required|numeric',
            'mutations.*.type' => 'required',
            'mutations.*.information' => 'required'
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
