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
use App\Models\Crm\apiPdmToken;
use App\Models\User;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class AccountController extends Controller
{
    public function index(Request $request)
    {
      $client = new Client();
      $requestLeads = $client->request('POST', 'http://10.35.65.111/skpp_concept/restws_hc',
        [
          'headers' =>
          [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => request()->header( 'Authorization' )
          ],
          'form_params' =>
            [
                'request' => json_encode(
                    [
                      "requestMethod" => "get_customer_leads",
                      "requestData"=> [
                        "id_user" => request()->header( 'pn' ),
                        "kode_branch" => request()->header( 'branch' ), // 5 digit uker
                        "type_request" => "list",//$request->input('type_request'), // list or search
                        "type_usulan" => $request->input('type_usulan'), // kpr or kkb
                        "limit" => "10000",//$request->input('limit'),
                        "page" => "1",//$request->input('page'),
                        "order_by" => "nama",//$request->input('order_by'), // nama or amount
                        "search_value" => "0",//$request->input('search_value')
                      ],
                    ]
                  )
            ]
        ]
      );
      $leads = json_decode($requestLeads->getBody()->getContents(), true);
      // dd($leads);
      if ($leads['responseCode'] == 00) {
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $leads['responseData']
          ]);
      } else {
        return response()->success( [
            'message' => 'Gagal',
            'contents' => $leads['responseDesc']
          ]);
      }

    }

    public function detail(Request $request)
    {
      $cif = $request->input('cif');
      $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      // $apiPdmToken = $apiPdmToken[0];

      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
        $token = $apiPdmToken['access_token'];
        $detailByCif = $this->byCif($cif, $token);

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $detailByCif['data']['info'][0]
        ]);
      } else {
        $briConnect = $this->gen_token();
        $apiPdmToken = apiPdmToken::get()->toArray();
        // $apiPdmToken = $apiPdmToken[0];
        $token = $apiPdmToken['access_token'];
        $detailByCif = $this->byCif($cif, $token);

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $detailByCif['data']['info'][0]
        ]);
      }
    }

    public function existingFo(Request $request)
    {
      $data['branch'] = $request->header('branch');
      $data['pn'] = $request->header('pn');

      // if ( count(apiPdmToken::all()) > 0 ) {
      //   $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      // } else {
      //   $this->gen_token();
      //   $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      // }
      //
      // if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
      //   $token = $apiPdmToken['access_token'];
      //   $listExisting = $this->getExistingByFo($data, $token);
      //
      //   return response()->success( [
      //       'message' => 'Sukses',
      //       'contents' => $listExisting['data']
      //   ]);
      // } else {
      //   $briConnect = $this->gen_token();
      //   $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      //
      //   $token = $apiPdmToken['access_token'];
      //   $listExisting = $this->getExistingByFo($data, $token);
      //
      //   return response()->success( [
      //       'message' => 'Sukses',
      //       'contents' => $listExisting['data']
      //   ]);
      // }
      $client = new Client();
      $requestListExisting = $client->request('GET', 'http://172.18.44.182/customer/saving/'.$data['branch'].'/'.$data['pn'],
        [
          'headers' =>
          [
            'Authorization' => 'Bearer '.$this->get_token()
          ]
        ]
      );
      $listExisting = json_decode($requestListExisting->getBody()->getContents(), true);

      return response()->success( [
          'message' => 'Get Customer by Officer success',
          'contents' => $listExisting["data"]
        ]);

    }

    public function getExistingByFo($data, $token)
    {
      $client = new Client();
      $requestListExisting = $client->request('GET', 'http://172.18.44.182/customer/saving/'.$data['branch'].'/'.$data['pn'],
        [
          'headers' =>
          [
            'Authorization' => 'Bearer '.$token
          ]
        ]
      );
      $listExisting = json_decode($requestListExisting->getBody()->getContents(), true);

      return $listExisting;
    }

    public function getBranchKanwil(Request $request)
    {
      $client = new Client();
      $requestLeads = $client->request('POST', 'http://10.35.65.111/skpp_concept/restws_hc',
        [
          'headers' =>
          [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => request()->header( 'Authorization' )
          ],
          'form_params' =>
            [
                'request' => json_encode(
                    [
                      "requestMethod" => "get_branch_kanwil",
                      "requestData"=> [
                        "id_user" => request()->header( 'pn' ),
                        "kode_branch" => request()->header( 'branch' )
                      ],
                    ]
                  )
            ]
        ]
      );
      $leads = json_decode($requestLeads->getBody()->getContents(), true);

      return $leads;
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
      }
    }
}
