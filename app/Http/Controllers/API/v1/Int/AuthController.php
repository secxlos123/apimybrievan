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
        if( $login[ 'responseCode' ] == '00' ) {
            return response()->success( [
                'message' => 'Login Sukses',
                'contents'=> [
                    'token' => 'Bearer ' . $data[ 'token' ],
                    'pn' => $data[ 'pn' ],
                    'name' => $data[ 'nama' ]
                ]
            ], 200 );
        } else {
            return response()->success( [
                'message' => $login[ 'responseData' ],
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
