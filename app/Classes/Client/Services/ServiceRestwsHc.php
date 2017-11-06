<?php

namespace App\Classes\Client\Services;

use App\Classes\Client\Client;

class ServiceRestwsHc extends Client
{
	/**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public function uri()
    {
        $base_url = config('restapi.restwshc');

        if (in_array(env('APP_ENV'), ['local', 'staging'])) {
            $this->endpoint = json_decode($this->body['request'])->requestMethod;
            $base_url .= $this->endpoint;
        }

        return $base_url;
    }

    /**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public static function getUser( $pn = null )
    {
        $get_user_info_service = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_user_info',
                'requestData' => [
                    'id_cari' => empty( $pn ) ? request()->header( 'pn' ) : $pn,
                    'id_user' => request()->header( 'pn' )
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => request()->header( 'Authorization' )
        ] )->post( 'form_params' );
        if( ! empty( $get_user_info_service ) ) {
            if( $get_user_info_service[ 'responseCode' ] == '00' ) {

                if( in_array( $get_user_info_service[ 'responseData' ][ 'HILFM' ], [ 37, 38, 39, 41, 42, 43 ] ) ) {
                    $role = 'ao';
                } else if( in_array( $get_user_info_service[ 'responseData' ][ 'HILFM' ], [ 21, 49, 50, 51 ] ) ) {
                    $role = 'mp';
                } else if( in_array( $get_user_info_service[ 'responseData' ][ 'HILFM' ], [ 5, 11, 12, 14, 19 ] ) ) {
                    $role = 'pinca';
                } else {
                    $role = 'none';
                }
                
                return [
                    'name' => $get_user_info_service[ 'responseData' ][ 'SNAME' ],
                    'nip' => $get_user_info_service[ 'responseData' ][ 'NIP' ],
                    'role_id' => $get_user_info_service[ 'responseData' ][ 'HILFM' ],
                    'role' => $role,
                    'branch_id' => $get_user_info_service[ 'responseData' ][ 'BRANCH' ],
                    'pn' => $get_user_info_service[ 'responseData' ][ 'PERNR' ],
                    // 'phone' => $get_user_info_service[ 'responseData' ][ 'HP1' ]
                ];
            }
        }
        return false;
    }

    /**
     * Post request to middleware.
     *
     * @return \Illuminate\Http\Response
     */
    // public function post($type = 'json')
    // {
    // 	$this->body = $this->requests($this->body);

    //     return parent::post($type);
    // }

    /**
     * Formating request to server.
     *
     * @param 	array $data
     * @return 	array
     */
   //  public function requests(array $data)
   //  {
   //  	return [
   //  		'request' => json_encode([
			// 	'requestMethod' => $data['endpoint'],
			// 	'requestData' 	=> array_except($data, ['endpoint'])
			// ])
   //  	];
   //  }
}