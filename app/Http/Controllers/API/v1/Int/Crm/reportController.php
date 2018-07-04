<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\MarketingActivityFollowup;
use App\Models\Crm\MarketingNote;
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
        $pemasar = array_column($this->pemasar_kanwil($request->header('pn'), $region),"SNAME","PERNR");
      }else{
        $list_kanca = $this->list_all_kanca();
        $region = $list_kanca[$request->header('branch')]['region_id'];
        $pemasar = array_column($this->pemasar_kanwil($request->header('pn'), $region),"SNAME","PERNR");
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
         // "bulan"=>date('M',strtotime($value->created_at)),
          "tgl_pembuatan"=>date('Y-m-d',strtotime($value->created_at)),
	  // "tanggal_pembuatan"=>('Y-M-D',strtotime($value->created_at)),
          "wilayah"=> isset($list_kanwil[$region]) ? $list_kanwil[$region] : '',
          "cabang"=> array_key_exists($branch, $list_kanca)?$list_kanca[$branch]['mbdesc']:'',
          "uker"=> $value->branch,
          "fo_name"=> $pemasar[substr( '00000000' . $value->pn, -8 )],
          "product_type"=> $value->product_type,
          "activity_type"=> $value->activity_type,
          "nama"=> $value->nama,
          "catatan" => MarketingNote::where('marketing_id',$value->id)->get(),
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
        $pemasar = array_column($this->pemasar_kanwil($request->header('pn'), $region),"SNAME","PERNR");
      }else{
        $list_kanca = $this->list_all_kanca();
        $region = $list_kanca[$request->header('branch')]['region_id'];
        $pemasar = array_column($this->pemasar_kanwil($request->header('pn'), $region),"SNAME","PERNR");
      }
      $list_kanwil = array_column($this->list_kanwil(),'region_name', 'region_id');
      $data = MarketingActivity::getReports($request)->get();

      $activities = [];

      foreach ($data as $key => $value) {
        $branch = $value->branch;
        $activities[] =[
          "pn"=>$value->pn,
          "wilayah"=> isset($list_kanwil[$region]) ? $list_kanwil[$region] : '',
          "cabang"=> array_key_exists($branch, $list_kanca)?$list_kanca[$branch]['mbdesc']:'',
          "fo_name"=> array_key_exists(substr('00000000'.$value->pn,-8), $pemasar) ? $pemasar[substr( '00000000' . $value->pn, -8 )]:"",
          "activity"=> $value->object_activity,
          "tujuan"=> $value->action_activity,
          "tanggal"=> $value->start_date,
          "alamat"=> $value->address,
          "marketing_type" => $value->activity_type,
          "nama" => $value->nama,
          "result" => $value->fu_result,
          "desc" => $value->desc,
          "result_date" => $value->created_at
        ];
      }

      return response()->success( [
        'message' => 'Sukses get list report Activities',
        'contents' => $activities
      ], 200 );
    }


   public function report_referrals(Request $request)
    {
      if($request->has('region'))
      {
        $region = $request->input('region');
        $list_kanca = $this->list_kanca_for_kanwil($region);
        $request['list_branch'] =array_keys($list_kanca);
        $pemasar = array_column($this->pemasar_kanwil($request->header('pn'), $region),"SNAME","PERNR");
      }
      else
      {
        $list_kanca = $this->list_all_kanca();
        $region = $list_kanca[$request->header('branch')]['region_id'];
        $pemasar = array_column($this->pemasar_kanwil($request->header('pn'), $region),"SNAME","PERNR");
      }

      $list_kanwil = array_column($this->list_kanwil(),'region_name', 'region_id');
      
      $data = Referral::getReports($request)->get();
      
      $referrals = [];
      
      foreach ($data as $key => $value) 
      {
        
        $last_activity = MarketingActivity::where('desc', '!=', 'first')->where('marketing_id',$value->id)->orderBy('created_at', 'desc')->first();
        
        $result = MarketingActivityFollowup::where('activity_id',$last_activity['id'])->orderBy('created_at', 'desc')->first();
        
        $branch = $value->branch_id;
        
        $referrals[] = [
                          "pn"=>$value->pn,
                          "tgl_pembuatan" => date('Y-m-d',strtotime($value->created_at)),
                          "wilayah"=> isset($list_kanwil[$region]) ? $list_kanwil[$region] : '',
                          "cabang"=> array_key_exists($branch, $list_kanca)?$list_kanca[$branch]['mbdesc']:'',
                          "uker"=> $value->branch_id,
                          "fo_name"=> $pemasar[substr( '00000000' . $value->pn, -8 )],
                          "ref_id"=> $value->ref_id,
                          "nama_ref"=> $value->name, 
                          "product_type"=>$value->product_type,
                          "owner"=>$value->creator_name,
                          "officer"=>$value->officer_name,
                          "status"=>$value->status,
                        ];
      
      }

      return response()->success([
                                    'message' => 'Sukses get list report marketings',
                                    'contents' => $marketings
      
                                 ], 200 );

    }
    


    public function list_kanwil(){
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

  	public function get_kanca_kanwil($region){
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

    public function list_kanca_for_kanwil($region){
      $list_all_kanca = [];

      $data = $this->get_kanca_kanwil($region);
      $list_kanca =[];
      foreach ($data['responseData'] as $key_kanca => $value_kanca) {
        $list_kanca[substr( '00000' . $value_kanca['mainbr'], -5 )] =
        [
           'mbdesc' => $value_kanca['mbdesc'],
           'region_id' => $region

           ]
        ;
      }
      return $list_kanca;
    }

    public function list_all_kanca(){
      $list_kanwil = $this->list_kanwil();
      $list_all_kanca = [];

      foreach ($list_kanwil as $key => $value) {
        $list_kanca = $this->get_kanca_kanwil($value['region_id']);
        foreach ($list_kanca['responseData'] as $key_kanca => $value_kanca) {
         $list_all_kanca[substr( '00000' . $value_kanca['mainbr'], -5 )] =
         [
           'mbdesc' => $value_kanca['mbdesc'],
           'region_id' => $value['region_id']

           ]
          ;
        }
      }


      return $list_all_kanca;
    }

  	public function get_uker_kanca($branch_code){
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

    public function pemasar_kanwil($pn, $region){
      $list_kanca = array_keys($this->list_kanca_for_kanwil($region));
      foreach($list_kanca as $kanca){
        foreach($this->pemasar($pn,$kanca) as $value){
        $pemasar[]= $value;
        }
      }
      return $pemasar;

    }
}
