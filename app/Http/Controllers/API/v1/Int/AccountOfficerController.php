<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountOfficerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $user_login = \RestwsHc::getUser();
        $get_list_AO = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_list_tenaga_pemasar',
                'requestData' => [
                    'id_user' => $request->header( 'pn' ),
                    'kode_branch' => $user_login[ 'branch_id' ]
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => request()->header( 'Authorization' )
        ] )->post( 'form_params' );
        $get_list_AO[ 'responseData' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'PERNR' ],
                'name' => $content[ 'SNAME' ],
                'role_id' => $content[ 'HILFM' ],
                'position' => $content[ 'HTEXT' ]
            ];
        }, $get_list_AO[ 'responseData' ] );
        $account_officers = [
            "current_page" => 1,
            "data" => $get_list_AO[ 'responseData' ],
            "from" => 1,
            "last_page" => 1,
            "next_page_url" => null,
            "path" => "",
            "per_page" => count( $get_list_AO[ 'responseData' ] ),
            "prev_page_url" => null,
            "to" => 1,
            "total" => 1
        ];
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $account_officers
        ], 200 );
    }
}
