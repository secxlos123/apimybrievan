<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\MarketMapping;
use App\Models\Crm\CustomerGroup;
use RestwsHc;

class marketMappingController extends Controller
{
    public function index(Request $request)
    {
      $marketingMaps = MarketMapping::all();
      return response()->success( [
          'message' => 'Sukses get data market mapping',
          'contents' => $marketingMaps
        ]);
    }

    public function store(Request $request)
    {
      $data['category']= $request['category'];
      $data['market_name']= $request['market_name'];
      $data['province']= $request['province'];
      $data['city']= $request['city'];
      $data['longitude']= $request['longitude'];
      $data['latitude']= $request['latitude'];
      $data['pot_account']= $request['pot_account'];
      $data['pot_fund']= $request['pot_fund'];
      $data['pot_loan']= $request['pot_loan'];
      $data['pot_transaction']= $request['pot_transaction'];

      $save = MarketMapping::create($data);
      if ($save) {
          return response()->success([
              'message' => 'Data Market Mapping berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Market Mapping Tidak Dapat Ditambah.',
      ], 500);
    }

}
