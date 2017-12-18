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

class AccountController extends Controller
{
    public function index(Request $request)
    {
      $leads = RestwsHc::setBody([
          "request" => json_encode([
            "requestMethod" => "get_customer_leads",
            "requestData"=> [
              "id_user" => request()->header( 'pn' ),
              "kode_branch" => request()->header( 'branch' ), // 5 digit uker
              "type_request" => $request->input('type_request'), // list or search
              "type_usulan" => $request->input('type_usulan'), // kpr or kkb
              "limit" => $request->input('limit'),
              "page" => $request->input('page'),
              "order_by" => $request->input('order_by'), // nama or amount
              "search_value" => $request->input('search_value')
            ]
          ])
      ])
      ->setHeaders(
        [
        'Authorization' => request()->header( 'Authorization' )
        // 'Content-Type' => 'application/x-www-form-urlencoded',
        // 'Authorization' => 'Bearer 0nga1q6zz3vw50136rhltxqsc448mvjo0svt2sur5wm8a0jdpphtc5kitaslei7zhi9dtnguii1078h9en6wbretyvnxj2qvocsc4z10hdeu7t7asmjr9bwmhwqk5rdn'
        ]
      )
      ->post('form_params');

      return response()->success( [
          'message' => 'Sukses',
          'contents' => $leads['responseData']
        ]);
    }
}
