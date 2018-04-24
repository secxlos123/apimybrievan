<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use RestwsHc;

class branchController extends Controller
{
	public function list_kanwil(Request $request)
	{
			if ( $request->has('device_id') ) {
				$sendRequest['device_id'] = $request->device_id;
			}
			$sendRequest['app_id'] = 'mybriapi';
    	$list_kanwil = RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_list_kanwil',
                'requestData' => $sendRequest,
            ])
        ])
        ->post( 'form_params' );

      if ($list_kanwil['responseCode'] == '00' ) {

         $list_kanwil[ 'responseData' ] = array_map( function( $content ) {
          return [
              'region_id' => $content[ 'region' ],
              'region_name' => $content[ 'rgdesc' ],
              'branch_id' => $content[ 'branch' ]
          ];
      }, $list_kanwil[ 'responseData' ] );
        $kanwil_list = [
            "current_page" => 1,
            "data" => $list_kanwil[ 'responseData' ],
            "from" => 1,
            "last_page" => 1,
            "next_page_url" => null,
            "path" => "",
            "per_page" => count( $list_kanwil[ 'responseData' ] ),
            "prev_page_url" => null,
            "to" => 1,
            "total" => 1
        ];
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $kanwil_list
        ], 200 );

      }

     	return response()->error( [
          'message' => $list_kanwil[ 'responseDesc' ]
      	], 422 );

	}

	public function get_kanca_kanwil(Request $request)
	{
			$requestPost =[
				'app_id' => 'mybriapi',
				'region' => $request['region']
			];

			if ( $request->has('device_id') ) {
				$requestPost['device_id'] = $request->device_id;
			}

			$list_kanca_kanwil = RestwsHc::setBody([
						'request' => json_encode([
								'requestMethod' => 'get_list_kanca_from_kanwil',
								'requestData' => $requestPost
						])
				])
				->post( 'form_params' );

			return response()->success( [
					'message' => 'Sukses',
					'contents' => $list_kanca_kanwil
			], 200 );
	}

	public function get_uker_kanca(Request $request)
	{
			$requestPost =[
				'app_id' => 'mybriapi',
				'branch_code' => $request['branch_code']
			];

			if ( $request->has('device_id') ) {
				$requestPost['device_id'] = $request->device_id;
			}

			$list_uker_kanca = RestwsHc::setBody([
						'request' => json_encode([
								'requestMethod' => 'get_list_uker_from_cabang',
								'requestData' => $requestPost
						])
				])
				->post( 'form_params' );

			return response()->success( [
					'message' => 'Sukses',
					'contents' => $list_uker_kanca
			], 200 );
	}
}
