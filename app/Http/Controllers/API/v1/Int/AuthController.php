<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\InternalController as Controller;

use App\Http\Requests\API\v1\Int\AuthRequest;
use App\Models\User;
use App\Models\UserServices;

class AuthController extends Controller
{

    public function __construct(User $user, UserServices $userservices)
    {
      $this->user = $user;
      $this->userservices = $userservices;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\Int\AuthRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( AuthRequest $request )
    {
        $pn = substr( '00000000' . $request->pn, -8 );
        $sendRequest = array(
            'id_user' => $pn,
            'password' => $request->password
        );

        if ( $request->has('device_id') ) {
            $sendRequest['device_id'] = $request->device_id;
        }

        $login = \RestwsHc::setBody( [
            'request' => json_encode( [
                'requestMethod' => 'login',
                'requestData' => $sendRequest
            ] )
        ] )->post( 'form_params' );

        $data = $login[ 'responseData' ];

        if( $login[ 'responseCode' ] == '00' ) {
            if( in_array( intval($data[ 'hilfm' ]), [ 37, 38, 39, 41, 42, 43 ] ) ) {
                $role = 'ao';
                $role_user = 'ao';
            } else if( in_array( intval($data[ 'hilfm' ]), [ 21, 49, 50, 51 ] ) ) {
                $role = 'mp';
                $role_user = 'mp';
                if( in_array( intval($data[ 'hilfm' ]), [ 49, 51 ] ) ) {
                    $role_user = 'amp';
                }
            } else if( in_array( intval($data[ 'hilfm' ]), [ 44 ] ) ) {
                $role = 'fo';
                $role_user = 'fo';
            } else if( in_array( intval($data[ 'hilfm' ]), [ 46 ] ) ) {
                $role = 'mantri';
                $role_user = 'mantri';
            } else if( in_array( intval($data[ 'hilfm' ]), [ 5, 11, 12, 14, 19 ] ) ) {
                $role = 'pinca';
                $role_user = 'pinca';
                if( in_array( intval($data[ 'hilfm' ]), [19] ) ) {
                    $role_user = 'pincapem';
                } else if( in_array( intval($data[ 'hilfm' ]), [5] ) ) {
                    $role_user = 'pincasus';
                } else if( in_array( intval($data[ 'hilfm' ]), [11] ) ) {
                    $role_user = 'wapincasus';
                }
            } else if( in_array( intval($data[ 'hilfm' ]), [3] ) ) {
                $role = 'pinwil';
                $role_user = 'pinwil';
            } else if( in_array( intval($data[ 'hilfm' ]), [9] ) ) {
                $role = 'wapinwil';
                $role_user = 'wapinwil';
            } else if( in_array( intval($data[ 'hilfm' ]), [53] ) ) {
                $role = 'spvkanwil';
                $role_user = 'spvkanwil';
            } else if( in_array( intval($data[ 'hilfm' ]), [66, 71, 75] ) ) {
                $role = 'cs';
                $role_user = 'cs';
            } else if( in_array( intval($data[ 'hilfm' ]), [65] ) ) {
                $role = 'teller';
                $role_user = 'teller';
            } else if( in_array( intval($data[ 'hilfm' ]), [54] ) ) {
                $role = 'spvadk';
                $role_user = 'spvadk';
            } else if( in_array( intval($data[ 'hilfm' ]), [ 59 ] ) ) {
                $role = 'prescreening';
                $role_user = 'prescreening';
                if( in_array( strtolower($data[ 'posisi' ]), [ 'collateral appraisal', 'collateral manager' ] ) ){
                    $role = str_replace(' ', '-', strtolower($data[ 'posisi' ]));
                    $role_user = str_replace(' ', '-', strtolower($data[ 'posisi' ]));
                }
            } else if( in_array( intval($data[ 'hilfm' ]), [26] ) ) {
                $role = 'staff';
                $role_user = 'staff';
            } else if( in_array( intval($data[ 'hilfm' ]), [18] ) ) {
                $role = 'collateral';
                $role_user = 'collateral';
            } else if( in_array( intval($data[ 'hilfm' ]), [58, 61] ) ) {
                $role = 'adk';
                $role_user = 'adk';
            } else {
                $role = 'other';
                $role_user = 'staff';
            }

            if (ENV('APP_ENV') == 'local') {
                $arr = [];
            }else {
                $checkedRolePn = $this->userservices->checkroleAndpn($role,$pn);
                if(!$checkedRolePn){
                    $this->userservices->updateOrCreate(['pn'=> $pn],[
                        'pn'=>$pn,
                        'hilfm'=> $data['hilfm'] == '-' ? 0 : $data['hilfm'],
                        'role'=> $role,
                        'name'=> $data['nama'],
                        'tipe_uker'=> $data['tipe_uker'],
                        'htext'=> $data['htext'],
                        'posisi'=> $data['posisi'],
                        'last_activity'=> isset($data['last_activity']) ? $data['last_activity'] : date("Y-m-d h:i:s") ,
                        'mobile_phone'=> 0,
                        'is_actived'=> true,
                        'branch_id'=>$data['branch'],
                        'password'=>md5($request->password)
                    ]);
                }
            }

            if (ENV('APP_ENV') == 'local') {
                $branch = '12';
                $userservices = $this->userservices->where(['pn' => $pn ])->first();
                if(!$userservices){
                    return response()->error( [
                        'message' => 'PN atau Password Salah',
                        'contents'=> 'PN atau Password Salah'
                    ], 422 );

                }else {
                     return response()->success( [
                        'contents'=> [
                            'token' => 'Bearer ' . $userservices[ 'password' ],
                            'pn' => substr( '00000000' . $userservices[ 'pn' ], -8 ),
                            'name' => $userservices[ 'name' ],
                            'branch' => $userservices['branch_id'],
                            'role' => $userservices['role'],
                            'position' => $userservices['posisi'],
                            'uker' => $userservices['tipe_uker']
                        ]
                    ], 200 );

                }
            } else {
                $branch = $data[ 'branch' ];
            }
            $superadmin = ['00054805','00139644','00076898','00079072'];
            if (in_array($pn,$superadmin)) $role = 'superadmin';

            return response()->success( [
                'message' => 'Login Sukses',
                'contents'=> [
                    'token' => 'Bearer ' . $data[ 'token' ],
                    'pn' => $data[ 'pn' ],
                    'name' => $data[ 'nama' ],
                    'branch' => $branch,
                    'role' => $role,
                    'role_user' => $role_user,
                    'position' => $data['posisi'],
                    'uker' => $data['tipe_uker'],
                    'hilfm' => $data['hilfm']
                ]
            ], 200 );

        } else {
            return response()->error( [
                'message' => isset($data) ? $data : 'Gagal Terhubung Dengan Server',
                'contents'=> isset($data) ? $data : 'Gagal Terhubung Dengan Server'
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
        ] )->post( 'form_params' );

        if( $logout_service[ 'responseCode' ] == '00' ) {
            return response()->success( [ 'message' => 'Logout Sukses' ] );
        } else {
            $this->showBRIResponseMessage( $logout_service );
        }
    }
}
