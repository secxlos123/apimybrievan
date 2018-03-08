<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\MarketingActivityFollowup;
use DB;
use RestwsHc;

class reportController extends Controller
{
    public function report_marketings(Request $request)
    {
      if($request->has('region')){
        $region = $request->input('region');
        $list_kanca = $this->list_kanca_for_kanwil($region);
        $request['list_branch'] =array_keys($list_kanca);
      }else{
        $list_kanca = $this->list_all_kanca();
        $region = $list_kanca[$request->header('branch')]['region_id'];
      }
      $list_kanwil = array_column($this->list_kanwil(),'region_name', 'region_id');
      $data = Marketing::getReports($request)->get();
      $marketings = [];
      foreach ($data as $key => $value) {
        $last_activity = MarketingActivity::where('desc', '!=', 'first')->where('marketing_id',$value->id)->orderBy('created_at', 'desc')->first();
        $result = MarketingActivityFollowup::where('activity_id',$last_activity['id'])->orderBy('created_at', 'desc')->first();
        $branch = $value->branch;
        $marketings[] =[
          "pn"=>$value->pn,
          "bulan"=>date('M',strtotime($value->created_at)),
          "tahun"=>date('Y',strtotime($value->created_at)),
          "wilayah"=> isset($list_kanwil[$region]) ? $list_kanwil[$region] : '',
          "cabang"=> array_key_exists($branch, $list_kanca)?$list_kanca[$branch]['mbdesc']:'',
          "uker"=> $value->branch,
          "fo_name"=> $value->pn,
          "product_type"=> $value->product_type,
          "activity_type"=> $value->activity_type,
          "nama"=> $value->nama,
          "target"=> $value->target,
          "rekening" => $result['account_number'],
          "volume_rekening" => $result['amount'],
          "target_closing_date"=> $value->target_closing_date,
          "status"=> $value->status,
          "result" => $result['fu_result'],
          "activity" => $last_activity,
        ];
      }
      return response()->success( [
        'message' => 'Sukses get list report marketings',
        'contents' => $marketings
      ], 200 );

    }

    public function report_activities(Request $request)
    {
      if($request->has('region')){
        $region = $request->input('region');
        $list_kanca = $this->list_kanca_for_kanwil($region);
        $request['list_branch'] =array_keys($list_kanca);
      }else{
        $list_kanca = $this->list_all_kanca();
        $region = $list_kanca[$request->header('branch')]['region_id'];
      }
      $list_kanwil = array_column($this->list_kanwil(),'region_name', 'region_id');
      $data = MarketingActivity::with('fu_result')->getReports($request)->get();
      return $data;die();
      $activities = [];

      return response()->success( [
        'message' => 'Sukses get list report Activities',
        'contents' => $activities
      ], 200 );
    }

    public function list_kanwil()
  	{
        try {
          $list_kanwil = RestwsHc::setBody([
            'request' => json_encode([
              'requestMethod' => 'get_list_kanwil',
              'requestData' => [
                'app_id' => 'mybriapi'
              ],
            ])
          ])
          ->post( 'form_params' );

          if ($list_kanwil['responseCode'] == '00' ) {

            $list_kanwil[ 'responseData' ] = array_map( function( $content ) {
              return [
                'region_id' => $content[ 'region' ],
                'region_name' => $content[ 'rgdesc' ],
                'branch_id' => $content[ 'branch' ]
              ];
            }, $list_kanwil[ 'responseData' ] );

            $kanwil_list = $list_kanwil[ 'responseData' ];

            return $kanwil_list;

          }
        } catch (\Exception $e) {
          $error = 'Gagal Get List kanwil';
          return $error;
        }

  	}

  	public function get_kanca_kanwil($region)
  	{
      try {
        $requestPost =[
          'app_id' => 'mybriapi',
          'region' => $region
        ];

        $list_kanca_kanwil = RestwsHc::setBody([
          'request' => json_encode([
            'requestMethod' => 'get_list_kanca_from_kanwil',
            'requestData' => $requestPost
          ])
        ])
        ->post( 'form_params' );

        return $list_kanca_kanwil;
      } catch (\Exception $e) {
        $error = 'Gagal Get List kanca region '.$region;
        return $error;
      }

  	}

    public function list_kanca_for_kanwil($region)
    {
      $list_all_kanca = [];
        $len = 5;
        $con =[
        1 =>'0',
        2=>'00',
        3=>'000',
        4=>'0000'
        ];

      $data = $this->get_kanca_kanwil($region);
      $list_kanca =[];
      foreach ($data['responseData'] as $key_kanca => $value_kanca) {
        $list_kanca[$con[$len-strlen($value_kanca['mainbr'])].$value_kanca['mainbr']] =
        [
           'mbdesc' => $value_kanca['mbdesc'],
           'region_id' => $region

           ]
        ;
      }
      return $list_kanca;
    }

    public function list_all_kanca()
    {
      $list_kanwil = $this->list_kanwil();
      $list_all_kanca = [];
        $len = 5;
        $con =[
        1 =>'0',
        2=>'00',
        3=>'000',
        4=>'0000'
        ];

      foreach ($list_kanwil as $key => $value) {
        $list_kanca = $this->get_kanca_kanwil($value['region_id']);
        foreach ($list_kanca['responseData'] as $key_kanca => $value_kanca) {
         $list_all_kanca[$con[$len-strlen($value_kanca['mainbr'])].$value_kanca['mainbr']] =
         [
           'mbdesc' => $value_kanca['mbdesc'],
           'region_id' => $value['region_id']

           ]
          ;
        }
      }


      return $list_all_kanca;
    }

  	public function get_uker_kanca($branch_code)
  	{
      try {

        $requestPost =[
          'app_id' => 'mybriapi',
          'branch_code' => $branch_code
        ];

        $list_uker_kanca = RestwsHc::setBody([
          'request' => json_encode([
            'requestMethod' => 'get_list_uker_from_cabang',
            'requestData' => $requestPost
          ])
        ])
        ->post( 'form_params' );

        return $list_uker_kanca;

      } catch (\Exception $e) {
        $error = 'Gagal Get List uker kanca '.$branch_code;
        return $error;
      }

  	}
}
