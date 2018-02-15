<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use RestwsHc;

class KanwilController extends Controller
{
    public function index(Request $request)
    {
    	$get_kanwil = RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_list_kanwil',
                'requestData' => [
                   'app_id' => 'mybriapi'
                ],
            ])
        ])
        ->post( 'form_params' );

      if ( ENV('APP_ENV') == 'local' ) {
        $kanwil_list = [
            "current_page" => 1,
            "data" => array(
                array(
                  'region_id' => 'Q',
                  'region_name' => 'Jakarta 3',
                  'branch_id' => '12'
                )
              ),
            "from" => 1,
            "last_page" => 1,
            "next_page_url" => null,
            "path" => "",
            "per_page" => 1,
            "prev_page_url" => null,
            "to" => 1,
            "total" => 1
        ];
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $kanwil_list
        ], 200 );
      } else {
        if ($get_kanwil['responseCode'] == '00' ) {

           $get_kanwil[ 'responseData' ] = array_map( function( $content ) {
            return [
                'region_id' => $content[ 'region' ],
                'region_name' => $content[ 'rgdesc' ],
                'branch_id' => $content[ 'branch' ]
            ];
        }, $get_kanwil[ 'responseData' ] );
          $kanwil_list = [
              "current_page" => 1,
              "data" => $get_kanwil[ 'responseData' ],
              "from" => 1,
              "last_page" => 1,
              "next_page_url" => null,
              "path" => "",
              "per_page" => count( $get_kanwil[ 'responseData' ] ),
              "prev_page_url" => null,
              "to" => 1,
              "total" => 1
          ];
          return response()->success( [
              'message' => 'Sukses',
              'contents' => $kanwil_list
          ], 200 );

        }
      }

     	return response()->error( [
          'message' => $get_kanwil[ 'responseDesc' ]
      	], 422 );

    }
}
