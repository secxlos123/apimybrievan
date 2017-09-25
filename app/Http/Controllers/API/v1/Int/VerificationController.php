<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\EForm;
use RestwsHc;

class VerificationController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\API\v1\CustomerRequest  $request
     * @param  int  $eform_id
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, $eform_id )
    {
        $eform = EForm::findOrFail( $eform_id );
        $customer = $eform->customer;
        $kemendagri = RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_kemendagri_profile_nik',
                'requestData' => [
                    'nik'     => $request->nik,
                    'id_user' => $request->header( 'pn' )
                ],
            ])
        ] )->setHeaders( [
            'Authorization' => $request->header( 'Authorization' )
        ] )->post( 'form_params' );
        $cif = RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_customer_profile_nik',
                'requestData'   => [ 'nik' => $request->nik ],
            ] )
        ] )->post('form_params');

        return response()->success( [
            'message' => 'Sukses',
            'contents' => [
                'customer' => [
                    'name' => $customer->full_name,
                    'gender' => $customer->gender,
                    'birth_place' => $customer->detail->birth_place,
                    'birth_date' => $customer->detail->birth_date,
                    'phone' => $customer->phone,
                    'mobile_phone' => $customer->mobile_phone,
                    'address' => $customer->detail->address,
                    'citizenship' => $customer->detail->citizenship,
                    'status' => $customer->detail->status,
                    'address_status' => $customer->detail->address_status,
                    'mother_name' => $customer->detail->mother_name
                ],
                'kemendagri' => $kemendagri[ 'responseData' ],
                'cif' => [
                    'cif_number' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'cifno' ],
                    'name' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'nama_sesuai_id' ],
                    'gender' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'jenis_kelamin' ],
                    'birth_place' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'tempat_lahir' ],
                    'birth_date' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'tanggal_lahir' ],
                    'phone' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'telp_rumah' ],
                    'mobile_phone' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'handphone' ],
                    'address' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'alamat_id1' ],
                    'citizenship' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'kewarganegaraan' ],
                    'status' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'status_nikah' ],
                    'address_status' => '',
                    'mother_name' => $cif[ 'responseData' ][ 'info' ][ 0 ][ 'nama_ibu_kandung' ],
                ]
            ]
        ], 200 );
    }
}
