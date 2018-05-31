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
use App\Models\Crm\Referral;
use RestwsHc;
use App\Models\User;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class AccountController extends Controller
{
    public function index(Request $request)
    {
      $sendRequest = array(
        "id_user" => request()->header( 'pn' ),
        "kode_branch" => request()->header( 'branch' ), // 5 digit uker
        "type_request" => "list",//$request->input('type_request'), // list or search
        "type_usulan" => $request->input('type_usulan'), // kpr or kkb
        "limit" => "10000",//$request->input('limit'),
        "page" => "1",//$request->input('page'),
        "order_by" => "nama",//$request->input('order_by'), // nama or amount
        "search_value" => "0",//$request->input('search_value')
      );

      if ( $request->has('device_id') ) {
          $sendRequest['device_id'] = $request->device_id;
      }

      $leads = \RestwsHc::setBody( [
          'request' => json_encode( [
              'requestMethod' => 'get_customer_leads',
              'requestData' => $sendRequest
          ] )
      ] )->post( 'form_params' );
      // dd($leads);
      if ($leads['responseCode'] == 00) {
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $leads['responseData']
          ]);
      } else {
        return response()->success( [
            'message' => 'Gagal',
            'contents' => []
          ]);
      }

    }

    public function detail(Request $request)
    {
      $cif = $request->input('cif');
      if ($cif == '') {
        return response()->success( [
            'message' => 'Error! Cif tidak boleh kosong',
            'contents' => []
        ]);
      }
      if (apiPdmToken::count() > 0) {
        $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
      } else {
        $briConnect = $this->gen_token();
        $apiPdmToken = apiPdmToken::get()->toArray();
      }

      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
        $token = $apiPdmToken['access_token'];
        $detailByCif = $this->byCif($cif, $token);
        if ($detailByCif['success'] == true) {
          return response()->success( [
              'message' => 'Sukses',
              'contents' => $detailByCif['data']['info'][0]
          ]);
        } else {
          return response()->success( [
              'message' => 'CIF tidak di temukan',
              'contents' => []
          ]);
        }
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

      try {
          $client = new Client();
          $host = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.apipdm'):config('restapi.apipdmdev');
          $requestListExisting = $client->request('GET', $host.'/customer/saving/'.$data['branch'].'/'.$data['pn'],
          [
            'headers' =>
            [
              'Authorization' => 'Bearer '.$this->get_token()
              // 'Authorization' => 'Bearer '.'0874cf43c96a04a3b931927d036b5cf200a63454'
            ]
          ]
        );
        $listExisting = json_decode($requestListExisting->getBody()->getContents(), true);

        if ($listExisting['code'] == 200) {
          return response()->success( [
            'message' => 'Sukses',
            'contents' => $listExisting['data']
          ]);
        } else {
          return response()->success( [
            'message' => $listExisting['message'],
            'contents' => $listExisting['message']
          ]);
        }
      } catch (\Exception $e) {
        $error = 'Gagal Koneksi Jaringan';
        return [
            'message' => 'Gagal Koneksi Jaringan',
            'contents' => $error
        ];
      }

    }

    public function getExistingByFo($data, $token)
    {
      $host = (env('APP_URL') == 'http://apimybri.bri.co.id')? config('restapi.apipdm'):config('restapi.apipdmdev');
      $client = new Client();
      $requestListExisting = $client->request('GET', $host.'/customer/saving/'.$data['branch'].'/'.$data['pn'],
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

    public function pemasar($pn, $branch, $auth){
      $list_ao = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_tenaga_pemasar',
          'requestData' => [
            'id_user' => $pn,
            'kode_branch' => $branch
          ],
        ])
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $pn,
            'kode_branch' => $branch
          ],
        ])
      ])->post('form_params');

      $ao = $list_ao['responseData'];
      $fo = $list_fo['responseData'];

      if ($ao != null && $fo != null) {
        $result = array_merge_recursive($fo,$ao);
      } else {
        $result = [];
      }

      return $result;
    }

    public function get_referral(Request $request)
    {
      $branch_id = $request->header('branch');
      $referrals = Referral::where('branch_id', $branch_id)->get();
      return response()->success( [
          'message' => 'Sukses get data referral',
          'contents' => $referrals
        ]);
    }

    public function detail_referral(Request $request)
    {
      $ref_id = $request['ref_id'];
      $referral = Referral::where('ref_id', $ref_id)->get();

      return response()->success( [
          'message' => 'Sukses get detail referral',
          'contents' => $referral
        ]);
    }

    public function update_officer_ref(Request $request)
    {
      $ref_id = $request['ref_id'];
      $referral = Referral::where('ref_id', $ref_id);

      $data['officer_ref'] = $request['officer_ref'];
      $data['officer_name'] = $request['officer_name'];
      $data['status'] = $request['status'];

      // return $data;die();

      $update = $referral->update($data);

      if ($update) {
        return response()->success( [
            'message' => 'Sukses update officer referral',
            'contents' => Referral::where('ref_id', $ref_id)->get()
          ]);
      }
    }

    public function get_referral_by_officer(Request $request)
    {
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $referral = Referral::where('officer_ref', $pn)->orWhere('created_by', $pn)->get();
      return response()->success( [
          'message' => 'Sukses get data referral by officer',
          'contents' => $referral
        ]);
    }

    public function get_referral_by_branch(Request $request)
    {
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $referral = [];
      foreach (Referral::whereIn('officer_ref', $list_pn)->get() as $ref) {
        $referral[]= [
          "id"=> $ref->id,
          "ref_id"=> $ref->ref_id,
          "nik"=> $ref->nik,
          "cif"=> $ref->cif,
          "name"=> $ref->name,
          "phone"=> $ref->phone,
          "address"=> $ref->address,
          "product_type"=> $ref->product_type,
          "officer_ref"=> $ref->officer_ref,
          "officer_name"=> array_key_exists($ref->officer_ref, $pemasar_name) ? $pemasar_name[$ref->officer_ref]:'',
          "status"=> $ref->status,
          "created_by"=> $ref->created_by,
          "creator"=> array_key_exists($ref->created_by, $pemasar_name) ? $pemasar_name[$ref->created_by]:'',
          "point"=> $ref->point,
        ];
      }

      return response()->success( [
          'message' => 'Sukses get data referral by branch',
          'contents' => $referral
        ]);
    }

    public function store_referral(Request $request)
    {
      $pn = $request->header('pn');
      $name = $request->header('name');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $count = Referral::where('branch_id', $branch)->whereMonth('created_at', date('m'))->count() + 1;
      $len = 4;
      if(strlen($count) == $len) {
        $num = $count;
      } elseif( strlen($count) < $len ) {
          $num = '0';
          $x = $len - strlen($count);
          for ($i=1; $i < $x ; $i++) {
            $num .='0';
          }
          $num .= $count;
      }

      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $data['ref_id'] = date('ym').$branch.$num;
      $data['nik'] = $request['nik'];
      $data['cif'] = $request['cif'];
      $data['name'] = $request['name'];
      $data['phone'] = $request['phone'];
      $data['address'] = $request['address'];
      $data['product_type'] = $request['product_type'];

      //switch 
      //    case(1):$data['contact_time']="pagi";
      //    case(2):$data['contact_time']="siang";
      //    case(3):$data['contact_time']="sore";
      //    case(4):$data['contact_time']="malam";

      //switch 
      //    case(0):$data['intention']="sendiri";
      //    case(1):$data['intention']="keluarga/other";

      $data['contact_time'] = $request['contact_time'];
      $data['intention'] = $request['intention'];
      // $data['officer_ref'] = $request['officer_ref'];
      $data['status'] = $request['status'];
      $data['created_by'] = $pn;
      $data['point'] = $request['point'];
      $data['branch_id'] = $branch;
      $data['creator_name'] = $name;
      $data['longitude'] = $request['longitude'];
      $data['latitude'] = $request['latitude'];

      $save = Referral::create($data);
      if ($save) {
          return response()->success([
              'message' => 'Data Referral berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Referral Tidak Dapat Ditambah.',
      ], 500);
    }
}
