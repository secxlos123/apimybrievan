<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\BaseRequest;

class GimmickRequest extends BaseRequest
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
                    /* 'gimmick_name' => 'required',
					'gimmick_level' => 'required|numeric',
					'area_level' => 'required|numeric',
					'segmen_level' => 'required|numeric',
					'mitra_kerjasama' => 'required',
					'mitra_kerjasama2' => 'required',
					'mitra_kerjasama3' => 'required',
					'mitra_kerjasama4' => 'required',
					'tgl_mulai' => 'required',
					'tgl_berakhir' => 'required',
					'payroll' => 'required|numeric',
					'admin_fee' => 'required|numeric',
					'admin_minimum' => 'required|numeric',
					'provisi_fee' => 'required|numeric',
					'waktu_minimum' => 'required|numeric',
					'waktu_maksimum' => 'required|numeric',
					'dir_rpc' => 'required|numeric',
					'asuransi_jiwa' => 'required',
					'suku_bunga' => 'required|numeric',
					'first_month' => 'required',
					'last_month' => 'required',
					'suku_bunga' => 'required|numeric',
					'pemutus_name' => 'required|numeric',
					'jabatan' => 'required|numeric', */
                     ];
                break;

            case 'put':
                if( $this->segment( 6 ) == 'verify' ) {
                    return [
                    'id' => 'required',
					'pefindo_score' => 'required|numeric',
                    ];
                } else {
                    return [
                    'id' => 'required',
					'pefindo_score' => 'required|numeric',
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
}
