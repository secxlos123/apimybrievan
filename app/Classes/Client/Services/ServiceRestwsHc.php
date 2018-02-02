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
        ] )->post( 'form_params' );

        if( ! empty( $get_user_info_service ) ) {
            if( $get_user_info_service[ 'responseCode' ] == '00' ) {

                if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 37, 38, 39, 41, 42, 43 ] ) ) {
                    $role = 'ao';
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 21, 49, 50, 51 ] ) ) {
                    $role = 'mp';
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 44 ] ) ) {
                    $role = 'fo';
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 5, 11, 12, 14, 19 ] ) ) {
                    $role = 'pinca';
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 59 ] ) ) {
                    $role = 'prescreening';
                    if( in_array( strtolower($get_user_info_service[ 'responseData' ][ 'ORGEH_TX' ]), [ 'collateral appraisal', 'collateral manager' ] ) ){
                        $role = str_replace(' ', '-', strtolower($get_user_info_service[ 'responseData' ][ 'ORGEH_TX' ]));
                    }
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 5 ] ) ) {
                    $role = 'pincasus';
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 11 ] ) ) {
                    $role = 'wapincasus';
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 3 ] ) ) {
                    $role = 'pinwil';
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 9 ] ) ) {
                    $role = 'wapinwil';
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [ 66, 71, 75 ] ) ) {
                    $role = 'cs';
                // hilfm adk tambah filter posisi
                } else if( in_array( intval($get_user_info_service[ 'responseData' ][ 'HILFM' ]), [58, 61] ) ) {
                    $adk = explode(' ', $get_user_info_service[ 'responseData' ][ 'ORGEH_TX' ]);
                    // print_r($adk);
                    // print_r($data);exit();
                    if ( in_array( strtolower($adk[1]), [ 'adm.kredit' ] ) ) {
                        $role = 'adk';
                    }
                } else {
                    $role = 'staff';
                }

                if (ENV('APP_ENV') == 'local') {
                    $branch = '12';
                } else {
                    $branch = $get_user_info_service[ 'responseData' ][ 'BRANCH' ];
                }

                return [
                    'name' => $get_user_info_service[ 'responseData' ][ 'SNAME' ],
                    'nip' => $get_user_info_service[ 'responseData' ][ 'NIP' ],
                    'role_id' => $get_user_info_service[ 'responseData' ][ 'HILFM' ],
                    'role' => $role,
                    'branch_id' => $branch,
                    'pn' => $get_user_info_service[ 'responseData' ][ 'PERNR' ],
                    'position' => $get_user_info_service[ 'responseData' ][ 'ORGEH_TX' ],
                    'department' => $get_user_info_service[ 'responseData' ][ 'STELL_TX' ]
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
