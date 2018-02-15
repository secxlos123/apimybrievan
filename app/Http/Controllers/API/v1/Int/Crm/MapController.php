<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\MarketMapping;

use RestwsHc;

class MapController extends Controller
{
    public function market_map(Request $request)
    {
      $market_map = [];
      foreach (MarketMapping::all() as $market) {
        $market_map []= [
          'market_name'=> $market->market_name,
          'category'=> $market->category,
          'longitude'=> $market->longitude,
          'latitude'=> $market->latitude,
          'address'=> $market->address
        ];
      }

      if ($market_map) {
          return response()->success([
              'message' => 'Sukses get Market Map.',
              'contents' => $market_map,
          ], 201);
      }

      return response()->error([
          'message' => 'Gagal get Market Map.',
      ], 500);
    }

    public function activity_map(Request $request)
    {
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $pemasar = $this->pemasar($pn,$branch);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $activity_map = [];
      foreach (MarketingActivity::where('desc', '!=', 'first')->whereIn('pn', $list_pn)->with('marketing')->get() as $activity) {
        $officer1 = array_key_exists($activity->pn, $pemasar_name) ? $pemasar_name[$activity->pn]:'';
        $officer2 = array_key_exists($activity->pn_join, $pemasar_name) ? ' ,'.$pemasar_name[$activity->pn_join]:'';
        $officer = $officer1.$officer2;
        $activity_map[]= [
          'id' => $activity->id,
          'nama' => $activity->marketing->nama,
          'pn' => $activity->pn,
          'pn_join' => $activity->pn_join,
          'officer_name' => $officer,
          'marketing_activity_type' => $activity->marketing->activity_type,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'address' => $activity->address,
          ];
      }


      if ($activity_map) {
          return response()->success([
              'message' => 'Sukses get Activity Map.',
              'contents' => $activity_map,
          ], 201);
      }

      return response()->error([
          'message' => 'Gagal get Activity Map.',
      ], 500);

    }


    public function pemasar($pn, $branch){
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
}
