<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\MarketMapping;
use App\Models\Crm\MarketCustomerMapping;
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
      $data['pos_code']= $request['pos_code'];
      $data['longitude']= $request['longitude'];
      $data['latitude']= $request['latitude'];
      $data['address']= $request['address'];
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
	
    public function update_marketMap(Request $request)
    {
	$id = $request['market_id'];
	$market_map = MarketMapping::find($id);
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

        $update = $market_map->update($data);
        if ($update) {
                return response()->success([
                'message' => 'Data Marketing Map berhasil diupdate.',
                'contents' => collect($update)->merge($request->all()),
                ], 201);
        }

        return response()->error([
                'message' => 'Data Marketing Map Tidak Dapat diupdate.',
        ], 500);

    }

    public function detail_market(Request $request)
    {
      $market_id = $request['market_mapping_id'];
      $market_detail = MarketMapping::find($market_id);

      if ($market_detail) {
          return response()->success([
              'message' => 'Sukses get Detail Market',
              'contents' => $market_detail
          ], 201);
      }

      return response()->error([
          'message' => 'Gagal get Detail Market',
      ], 500);
    }

    public function store_mapping_customer(Request $request)
    {
      $data['customer_name'] = $request['customer_name'];
      $data['cif'] = $request['cif'];
      $data['nik'] = $request['nik'];
      $data['category'] = $request['category'];
      $data['market_mapping_id'] = $request['market_mapping_id'];
      $data['created_by'] = $request->header('pn');
      $data['creator_name'] = $request->header('name');
      $data['branch'] = $request->header('branch');
      $data['uker'] = $request->header('uker');
// return $data;die();
      $save = MarketCustomerMapping::create($data);

      if ($save) {
          return response()->success([
              'message' => 'Data Market Customer Mapping berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Market Customer Mapping Tidak Dapat Ditambah.',
      ], 500);
    }

    public function customer_mapping(Request $request)
    {
      $customers = MarketCustomerMapping::orderBy('customer_name', 'asc')->get();
      if ($customers) {
          return response()->success([
              'message' => 'Sukses get customers mapping',
              'contents' => $customers,
          ], 201);
      }

      return response()->error([
          'message' => 'Gagal get customers mapping.',
      ], 500);
    }

    public function customer_by_market(Request $request)
    {
      $market_id = $request['market_mapping_id'];
      $customers = MarketCustomerMapping::where('market_mapping_id', $market_id)->orderBy('customer_name', 'asc')->get();

      if ($customers) {
          return response()->success([
              'message' => 'Sukses get customers by market',
              'contents' => $customers,
          ], 201);
      }

      return response()->error([
          'message' => 'Gagal get customers by market.',
      ], 500);
    }

}
