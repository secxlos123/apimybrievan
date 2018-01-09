<?php

namespace App\Http\Requests\API\v1\Collateral;

use App\Http\Requests\BaseRequest as FormRequest;

class CreateOts extends FormRequest
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
        $rules = collect();
        return $rules
          ->merge($this->areaRules())
          ->merge($this->letterRules())
          ->merge($this->buildingRules())
          ->merge($this->valuationRules())
          ->merge($this->otherRules())
          ->merge($this->environmentRules())
          ->merge($this->sevenRules())
          ->merge($this->eightRules())
          ->merge($this->nineRules())
          ->merge($this->tenRules())
          ->toArray();
    }

    /**
     * area rules
     * @return array
     */
    private function areaRules()
    {
      return [
        'area.collateral_type' => 'required',
        'area.city_id' => 'required|regex:/^[\d.]+$/|exists:cities,id',
        'area.location' => 'required',
        'area.latitude' => 'required',
        'area.longtitude' => 'required',
        'area.district' => 'required',
        'area.sub_district' => 'required',
        'area.rt' => 'required',
        'area.rw' => 'required',
        'area.zip_code' => 'required',
        'area.distance' => 'required',
        'area.unit_type' => 'required',
        'area.distance_from' => 'required',
        'area.position_from_road' => 'required',
        'area.ground_type' => 'required',
        'area.ground_level' => 'required',
        'area.distance_of_position' => 'required|regex:/^[\d.]+$/',
        'area.north_limit' => 'required',
        'area.east_limit' => 'required',
        'area.south_limit' => 'required',
        'area.west_limit' => 'required',
        'area.another_information' => 'required',
        'area.surface_area' => 'required|regex:/^[\d.]+$/'
      ];
    }

    /**
     * letter rules
     * @return array
     */
    private function letterRules()
    {
      return [
        'letter.type' => 'required',
        'letter.authorization_land' => 'required',
        'letter.match_bpn' => 'required',
        'letter.match_area' => 'required',
        'letter.match_limit_in_area' => 'required',
        'letter.surface_area_by_letter' => 'required|regex:/^[\d.]+$/',
        'letter.number' => 'required',
        'letter.date' => 'required|date',
        'letter.on_behalf_of' => 'required',
        'letter.duration_land_authorization' => '',
        'letter.bpn_name' => 'required'
      ];
    }

    /**
     * bulding rules
     * @return array
     */
    private function buildingRules()
    {
      return [
        'building.permit_number' => 'required',
        'building.permit_date' => 'required|date',
        'building.on_behalf_of' => 'required',
        'building.type' => 'required',
        'building.count' => 'required',
        'building.spacious' => 'required',
        'building.year' => 'required',
        'building.description' => 'required',
        'building.north_limit' => 'required|regex:/^[\d.]+$/',
        'building.north_limit_from' => 'required',
        'building.east_limit' => 'required|regex:/^[\d.]+$/',
        'building.east_limit_from' => 'required',
        'building.south_limit' => 'required|regex:/^[\d.]+$/',
        'building.south_limit_from' => 'required',
        'building.west_limit' => 'required|regex:/^[\d.]+$/',
        'building.west_limit_from' => 'required'
      ];
    }

    /**
     * valuation rules
     * @return array
     */
    private function valuationRules()
    {
      return [
        'valuation.scoring_land_date' => 'required|date',
        'valuation.npw_land' => 'required',
        'valuation.nl_land' => 'required',
        'valuation.pnpw_land' => 'required',
        'valuation.pnl_land' => 'required',
        'valuation.scoring_building_date' => 'required|date',
        'valuation.npw_building' => 'required',
        'valuation.nl_building' => 'required',
        'valuation.pnpw_building' => 'required',
        'valuation.pnl_building' => 'required',
        'valuation.scoring_all_date' => 'required|date',
        'valuation.npw_all' => 'required',
        'valuation.nl_all' => 'required',
        'valuation.pnpw_all' => 'required',
        'valuation.pnl_all' => 'required'
      ];
    }

    /**
     * other rules
     * @return array
     */
    private function otherRules()
    {
      return [
        'other.bond_type' => 'required',
        'other.use_of_building_function' => 'required',
        'other.optimal_building_use' => 'required',
        'other.building_exchange' => 'required',
        'other.things_bank_must_know' => 'required',
        'other.image_area.*.image_data' => 'required|mimes:jpeg,png,jpg,zip,pdf',
        'other.image_condition_area' => 'mimes:jpeg,png,jpg,zip,pdf'
      ];
    }

    /**
     * environment rules
     * @return array
     */
    private function environmentRules()
    {
      return [
        'environment.designated_land' => 'required',
        'environment.designated_pln' => '',
        'environment.designated_phone' => '',
        'environment.designated_pam' => '',
        'environment.designated_telex' => '',
        'environment.other_designated' => 'required',
        'environment.nearest_location' => 'required',
        'environment.other_guide' => 'required',
        'environment.transportation' => 'required',
        'environment.distance_from_transportation' => 'required|regex:/^[\d.]+$/'
      ];
    }

     /**
     * environment rules
     * @return array
     */
    private function sevenRules()
    {
      return [
        'seven.collateral_status' => 'required',
        'seven.on_behalf_of' => '',
        'seven.ownership_number' => '',
        'seven.location' => '',
        'seven.address_collateral' => '',
        'seven.description' => 'required',
        'seven.ownership_status' => 'required',
        'seven.date_evidence' => 'required',
        'seven.village' => 'required',
        'seven.districts' => 'required'
      ];
    }

     /**
     * environment rules
     * @return array
     */
    private function eightRules()
    {
      return [
        'eight.liquidation_realization' => 'required',
        'eight.fair_market' => '',
        'eight.liquidation' => '',
        'eight.fair_market_projection' => '',
        'eight.liquidation_projection' => '',
        'eight.njop' => 'required',
        'eight.appraisal_by' => 'required',
        'eight.independent_appraiser' => '',
        'eight.date_assessment' => 'required',
        'eight.type_binding' => 'required',
        'eight.binding_number' => 'required',
        'eight.binding_value' => 'required',
      ];
    }

     /**
     * environment rules
     * @return array
     */
    private function nineRules()
    {
      return [
        'nine.certificate_status' => 'required',
        'nine.receipt_date' => '',
        'nine.information' => '',
        'nine.notary_status' => '',
        'nine.takeover_status' => '',
        'nine.credit_status' => 'required',
        'nine.skmht_status' => 'required',
        'nine.imb_status' => 'required',
        'nine.shgb_status' => 'required',
      ];
    }

    /**
     * environment rules
     * @return array
     */
    private function tenRules()
    {
      return [
        'ten.paripasu' => 'required',
        'ten.paripasu_bank' => '',
        'ten.insurance' => '',
        'ten.insurance_company' => '',
        'ten.insurance_value' => '',
        'ten.eligibility' => 'required',
      ];
    }
}
