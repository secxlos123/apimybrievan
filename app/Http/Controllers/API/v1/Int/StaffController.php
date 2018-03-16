<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use RestwsHc;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        if ( ENV('APP_ENV') == 'local' ) {
            return response()->success(
                [
                    'message' => 'Sukses',
                    'contents' => {
                        "current_page": 1,
                        "data": [
                            {
                                "id": "00137746",
                                "name": "Achmad Irfan Ramadhan"
                            },
                            {
                                "id": "00137746",
                                "name": "Dedi Idrus"
                            },
                            {
                                "id": "00137746",
                                "name": "Rosiva Corry Gustina Sitorus"
                            },
                            {
                                "id": "00137746",
                                "name": "Ryanto Imam Wibowo"
                            },
                            {
                                "id": "00137746",
                                "name": "Gustiawan Aswinardian"
                            }
                        ],
                        "from": 1,
                        "last_page": 1,
                        "next_page_url": null,
                        "path": "",
                        "per_page": 5,
                        "prev_page_url": null,
                        "to": 1,
                        "total": 1
                    }
                ]
                , 200
            );

        } else {
            $get_staff = RestwsHc::setBody([
                    'request' => json_encode([
                        'requestMethod' => 'get_pekerja_collateral_from_kanwil',
                        'requestData' => [
                           'id_user' => $request->header( 'pn' ),
                           'region'=> $request->input( 'region_id' )
                        ],
                    ])
                ])->post( 'form_params' );

            if ($get_staff['responseCode'] == '00' ) {
                $get_staff[ 'responseData' ] = array_map( function( $content ) {
                    return [
                        'id' => $content[ 'PN' ],
                        'name' => $content[ 'Nama' ]
                    ];
                }, $get_staff[ 'responseData' ] );

                $staff_list = [
                    "current_page" => 1,
                    "data" => $get_staff[ 'responseData' ],
                    "from" => 1,
                    "last_page" => 1,
                    "next_page_url" => null,
                    "path" => "",
                    "per_page" => count( $get_staff[ 'responseData' ] ),
                    "prev_page_url" => null,
                    "to" => 1,
                    "total" => 1
                ];

                return response()->success( [
                    'message' => 'Sukses',
                    'contents' => $staff_list
                ], 200 );
            }
        }

       	return response()->error( [
                'message' => $get_staff[ 'responseDesc' ]
        	], 422 );

    }

}
