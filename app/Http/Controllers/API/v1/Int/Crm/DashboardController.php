<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RestwsHc;
use RestwsSm;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


use App\Models\Crm\RestBrispot;
use App\Models\Crm\ProductType;
use App\Models\Crm\ActivityType;
use App\Models\Crm\Status;
use App\Models\Crm\ObjectActivity;
use App\Models\Crm\ActionActivity;
use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\MarketingActivityFollowup;



class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $data['product_type'] = ProductType::all();
      $data['activity_type'] = ActivityType::all();
      // $data['status'] = Status::all();

      foreach (Status::all() as $status) 
      {
        $data['status'][]=[
                             'id'=> $status->id,
                             'status_name'=>$status->status_name
                          ];
      }

      $additional =[
                      'id'=>0,
                      'status_name'=>'All'
                   ];

      if (isset($data['status'])) 
      {
        array_push($data['status'],$additional);
      }

      // return $data['status'];
      $data['object_activity'] = ObjectActivity::all();
      $data['action_activity'] = ActionActivity::all();

      return response()->success([
                                    'message' => 'Sukses',
                                    'contents' => $data
                                 ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pemasar(Request $request)
    {
      $list_ao = RestwsHc::setBody([
                                      'request' => json_encode([
                                                                  'requestMethod' => 'get_list_tenaga_pemasar',
                                                                  'requestData' => [
                                                                                      'id_user' => $request->header('pn'),
                                                                                      'kode_branch' => $request->header('branch')
                                                                                   ],
                                                               ])
                                   ])->post('form_params');

      $list_fo = RestwsHc::setBody([
                                      'request' => json_encode([
                                                                  'requestMethod' => 'get_list_fo',
                                                                  'requestData' => [
                                                                                      'id_user' => $request->header('pn'),
                                                                                      'kode_branch' => $request->header('branch')
                                                                                   ],
                                                               ])
                                  ])->post('form_params');

      $ao = $list_ao['responseData'];
      $fo = $list_fo['responseData'];

      if ($ao != null && $fo != null) 
      {
        $result = array_merge_recursive($fo,$ao);
      } 
      else 
      {
        $result = [];
      }

      return response()->success([
                                    'contents' => $result
                                 ]);
    }

    public function kinerja_pemasar(Request $request)
    {
      $pn = $request->header('pn');
      $client = new Client();
      $host = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.apipdm'):config('restapi.apipdmdev');
      $data = $client->request('GET', $host.'/customer/performance/officer/'.$pn,[
        'headers' =>
        [
          'Authorization' => 'Bearer '.$this->get_token()
          // 'Authorization' => 'Bearer 8288bdbcd66d6ac6dd0cfb21677edab663e2bb83'
        ]
      ]);
      $kinerja_pemasar = json_decode($data->getBody()->getContents(), true);

      return response()->success([
        'message' => $kinerja_pemasar['message'],
        'contents' => $kinerja_pemasar['data']
      ]);
    }

    public function sales_kit(Request $request)
    {
      $db_ext = new RestBrispot;
      $db_ext->setConnection('mysql2');
      $data = $db_ext->all();
      $sales_kit = [];
      $host = (env('APP_URL') == 'http://api.dev.net/')? 'http://10.35.65.111':'http://pinjaman.bri.co.id';
      $img_url = $host.'/brispot/uploads/saleskit_sme/';

      foreach ($data as $key => $value) {
        $sales_kit[]=[
          'id'=>$value->id,
          'type'=>$value->type,
          'headline'=>$value->headline,
          'caption'=>$value->caption,
          'filename'=>$value->filename,
          'img_url'=>$img_url.$value->filename,
          'filename_thumb'=>$value->filename_thumb,
          'thumb_url'=>$img_url.$value->filename_thumb,
          'description'=>$value->description,
          'uploaded_on'=> $value->uploaded_on,
          'uploaded_by'=>$value->uploaded_by
        ];
      }
      return response()->success([
        'message' => 'Sukses get Sales Kit',
        'contents' => $sales_kit
      ]);
    }

    public function marketing_summary_v2(Request $request)
    {
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar_branch($pn,$branch);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $data = Marketing::getMarketingSummary($request)->whereIn('pn',$list_pn)->get();
      $total = [];
      foreach ($data as $key => $value) {
        $total[$value->pn][]=
          $value->target
        ;
      }
      $status = [];
      foreach ($data as $key => $value) {
        $status[$value->pn][$value->status][]=[
          $value->target
        ];
      }

      $data_summary = [];
      $ext_nul = [
        1=>'0',
        2=>'00',
        3=>'000',
        4=>'0000'
      ];
      foreach ($data as $key => $value) {
        $data_summary[$value->pn]=[
          'Pemasar'=>$ext_nul[8-strlen($value->pn)].$value->pn,
          'Nama'=>array_key_exists($ext_nul[8-strlen($value->pn)].$value->pn, $pemasar_name) ? $pemasar_name[$ext_nul[8-strlen($value->pn)].$value->pn]:'',
          'Total'=>count($total[$value->pn]),
          'Prospek'=>(array_key_exists('Prospek',$status[$value->pn]))?count($status[$value->pn]['Prospek']):0,
          'On Progress'=>(array_key_exists('On Progress',$status[$value->pn]))?count($status[$value->pn]['On Progress']):0,
          'Done'=>(array_key_exists('Done',$status[$value->pn]))?count($status[$value->pn]['Done']):0,
          'Batal'=>(array_key_exists('Batal',$status[$value->pn]))?count($status[$value->pn]['Batal']):0
        ];
      }
      $marketing_summary = [];
      foreach ($data_summary as $key => $value) {
        $marketing_summary[]=$value;
      }



      if ($marketing_summary) {
          return response()->success([
              'message' => 'Sukses get Marketing Summary.',
              'contents' => $marketing_summary,
          ], 200);
      }

      return response()->error([
          'message' => 'Gagal get Marketing Summary.',
      ], 200);
    }

    public function marketing_summary(Request $request)
    {
      $pn = $request->header('pn');

      if ($request->has('branch')) 
      {
        $branch = $request['branch'];
      } else {
        $branch = $request->header('branch');
      }

      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar_branch($pn,$branch);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $data = Marketing::getMarketingSummary($request)->whereIn('marketings.pn',$list_pn)->get();
      //return $data;

      $total = [];

      foreach ($data as $key => $value) {
        $total[$value->pn][]=
          $value->target
        ;
      }
      //return $total;

      $status = [];

      foreach ($data as $key => $value) 
      {
        $status[$value->pn][$value->status][]=[
          $value->target
        ];
      }
      //return $status

      $data_summary = [];
      $ext_nul = [
                    1=>'0',
                    2=>'00',
                    3=>'000',
                    4=>'0000'
                 ];

      $activity = [];
      $lkn = [];

      foreach ($data as $key => $value) 
      {
        $activity[$value->pn][$value->id] = (MarketingActivity::where('marketing_id', $value->id)->where('desc','!=', 'first')->first()!=null)?1:0;
        //return $activity;

        $latest_act[$value->pn][$value->id] = MarketingActivity::where('marketing_id', $value->id)->where('desc','!=', 'first')->orderBy('created_at','asc')->first();
        //return $activity;

        $lkn[$value->pn][$value->id] = ($activity[$value->pn][$value->id]==1)?(MarketingActivityFollowup::where('activity_id',$latest_act[$value->pn][$value->id]['id'])->first()!=null)?1:0:0;
        //return $lkn;

        $data_summary[$value->pn]=[
          'Pemasar'=>$ext_nul[8-strlen($value->pn)].$value->pn,
          'Nama'=>array_key_exists($ext_nul[8-strlen($value->pn)].$value->pn, $pemasar_name) ? $pemasar_name[$ext_nul[8-strlen($value->pn)].$value->pn]:'',
          'Total'=>count($total[$value->pn]),
          'Prospek'=>array_sum(array_values($activity[$value->pn])),
          'On Progress'=>array_sum(array_values($lkn[$value->pn])),
          'Done'=>(array_key_exists('Done',$status[$value->pn]))?count($status[$value->pn]['Done']):0,
          'Batal'=>(array_key_exists('Batal',$status[$value->pn]))?count($status[$value->pn]['Batal']):0
        ];
      }
      //return $activity;
      //return $activity;
      //return $lkn;


      $marketing_summary = [];

      foreach ($data_summary as $key => $value) 
      {
        $marketing_summary[]=$value;
      }

      if (!empty($marketing_summary)) 
      {
          return response()->success([
                                        'message' => 'Sukses get Marketing Summary.',
                                        'contents' => $marketing_summary,
                                     ], 200);
      }

      return response()->error([
                                  'message' => 'Marketing Summary tidak ditemukan.',
                                  'contents' => $marketing_summary,
                               ], 200);
    }

    public function marketing_summary_amount(Request $request)
    {
      $pn = $request->header('pn');
      if ($request->has('branch')) {
        $branch = $request['branch'];
      } else {
        $branch = $request->header('branch');
      }
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar_branch($pn,$branch);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $data = Marketing::getMarketingSummary($request)->whereIn('marketings.pn',$list_pn)->get();
      $total = [];
      foreach ($data as $key => $value) {
        $total[$value->pn][]=
          str_replace(".", "", $value->target)
        ;
      }
      $status = [];
      foreach ($data as $key => $value) {
        $status[$value->pn][$value->status][]=
          str_replace(".", "", $value->target)
        ;
      }

      $data_summary = [];
      $ext_nul = [
        1=>'0',
        2=>'00',
        3=>'000',
        4=>'0000'
      ];
      $activity = [];
      $lkn = [];
      foreach ($data as $key => $value) 
      {
        $status[$value->pn]['Prospek']=array_key_exists('Prospek',$status[$value->pn])?$status[$value->pn]['Prospek']:[];
        $status[$value->pn]['On Progress']=array_key_exists('On Progress', $status[$value->pn])?$status[$value->pn]['On Progress']:[];
        $status[$value->pn]['Done']=array_key_exists('Done',$status[$value->pn])?$status[$value->pn]['Done']:[];
        $status[$value->pn]['Batal']=array_key_exists('Batal', $status[$value->pn])?$status[$value->pn]['Batal']:[];

        $activity[$value->pn][$value->id] = (MarketingActivity::where('marketing_id', $value->id)->where('desc','!=', 'first')->first()!=null)?1:0;
        $latest_act[$value->pn][$value->id] = MarketingActivity::where('marketing_id', $value->id)->where('desc','!=', 'first')->orderBy('created_at','asc')->first();
        $lkn[$value->pn][$value->id] = ($activity[$value->pn][$value->id]==1)? Marketing::find($value->id)->target:0;
        $data_summary[$value->pn]=[
          'Pemasar'=>$ext_nul[8-strlen($value->pn)].$value->pn,

          'Nama'=>array_key_exists($ext_nul[8-strlen($value->pn)].$value->pn, $pemasar_name) ? $pemasar_name[$ext_nul[8-strlen($value->pn)].$value->pn]:'',

          'Total'=>array_sum($total[$value->pn]),

          'Prospek'=>(array_key_exists('Prospek',$status[$value->pn])||array_key_exists('On Progress',$status[$value->pn])||array_key_exists('Done',$status[$value->pn])||array_key_exists('Batal',$status[$value->pn]))?(array_sum($status[$value->pn]['Prospek'])+array_sum($status[$value->pn]['On Progress'])+array_sum($status[$value->pn]['Done'])+array_sum($status[$value->pn]['Batal'])):0,//:9,//array_sum($status[$value->pn]['On Progress'])+array_sum($status[$value->pn]['Done'])+array_sum($status[$value->pn]['Batal'])):0,

          'On Progress'=>(array_key_exists('On Progress',$status[$value->pn])||array_key_exists('Done',$status[$value->pn])||array_key_exists('Batal',$status[$value->pn]))?(array_sum($status[$value->pn]['On Progress'])+array_sum($status[$value->pn]['Done'])+array_sum($status[$value->pn]['Batal'])):0,//array_sum(array_values($lkn[$value->pn])),

          'Done'=>(array_key_exists('Done',$status[$value->pn]))?array_sum($status[$value->pn]['Done']):0,

          'Batal'=>(array_key_exists('Batal',$status[$value->pn]))?array_sum($status[$value->pn]['Batal']):0
        ];
        /* Backup 
        $activity[$value->pn][$value->id] = (MarketingActivity::where('marketing_id', $value->id)->where('desc','!=', 'first')->first()!=null)?1:0;
        $latest_act[$value->pn][$value->id] = MarketingActivity::where('marketing_id', $value->id)->where('desc','!=', 'first')->orderBy('created_at','asc')->first();
        $lkn[$value->pn][$value->id] = ($activity[$value->pn][$value->id]==1)? Marketing::find($value->id)->target:0;
        $data_summary[$value->pn]=[
          'Pemasar'=>$ext_nul[8-strlen($value->pn)].$value->pn,
          'Nama'=>array_key_exists($ext_nul[8-strlen($value->pn)].$value->pn, $pemasar_name) ? $pemasar_name[$ext_nul[8-strlen($value->pn)].$value->pn]:'',
          'Total'=>array_sum($total[$value->pn]),
          'Prospek'=>(array_key_exists('Prospek',$status[$value->pn]))?array_sum($status[$value->pn]['Prospek']):0,
          'On Progress'=>array_sum(array_values($lkn[$value->pn])),
          'Done'=>(array_key_exists('Done',$status[$value->pn]))?array_sum($status[$value->pn]['Done']):0,
          'Batal'=>(array_key_exists('Batal',$status[$value->pn]))?array_sum($status[$value->pn]['Batal']):0
        ];*/
      }
      $marketing_summary = [];
      foreach ($data_summary as $key => $value) {
        $marketing_summary[]=$value;
      }

      if (!empty($marketing_summary)) {
          return response()->success([
              'message' => 'Sukses get Marketing Summary.',
              'contents' => $marketing_summary,
          ], 200);
      }

      return response()->error([
          'message' => 'Marketing Summary tidak ditemukan.',
          'contents' => $marketing_summary,
      ], 200);
    }

    public function pemasar_branch($pn, $branch){
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

    public function officer($pn, $branch){
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

    public function pemasar_kanwil(Request $request)
    {
      $requestPost =[
				'app_id' => 'mybriapi',
				'region' => $request->input('region')
			];

			$data = RestwsHc::setBody([
						'request' => json_encode([
								'requestMethod' => 'get_list_kanca_from_kanwil',
								'requestData' => $requestPost
						])
				])
				->post( 'form_params' );

      $list_kanca =[];
      foreach ($data['responseData'] as $key_kanca => $value_kanca) {
        $list_kanca[] =
        substr( '00000' . $value_kanca['mainbr'], -5 )
        ;
      }

      foreach($list_kanca as $kanca){
        foreach($this->officer($request->header('pn'),$kanca) as $value){
        $pemasar[]= $value;
        }
      }

			return response()->success( [
					'message' => 'Sukses get list pemasar kanwil',
					'contents' => $pemasar
			], 200 );

    }

    public function pemasar_cabang(Request $request)
    {
      $pn = $request->header('pn');
      $kanca = $request->input('branch');
      $pemasar = $this->officer($pn,$kanca);

			return response()->success( [
					'message' => 'Sukses get list pemasar cabang',
					'contents' => $pemasar
			], 200 );

    }
}
