<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\InternalController as Controller;

use App\Http\Requests\API\v1\Int\AuthRequest;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\Int\AuthRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( AuthRequest $request )
    {
        $pn = substr( '00000000' . $request->pn, -8 );
        $login = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'login',
                'requestData' => [
                    'id_user' => $pn,
                    'password' => $request->password
                ]
            ] )
        ] )->post( 'form_params' );
        $data = $login[ 'responseData' ];

        \Log::info($data);
        if( $login[ 'responseCode' ] == '00' ) {

            if( in_array( intval($data[ 'hilfm' ]), [ 37, 38, 39, 41, 42, 43 ] ) ) {
                $role = 'ao';
            } else if( in_array( intval($data[ 'hilfm' ]), [ 21, 49, 50, 51 ] ) ) {
                $role = 'mp';
            } else if( in_array( intval($data[ 'hilfm' ]), [ 44 ] ) ) {
                $role = 'fo';
            } else if( in_array( intval($data[ 'hilfm' ]), [ 5, 11, 12, 14, 19 ] ) ) {
                $role = 'pinca';
            } else if( in_array( intval($data[ 'hilfm' ]), [ 59 ] ) ) {
                $role = 'prescreening';
                if( in_array( strtolower($data[ 'posisi' ]), [ 'collateral appraisal', 'collateral manager' ] ) ){
                    $role = str_replace(' ', '-', strtolower($data[ 'posisi' ]));
                }
            } else if( in_array( intval($data[ 'hilfm' ]), [26] ) ) {
                $role = 'staff';
            } else if( in_array( intval($data[ 'hilfm' ]), [18] ) ) {
                $role = 'collateral';
            } else {
                // $request->headers->set( 'pn', $pn );
                // $this->destroy( $request );
                // return response()->success( [
                //     'message' => 'Unauthorized',
                //     'contents'=> []
                // ], 401 );
                // Ini Buat Handle Semua User Bisa Masuk Role Staff
                $role = 'staff';
            }

            if (ENV('APP_ENV') == 'local') {
                $branch = '12';
            } else {
                $branch = $data[ 'branch' ];
            }

            \Log::info($role);
            return response()->success( [
                'message' => 'Login Sukses',
                'contents'=> [
                    'token' => 'Bearer ' . $data[ 'token' ],
                    'pn' => $data[ 'pn' ],
                    'name' => $data[ 'nama' ],
                    'branch' => $branch,
                    'role' => $role,
                    'position' => $data['posisi'],
                    'uker' => $data['tipe_uker']
                ]
            ], 200 );

        } else {
            return response()->error( [
                'message' => isset($data) ? $data : 'Gagal Terhubung Dengan Server',
                'contents'=> []
            ], 422 );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request )
    {
        $logout_service = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'logout',
                'requestData' => [
                    'id_user' => $request->header( 'pn' ),
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => $request->header( 'Authorization' )
        ] )->post( 'form_params' );

        if( $logout_service[ 'responseCode' ] == '00' ) {
            return response()->success( [ 'message' => 'Logout Sukses' ] );
        } else {
            $this->showBRIResponseMessage( $logout_service );
        }
    }
}
