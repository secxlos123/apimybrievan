<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\Crm\Marketing\CreateRequest;
// use App\Http\Request\API\v1\Crm\Marketing\UpdateRequest;
use App\Models\Crm\Marketing;
use App\Models\Crm\ActivityType;
use App\Models\Crm\ProductType;
use App\Models\Crm\Status;
use App\Models\User;

use RestwsHc;

// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;

use App\Classes\Client\Client;


class CustomerController extends Controller
{
    public function index(Request $request)
    {
      $customersInt = User::getCustomers( $request )->get();
      // $customerData = Client::setEndpoint('customer')
      //               ->setQuery(['limit' => 100])
      //               ->setHeaders([
      //                 "Authorization" => request()->header('Authorization'),
      //                 "pn" => request()->header('branch')
      //               ])->get();
      // $customersEks = $customerData['contents']['data'];
      // dd($customersEks);
      return response()->success( [
  			'message' => 'Sukses',
  			'contents' => $customersInt
  		], 200 );

    }

    // public function test(Request $request)
    // {
    //   $customerData = Client::setEndpoint('customer')
    //                 ->setHeaders([
    //                   "Authorization" => request()->header('Authorization'),
    //                   "pn" => request()->header('branch')
    //                 ])->get();
    //   $dataCustomer = $customerData['contents']['data'];
    //
    //   return response()->success( [
  	// 		'message' => 'Sukses',
  	// 		'contents' => $dataCustomer
  	// 	], 200 );
    // }

    public function customer_nik(Request $request)
    {
      $customer_nik = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_customer_profile_nik',
          'requestData' => [
            'app_id' => 'mybriapi',
            'nik' => $request['nik']
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      return response()->success([
        'message' => 'Get Customer Detail by NIK success',
        'contents' => $customer_nik['responseData']
      ]);
    }

    public function customer_officer(Request $request)
    {
      // http://api.briconnect.bri.co.id/customer/officer/00025222
      $token =
      $request_customer_officer = Client()->request('GET', config('restapi.apipdm').'/customer/officer/'.$request->header('pn'),[
        'headers' =>
        [
          'Authorization' => 'Bearer '.$this->get_token()
        ]
      ]);

      $customer_officer = json_encode($request_customer_officer->getBody()->getContents(), true);

      return $customer_officer;
    }

    public function get_token()
    {
      $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      // $apiPdmToken = $apiPdmToken[0];

      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {

        return $apiPdmToken['access_token'];

      } else {
        $briConnect = $this->gen_token();
        $apiPdmToken = apiPdmToken::get()->toArray();

        return $apiPdmToken['access_token'];
      }
    }
}
