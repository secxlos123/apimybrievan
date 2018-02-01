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

    public function get_token()
    {
      if ( count(apiPdmToken::all()) > 0 ) {
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      } else {
        $this->gen_token();
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      }

      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
        $token = $apiPdmToken['access_token'];
        return $token;
      } else {
        $this->gen_token();
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();

        $token = $apiPdmToken['access_token'];
        return $token;
      }
    }

    public function gen_token()
    {
      $client = new Client();
	   $host = env('APP_URL');
	  if($host == 'http://api.dev.net/'){
		//$url = 'http://172.18.44.182/bribranch/branch/';
		$client_id = '558ecf37e98319c9284e6f5e3afef74c720f20ec';
		$client_secret = '2d6b857f59ffd065acc9cbd9d851d48b61846aac';
		$url = config('restapi.apipdmdev');
	}else{
		$client_id = '3f60d2edcd0399e6ea25290fe4022e0af91e5016';
		$client_secret = 'ef3d569a4a609c636e114ff9056b8c324e0f2e7a';
		$url = config('restapi.apipdm');
	  }
	  
      $requestBriconnect = $client->request('POST', $url.'/oauth/token',
        [
          'form_params' =>
          [
            'grant_type'=> 'client_credentials',
            'client_id'=> $client_id,
            'client_secret'=> $client_secret
          ]
        ]
      );
      $briConnect = json_decode($requestBriconnect->getBody()->getContents(), true);

      $apiPdmToken = new apiPdmToken;

      $apiPdmToken->access_token = $briConnect['access_token'];
      $apiPdmToken->expires_in = date("Y-m-d H:i:s", time() + $briConnect['expires_in']);
      $apiPdmToken->token_type = $briConnect['token_type'];
      $apiPdmToken->scope = $briConnect['scope'];
      $apiPdmToken->clientid = $client_id;
      $apiPdmToken->clientsecret = $client_secret;

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
