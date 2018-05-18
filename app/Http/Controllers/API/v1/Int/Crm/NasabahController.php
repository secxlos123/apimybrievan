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

// class AccountController extends Controller
// {
//     public function index(Request $request)
//     {
//       $kode_kanwil = $request->input('kode_kanwil');
//       $main_branch = $request->input('main_branch');
//       $branch = $request->input('branch');
//       $pn = $request->header('pn');
//       $apiPdmToken = apiPdmToken::latest('id')->first()->toArray();
//       // $apiPdmToken = $apiPdmToken[0];
//
//       if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
//         $token = $apiPdmToken['access_token'];
//         $detailByCif = $this->byCif($cif, $token);
//
//         return response()->success( [
//             'message' => 'Sukses',
//             'contents' => $detailByCif['data']['info'][0]
//         ]);
//       } else {
//         $briConnect = $this->briconnectToken();
//         $apiPdmToken = apiPdmToken::get()->toArray();
//         // $apiPdmToken = $apiPdmToken[0];
//
//         $token = $apiPdmToken['access_token'];
//         $detailByCif = $this->byCif($cif, $token);
//
//         return response()->success( [
//             'message' => 'Sukses',
//             'contents' => $detailByCif['data']['info'][0]
//         ]);
//       }
//     }
//
//     public function listExisting($cif, $token)
//     {
//       $client = new Client();
//       $requestLeadsDetail = $client->request('GET', 'http://172.18.44.182/customer/saving/'.'D/59/1001/00138617',
//         [
//           'headers' =>
//           [
//             'Authorization' => 'Bearer '.$token
//           ]
//         ]
//       );
//       $leadsDetail = json_decode($requestLeadsDetail->getBody()->getContents(), true);
//
//       return $leadsDetail;
//     }
// }
