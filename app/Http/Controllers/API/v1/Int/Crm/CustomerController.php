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

use App\Models\Crm\apiPdmToken;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use URL;


class CustomerController extends Controller
{
    public function index(Request $request)
    {
      $customersInt = User::getCustomers( $request )->get();
      return response()->success( [
  			'message' => 'Sukses',
  			'contents' => $customersInt
  		], 200 );

    }


    public function customer_nik(Request $request)
    {
      $sendRequest = array(
        'app_id' => 'mybriapi',
        'nik' => $request['nik']
      );
      if ( $request->header('Device-Id') ) {
          $sendRequest['device_id'] = $request->header('Device-Id');
      }
      $customer_nik = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_customer_profile_nik',
          'requestData' => $sendRequest,
        ])
      ])->post('form_params');

      $info = $customer_nik['responseData']['card_info'];
      foreach ($info as $key => $value) {
        $customer_nik['responseData']['card_info'][$key]['nomor_produk'] = substr($value['nomor_produk'], 0, -8).str_repeat('*', 8);
      }

      return response()->success([
        'message' => 'Get Customer Detail by NIK success',
        'contents' => $customer_nik['responseData']
      ]);
    }

    public function detailByCif(Request $request)
    {
      $cif = $request->input('cif');
      $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      // $apiPdmToken = $apiPdmToken[0];

      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
        $token = $apiPdmToken['access_token'];
        $detailByCif = $this->byCif($cif, $token);

        $detail = $detailByCif['data']['info'][0];
        $nik = preg_replace('/\s+/', '', $detail['id_number']);

        $customer_nik = RestwsHc::setBody([
          'request' => json_encode([
            'requestMethod' => 'get_customer_profile_nik',
            'requestData' => [
              'app_id' => 'mybriapi',
              'nik' => $nik
            ],
          ])
        ])->post('form_params');

        return response()->success([
          'message' => 'Get Customer Detail by NIK success',
          'contents' => $customer_nik['responseData']
        ]);
      } else {
        $briConnect = $this->gen_token();
        $apiPdmToken = apiPdmToken::get()->toArray();
        // $apiPdmToken = $apiPdmToken[0];
        $token = $apiPdmToken['access_token'];
        $detailByCif = $this->byCif($cif, $token);

        $detail = $detailByCif['data']['info'][0];
        $nik = preg_replace('/\s+/', '', $detail['id_number']);

        $customer_nik = RestwsHc::setBody([
          'request' => json_encode([
            'requestMethod' => 'get_customer_profile_nik',
            'requestData' => [
              'app_id' => 'mybriapi',
              'nik' => $nik
            ],
          ])
        ])->post('form_params');

        return response()->success([
          'message' => 'Get Customer Detail by NIK success',
          'contents' => $customer_nik['responseData']
        ]);
      }

    }

    public function customer_officer(Request $request)
    {
      $client = new Client();
      $request_customer_officer = $client->request('GET', config('restapi.apipdm').'/customer/officer/'.$request->header('pn'),[
        'headers' =>
        [
          'Authorization' => 'Bearer '.$this->get_token()
          // 'Authorization' => 'Bearer 8288bdbcd66d6ac6dd0cfb21677edab663e2bb83'
        ]
      ]);
      $customer_officer = json_decode($request_customer_officer->getBody()->getContents(), true);

      $result = array_merge_recursive($customer_officer['data']['top'],$customer_officer['data']['bottom']);
      // return $result;die();
      foreach ($result as $key =>  $value) {
        if($value['DELTA_SALDO'] < 0 || $value['DELTA_SALDO'] > 0){
          if ( $value['DELTA_SALDO'] > 0) {
            $customer ['top'][]=[
              'POSISI_LAST_MONTH' => $value['POSISI_LAST_MONTH'],
              'POSISI_1' => $value['POSISI_1'],
              'NASABAH'=>$value['NASABAH'],
              'NOREK' => $value['NOREK'],
              'SALDO_AKHIR_BULAN'=> $value['SALDO_AKHIR_BULAN'],
              'H-2'=> $value['H-2'],
              'H-1'=> $value['H-1'],
              'DELTA_SALDO'=> $value['DELTA_SALDO'],
              'PERSONAL_NUMBER'=> $value['PERSONAL_NUMBER'],
            ];
          } else {
            $customer ['bottom'][]=[
              'POSISI_LAST_MONTH' => $value['POSISI_LAST_MONTH'],
              'POSISI_1' => $value['POSISI_1'],
              'NASABAH'=>$value['NASABAH'],
              'NOREK' => $value['NOREK'],
              'SALDO_AKHIR_BULAN'=> $value['SALDO_AKHIR_BULAN'],
              'H-2'=> $value['H-2'],
              'H-1'=> $value['H-1'],
              'DELTA_SALDO'=> $value['DELTA_SALDO'],
              'PERSONAL_NUMBER'=> $value['PERSONAL_NUMBER'],
            ];
          }
        }
      }
      $data=[];

      if(array_key_exists('top', $customer) && $customer['top'] != null){
        usort($customer['top'], function($a, $b) { //Sort the array using a user defined function
            return $a['DELTA_SALDO'] >= $b['DELTA_SALDO'] ? -1 : 1; //Compare the scores
        });
        $data ['top']=array_slice($customer['top'],0,10,false);
      }else{
        $data['top']=[];
      }

      if(array_key_exists('bottom', $customer) && $customer['bottom'] != null){
        usort($customer['bottom'], function($a, $b) { //Sort the array using a user defined function
            return $a['DELTA_SALDO'] <= $b['DELTA_SALDO'] ? -1 : 1; //Compare the scores
        });
        $data ['bottom']=array_slice($customer['bottom'],0,10,false);
      }else{
        $data['bottom']=[];
      }


      return response()->success( [
          'message' => 'Get Customer by Officer success',
          'contents' => $data
        ]);
    }
}
