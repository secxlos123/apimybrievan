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
        if (ENV('APP_ENV') == 'local') {
            $branch = '12';
            $userservices = $this->userservices->where(['pn' => $request->pn, 'password' => md5($request->password) ])->first();
            if(!$userservices){
                return response()->error( [
                    'message' => 'Gagal Terhubung Dengan Server',
                    'contents'=> []
                ], 422 );

            }else {
                $role = checkRolesInternal($userservices[ 'hilfm' ] ,$userservices[ 'posisi' ]);
                $a = $this->CheckroleAndpn( $userservices, $role['role'], (integer)$userservices['pn'], $request );
             
                return response()->success( [
                    'message' => 'Login Sukses',
                    'contents'=> [
                        'token' => 'Bearer ' . $userservices[ 'password' ],
                        'pn' => $userservices[ 'pn' ],
                        'name' => $userservices[ 'name' ],
                        'branch' => $userservices['branch_id'],
                        'role' => $userservices['role'],
                        'position' => $userservices['posisi'],
                        'uker' => $userservices['tipe_uker']
                    ]
                ], 200 );
             }
        } else {
        
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

                $role = checkRolesInternal($data[ 'hilfm' ] ,$data[ 'posisi' ]);            
                $this->CheckroleAndpn( $data, $role, substr($data['pn'],3), $request );

                return response()->success( [
                    'message' => 'Login Sukses',
                    'contents'=> [
                        'token' => 'Bearer ' . $data[ 'token' ],
                        'pn' => $data[ 'pn' ],
                        'name' => $data[ 'nama' ],
                        'branch' => $branch,
                        'role' => $role['role'],
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


    }

    public function CheckroleAndpn($data, $role, $pn ,$request){

        $checkedRolePn = $this->userservices->where('role',$role)->where('pn',$pn)->first();
        if(!$checkedRolePn){
            return false;
        }else {
            $this->userservices->updateOrCreate(['pn'=>$request->pn],[
                'pn'=>$request->pn,
                'hilfm'=>$data['hilfm'],
                'role'=> $role['role'],
                'name'=> $data['nama'],
                'tipe_uker'=> $data['tipe_uker'],
                'htext'=> $data['htext'],
                'posisi'=> $data['posisi'],
                'last_activity'=> isset($data['last_activity']) ? $data['last_activity'] : date("Y-m-d h:i:s") ,
                'mobile_phone'=> 0,
                'is_actived'=> true,
                'branch_id'=>$data['branch'],
                'password' => md5($request->password)
            ]);
            return true;        }
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
