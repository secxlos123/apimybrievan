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
use App\Models\ApiPdmTokensBriguna;

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
      $url = env('APP_URL');
      // Keperluan TOT
	
	    $host = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.apipdm'):config('restapi.apipdmdev');
		$client_id = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.pdm_client_id'):config('restapi.pdm_client_id_dev');
		$client_secret = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.pdm_client_secret'):config('restapi.pdm_client_secret_dev');

      $requestBriconnect = $client->request('POST', $host.'/oauth/token',
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
    public function gen_token_briguna()
    {
      $client = new Client();
	  $host = env('APP_URL');
	  
        \Log::info($host);
		
	    $host = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.apipdm'):'http://10.35.65.208:81/';
		$client_id = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.pdm_client_id'):config('restapi.pdm_client_id_dev');
		$client_secret = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.pdm_client_secret'):config('restapi.pdm_client_secret_dev');
	
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

      $apiPdmToken = new ApiPdmTokensBriguna;

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
      $host = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.apipdm'):config('restapi.apipdmdev');
      $requestLeadsDetail = $client->request('GET', $host.'/customer/details/'.$cif,
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
