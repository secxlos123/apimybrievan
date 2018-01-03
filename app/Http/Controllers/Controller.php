<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\User;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

use App\Models\Crm\apiPdmToken;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $_user;

    public function __construct(User $user, Request $request)
    {
        if ( $request->header('_token') ) {
            $user = Sentinel::findByPersistenceCode($request->header('_token'));

            if ( $user ) {
                $user_id = $user->id;

                $userModel = User::findOrFail($user_id);

                $this->_user = $userModel;
            }
        }
    }

    public function gen_token()
    {
      $client = new Client();
      $requestBriconnect = $client->request('POST', config('restapi.apipdm').'/oauth/token',
        [
          'form_params' =>
          [
            'grant_type'=> 'client_credentials',
            'client_id'=> '3f60d2edcd0399e6ea25290fe4022e0af91e5016',
            'client_secret'=> 'ef3d569a4a609c636e114ff9056b8c324e0f2e7a'
          ]
        ]
      );
      $briConnect = json_decode($requestBriconnect->getBody()->getContents(), true);

      $apiPdmToken = new apiPdmToken;

      $apiPdmToken->access_token = $briConnect['access_token'];
      $apiPdmToken->expires_in = date("Y-m-d H:i:s", time() + $briConnect['expires_in']);
      $apiPdmToken->token_type = $briConnect['token_type'];
      $apiPdmToken->scope = $briConnect['scope'];
      $apiPdmToken->clientid = '3f60d2edcd0399e6ea25290fe4022e0af91e5016';
      $apiPdmToken->clientsecret = 'ef3d569a4a609c636e114ff9056b8c324e0f2e7a';

      $apiPdmToken->save();

      return $briConnect;
    }

    public function byCif($cif, $token)
    {
      $client = new Client();
      $requestLeadsDetail = $client->request('GET', config('restapi.apipdm').'/customer/details/'.$cif,
        [
          'headers' =>
          [
            'Authorization' => 'Bearer '.$token
          ]
        ]
      );
      $leadsDetail = json_decode($requestLeadsDetail->getBody()->getContents(), true);

      return $leadsDetail;
    }
}
