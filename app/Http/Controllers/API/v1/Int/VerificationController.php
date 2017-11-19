<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\Int\VerificationRequest;
use App\Models\EForm;
use RestwsHc;

class VerificationController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\API\v1\Int\VerificationRequest  $request
     * @param  int  $eform_id
     * @return \Illuminate\Http\Response
     */
    public function show( VerificationRequest $request, $eform_id )
    {
        $eform = EForm::findOrFail( $eform_id );
        $customer = $eform->customer;

        return response()->success( [
            'message' => 'Sukses',
            'contents' => [
                'customer' => [
                    'id' => $customer->id,
                    'nik' => $eform->nik,
                    'is_completed' => $customer->is_completed,
                    'name' => $customer->full_name,
                    'gender' => $customer->gender,
                    'birth_place_id' => $customer->detail->birth_place_id,
                    'birth_place' => $customer->detail->birth_place_id ? $customer->detail->birth_place_city->name : '',
                    'birth_date' => $customer->detail->birth_date,
                    'phone' => $customer->phone,
                    'mobile_phone' => $customer->mobile_phone,
                    'address' => $customer->detail->address,
                    'current_address' => $customer->detail->current_address,
                    'citizenship_id' => $customer->detail->citizenship_id,
                    'citizenship_name' => $customer->detail->citizenship_name,
                    'status' => $customer->detail->status_id,
                    'status_name' => $customer->detail->status,
                    'address_status' => $customer->detail->address_status_id,
                    'address_status_name'=> $customer->detail->address_status,
                    'mother_name' => $customer->detail->mother_name,
                    'email' => $customer->email,
                    'city_id'=> $customer->detail->city_id,
                    'city' => $customer->detail->city_id ? $customer->detail->city->name : '',
                    'identity'=> $customer->detail->identity,
                    'couple_nik'=> $customer->detail->couple_nik,
                    'couple_name'=> $customer->detail->couple_name,
                    'couple_birth_place_id' => $customer->detail->couple_birth_place_id,
                    'couple_birth_place' => $customer->detail->couple_birth_place_id ? $customer->detail->couple_birth_place_city->name : '',
                    'couple_birth_date'=> $customer->detail->couple_birth_date,
                    'couple_identity'=> $customer->detail->couple_identity,
                    'job_field_id'=> $customer->detail->job_field_id,
                    'job_field_name'=> $customer->detail->job_field_name,
                    'job_type_id'=> $customer->detail->job_type_id,
                    'job_type_name'=> $customer->detail->job_type_name,
                    'job_id'=> $customer->detail->job_id,
                    'job_name'=> $customer->detail->job_name,
                    'company_name'=> $customer->detail->company_name,
                    'position'=> $customer->detail->position,
                    'position_name'=> $customer->detail->position_name,
                    'work_duration'=> $customer->detail->work_duration,
                    'work_duration_month' => $customer->detail->work_duration_month,
                    'office_address'=> $customer->detail->office_address,
                    'salary'=> $customer->detail->salary,
                    'other_salary'=> $customer->detail->other_salary,
                    'loan_installment'=> $customer->detail->loan_installment,
                    'dependent_amount'=> $customer->detail->dependent_amount,
                    'couple_salary'=> $customer->detail->couple_salary,
                    'couple_other_salary'=> $customer->detail->couple_other_salary,
                    'couple_loan_installment'=> $customer->detail->couple_loan_installment,
                    'emergency_name'=> $customer->detail->emergency_name,
                    'emergency_contact'=> $customer->detail->emergency_contact,
                    'emergency_relation'=> $customer->detail->emergency_relation


                ],
                'kemendagri' => $this->getKemendagri( $request->header( 'Authorization' ), $eform->nik, $request->header( 'pn' ) ),
                'cif' => $this->getCIF( $request->header( 'Authorization' ), $eform->nik, $request->header( 'pn' ) )
            ]
        ], 200 );
    }
    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchNik( Request $request )
    {
        return response()->success( [
            'message' => 'Sukses',
            'contents' => [
                'kemendagri' => $this->getKemendagri( $request->header( 'Authorization' ), $request->input('new_nik'), $request->header( 'pn' ) ),
                'cif' => $this->getCIF( $request->header( 'Authorization' ), $request->input('new_nik'), $request->header( 'pn' ) )
            ]
        ], 200 );
    }

    /**
     * Get Kemendagri Data.
     *
     * @param  string  $authorization
     * @param  string  $nik
     * @param  string  $pn
     * @return \Illuminate\Http\Response
     */
    public function getKemendagri( $authorization, $nik, $pn )
    {
        $kemendagri = RestwsHc::setBody([
                'request' => json_encode([
                    'requestMethod' => 'get_kemendagri_profile_nik',
                    'requestData' => [
                        'nik'     => $nik
                        , 'id_user' => $pn
                    ],
                ])
            ])
            ->setHeaders([
                'Authorization' => $authorization
            ])
            ->post( 'form_params' );

        $keys = [
            'name' => 'namaLengkap'
            , 'gender' => 'jenisKelamin'
            , 'birth_place' => 'tempatLahir'
            , 'birth_date' => 'tanggalLahir'
            , 'phone' => ''
            , 'mobile_phone' => ''
            , 'address' => 'alamat'
            , 'citizenship' => ''
            , 'status' => 'statusKawin'
            , 'address_status' => ''
            , 'mother_name' => 'namaIbu'
        ];

        $return = array();

        foreach ($keys as $key => $field) {
            $return[$key] = $this->mapData(
                $kemendagri['responseData'][0]
                , $field
                , $kemendagri['responseCode']
            );
        }

        return $return;
    }

    /**
     * Get CIF Data.
     *
     * @param  string  $authorization
     * @param  string  $nik
     * @param  string  $pn
     * @return \Illuminate\Http\Response
     */
    public function getCIF( $authorization, $nik, $pn )
    {
        $kemendagri = RestwsHc::setBody([
                'request' => json_encode([
                    'requestMethod' => 'get_customer_profile_nik',
                    'requestData' => [
                        'app_id' => 'mybriapi'
                        , 'nik'     => $nik
                    ],
                ])
            ])
            ->setHeaders([
                'Authorization' => $authorization
            ])
            ->post( 'form_params' );

        $keys = [
            'cif_number' => 'cifno'
            , 'name' => 'nama_sesuai_id'
            , 'gender' => 'jenis_kelamin'
            , 'birth_place' => 'tempat_lahir'
            , 'birth_date' => 'tanggal_lahir'
            , 'phone' => 'telp_rumah'
            , 'mobile_phone' => 'handphone'
            , 'address' => 'alamat_id1'
            , 'citizenship' => 'kewarganegaraan'
            , 'status' => 'status_nikah'
            , 'address_status' => ''
            , 'mother_name' => 'nama_ibu_kandung'
        ];

        $return = array();

        foreach ($keys as $key => $field) {
            $return[$key] = $this->mapData(
                $kemendagri['responseData']['info'][0]
                , $field
                , $kemendagri['responseCode']
            );
        }

        return $return;
    }

    /**
     * Mapping Data.
     *
     * @param  array  $data
     * @param  string  $field
     * @param  string  $responseCode
     * @return \Illuminate\Http\Response
     */
    public function mapData( $data, $field, $responseCode )
    {
        return $responseCode == '00' ? ( isset($data[$field]) ? $data[$field] : '' ) : '';
    }
}
