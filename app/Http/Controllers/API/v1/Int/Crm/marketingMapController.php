<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\MarketingMap;

class marketingMapController extends Controller
{
    public function index(Request $request)
    {
      $marketingMaps = MarketingMap::all();
      return response()->success( [
          'message' => 'Sukses get data marketing map',
          'contents' => $marketingMaps
        ]);
    }

    public function store(Request $request)
    {
      $data['category']= $request['category'];
      $data['district_name']= $request['district_name'];
      $data['address']= $request['address'];
      $data['city']= $request['city'];
      $data['longitude']= $request['longitude'];
      $data['latitude']= $request['latitude'];
      $data['pot_account']= $request['pot_account'];
      $data['pot_fund']= $request['pot_fund'];
      $data['pot_loan']= $request['pot_loan'];
      $data['pot_transaction']= $request['pot_transaction'];

      $save = MarketingMap::create($data);
      if ($save) {
          return response()->success([
              'message' => 'Data Marketing Map berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Marketing Map Tidak Dapat Ditambah.',
      ], 500);
    }
}
