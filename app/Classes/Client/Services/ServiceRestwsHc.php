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
        return config('restapi.restwshc');
    }

    /**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public function getUser()
    {
        $get_user_info_service = $this->setBody( [
            'request' => json_encode( [
                'requestMethod' => 'get_user_info',
                'requestData' => [
                    'id_cari' => request()->header( 'pn' ),
                    'id_user' => request()->header( 'pn' )
                ]
            ] )
        ] )->setHeaders( [
            'Authorization' => request()->header( 'Authorization' )
        ] )->post( 'form_params' );
        if( ! empty( $get_user_info_service ) ) {
            if( $get_user_info_service[ 'responseCode' ] == '00' ) {
                return [
                    'name' => $get_user_info_service[ 'responseData' ][ 'SNAME' ],
                    'nip' => $get_user_info_service[ 'responseData' ][ 'NIP' ],
                    'role_id' => $get_user_info_service[ 'responseData' ][ 'HILFM' ],
                    'branch_id' => $get_user_info_service[ 'responseData' ][ 'BRANCH' ],
                    'phone' => $get_user_info_service[ 'responseData' ][ 'HP1' ]
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