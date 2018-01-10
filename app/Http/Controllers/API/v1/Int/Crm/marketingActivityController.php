<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RestwsHc;

use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\ObjectActivity;
use App\Models\Crm\ActionActivity;

use App\Models\Crm\rescheduleActivity;
use App\Models\Crm\MarketingActivityFollowup;

class marketingActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $pn = $request->header('pn');
      // $marketingActivity = MarketingActivity::get();
      $list_ao = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_tenaga_pemasar',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      if ($list_ao != null && $list_fo != null) {
        $ao = $list_ao['responseData'];
        $fo = $list_fo['responseData'];

        $result = array_merge_recursive($fo,$ao);
        $pemasar = array_column($result, 'SNAME','PERNR' );
      } else {
        $pemasar = [];
      }

      // print_r($pemasar);
      $marketingActivity = [];
      foreach (MarketingActivity::where('pn', $pn)->orwhere('pn_join', $pn)->with('marketing')->get() as $activity) {
        $rescheduled = rescheduleActivity::where('activity_id',$activity->id)->count();
        $followUp = MarketingActivityFollowup::where('activity_id',$activity->id)->count();

        if($activity->pn != $activity->pn_join){
          if($activity->pn == $pn) {
            $ownership = 'main';
          }else {
            $ownership = 'join';
          }
        }else {
          $ownership = 'main';
        }

        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'marketing_activity_type' => $activity->marketing->activity_type,
          'pn_name' => array_key_exists($activity->pn, $pemasar) ? $pemasar[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join,$pemasar)? $pemasar[$activity->pn_join]: '',
          'desc' => $activity->desc,
          'address' => $activity->address,
          'ownership' => $ownership,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          ];
      }

      return response()->success( [
          'message' => 'Sukses',
          'contents' => $marketingActivity
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activity_by_officer(Request $request)
    {
      $pn = $request->header('pn');
      // $marketingActivity = MarketingActivity::get();
      $list_ao = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_tenaga_pemasar',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');


      if ($list_ao != null && $list_fo != null) {
        $ao = $list_ao['responseData'];
        $fo = $list_fo['responseData'];

        $result = array_merge_recursive($fo,$ao);
        $pemasar = array_column($result, 'SNAME','PERNR' );
      } else {
        $pemasar = [];
      }

      // print_r($pemasar);
      $marketingActivity = [];
      foreach (MarketingActivity::where('pn', $pn)->orwhere('pn_join', $pn)->get() as $activity) {
        $rescheduled = rescheduleActivity::where('activity_id',$activity->id)->count();
        $followUp = MarketingActivityFollowup::where('activity_id',$activity->id)->count();

        if($activity->pn != $activity->pn_join){
          if($activity->pn == $pn) {
            $ownership = 'main';
          }else {
            $ownership = 'join';
          }
        }else {
          $ownership = 'main';
        }

        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'pn_name' => array_key_exists($activity->pn, $pemasar) ? $pemasar[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join,$pemasar)? $pemasar[$activity->pn_join]: '',
          'desc' => $activity->desc,
          'address' => $activity->address,
          'ownership' => $ownership,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          ];
      }

      return response()->success( [
          'message' => 'Sukses',
          'contents' => $marketingActivity
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      $pn = $request->header('pn');
      $data['object_activity'] = ObjectActivity::all();
      $data['action_activity'] = ActionActivity::all();
      $data['marketings'] = Marketing::where('pn',$pn)->get();

      for($x=1;$x < 50; $x++){
        $pn = 66777 + $x;
        $data['tenaga_pemasar'] []= ['pn' => $pn, 'name' => 'FO - '.$x];
      }


      return response()->success( [
        'message' => 'Sukses',
        'contents' => $data
      ], 200 );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $data['pn'] = $request->header('pn');
      // $data['pn_name'] = $request['pn_name'];
      $data['object_activity'] = $request['object_activity'];
      $data['action_activity'] = $request['action_activity'];
      $data['start_date'] = date('Y-m-d H:i:s', strtotime($request['start_date'].$request['start_time']));
      $data['end_date'] = date('Y-m-d H:i:s', strtotime($request['end_date'].$request['end_time']));

      if(isset($request['longitude']) && isset($request['latitude']) ){
        $data['longitude'] = $request['longitude'];
        $data['latitude'] = $request['latitude'];
      }else{
        $data['longitude'] = 'unset';
        $data['latitude'] = 'unset';
      }

      $data['address'] = $request['address'];
      $data['marketing_id'] = $request['marketing_id'];
      $data['pn_join'] = ($request['pn_join']!='' ? $request['pn_join'] : 'null');
      // $data['join_name'] = $request['join_name'];
      $data['desc'] = $request['desc'];

      $save = MarketingActivity::create($data);

      if ($save) {
          $marketing_activity_type = Marketing::find($request['marketing_id']);
          $request['marketing_activity_type'] = $marketing_activity_type->activity_type;
          return response()->success([
              'message' => 'Data Activity berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Activity Tidak Dapat Ditambah.',
      ], 500);
    }
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store_by_pinca(Request $request)
    {
      $data['pn'] = $request['pn'];
      $data['object_activity'] = $request['object_activity'];
      $data['action_activity'] = $request['action_activity'];
      $data['start_date'] = date('Y-m-d H:i:s', strtotime($request['start_date'].$request['start_time']));
      $data['end_date'] = date('Y-m-d H:i:s', strtotime($request['end_date'].$request['end_time']));

      if(isset($request['longitude']) && isset($request['latitude']) ){
        $data['longitude'] = $request['longitude'];
        $data['latitude'] = $request['latitude'];
      }else{
        $data['longitude'] = 'unset';
        $data['latitude'] = 'unset';
      }

      $data['address'] = $request['address'];
      $data['marketing_id'] = $request['marketing_id'];
      $data['pn_join'] = ($request['pn_join']!='' ? $request['pn_join'] : 'null');
      $data['desc'] = $request['desc'];

      $save = MarketingActivity::create($data);

      if ($save) {
          return response()->success([
              'message' => 'Data Activity by pinca berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Activity by pinca Tidak Dapat Ditambah.',
      ], 500);
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
     * @param  int  $id of Activity
     * @return \Illuminate\Http\Response
     */
     public function reSchedule(Request $request)
     {
       $id = $request['activity_id'];
       $marketingActivity = MarketingActivity::find($id);
       $reSchedule['activity_id'] = $id;
       $reSchedule['desc'] = $request['desc'];
       $reSchedule['reason'] = $request['reason'];
       $reSchedule['origin_date'] = $marketingActivity->start_date;
       $reSchedule['reschedule_date'] = date('Y-m-d H:i:s', strtotime($request['start_date'].$request['start_time']));

       $updateMarketingActivity['start_date'] = $reSchedule['reschedule_date'];
       $updateMarketingActivity['end_date'] = date('Y-m-d H:i:s', strtotime($request['end_date'].$request['end_time']));

       $save = rescheduleActivity::create($reSchedule);

       if($save) {
         $marketingActivity->update($updateMarketingActivity);

         return response()->success([
           'message' => 'Data Activity berhasil direschedule.',
           'contents' => collect($save)->merge($request->all()),
         ], 201);
       }

       return response()->error([
           'message' => 'Data Activity Tidak Dapat direschedule.',
       ], 500);
     }

     /**
      * Update the specified resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @param  int  $id of Activity
      * @return \Illuminate\Http\Response
      */
      public function storeFollowUp(Request $request)
      {
        $id = $request['activity_id'];
        $mrk_id = $request['marketing_id'];

        $marketingActivity = MarketingActivity::find($id);
        // $marketing_id = $marketingActivity->marketing_id;
        // $marketing = Marketing::find($marketing_id);
        $marketing = Marketing::find($mrk_id);

        $followUp['activity_id'] = $id;
        $followUp['desc'] = $request['desc'];
        $followUp['fu_result'] = $request['fu_result'];

        if($request['fu_result']=='Done'){
          $followUp['count_rekening'] = $request['count_rekening'];
          $followUp['amount'] = $request['amount'];
          $followUp['target_commitment_date'] = $request['target_commitment_date'];
        }

        $save = MarketingActivityFollowup::create($followUp);

        $updateMarketingStatus['status'] = $request['fu_result'];

        if($request['fu_result']=='Done' || $request['fu_result']=='Batal'){
          $marketing->update($updateMarketingStatus);
        }

        if ($save) {
            return response()->success([
                'message' => 'Data Tindakan berhasil ditambah.',
                'contents' => collect($save)->merge($request->all()),
            ], 201);
        }

        return response()->error([
            'message' => 'Data tindakan Tidak Dapat Ditambah.',
        ], 500);

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


    public function activity_branch(Request $request)
    {
      $list_ao = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_tenaga_pemasar',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      if($list_fo!=NULL && $list_ao!=NULL){
        $fo_list = array_column($list_fo['responseData'], 'PERNR');
        $ao_list = array_column($list_ao['responseData'], 'PERNR');
        $list_pn = array_merge_recursive($fo_list, $ao_list);
        $result = $this->pemasar($request->header('pn'), $request->header('branch'), $request->header('Authorization'));
        $pn_name = array_column($result, 'SNAME', 'PERNR');
      } else {
        $list_pn = '';
      }

      $marketingActivity = [];
      foreach (MarketingActivity::whereIn('pn', $list_pn)->get() as $activity) {
        $rescheduled = rescheduleActivity::where('activity_id',$activity->id)->count();
        $followUp = MarketingActivityFollowup::where('activity_id',$activity->id)->count();
        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'pn_name' => array_key_exists($activity->pn, $pn_name) ? $pn_name[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join, $pn_name) ? $pn_name[$activity->pn_join]:'',
          'desc' => $activity->desc,
          'address' => $activity->address,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          ];
      }

       // return array_column($marketingActivity,'pn');die();
      return response()->success( [
          'message' => 'Success get marketing Activity by branch',
          'contents' => $marketingActivity
        ]);
    }

    public function activity_by_marketing(Request $request)
    {
      $list_ao = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_tenaga_pemasar',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      if($list_fo!=NULL && $list_ao!=NULL){
        $fo_list = array_column($list_fo['responseData'], 'PERNR');
        $ao_list = array_column($list_ao['responseData'], 'PERNR');
        $list_pn = array_merge_recursive($fo_list, $ao_list);
        $result = $this->pemasar($request->header('pn'), $request->header('branch'), $request->header('Authorization'));
        $pn_name = array_column($result, 'SNAME', 'PERNR');
      } else {
        $list_pn = '';
      }

      $marketing_id = $request['marketing_id'];
      $marketingActivity = [];
      foreach (MarketingActivity::where('marketing_id', $marketing_id)->get() as $activity) {
        $rescheduled = rescheduleActivity::where('activity_id',$activity->id)->count();
        $followUp = MarketingActivityFollowup::where('activity_id',$activity->id)->count();
        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'pn_name' => array_key_exists($activity->pn, $pn_name) ? $pn_name[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join, $pn_name) ? $pn_name[$activity->pn_join]:'',
          'desc' => $activity->desc,
          'address' => $activity->address,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          ];
      }

       // return array_column($marketingActivity,'pn');die();
      return response()->success( [
          'message' => 'Success get marketing Activity by branch',
          'contents' => $marketingActivity
        ]);

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
      ])->setHeaders([
        'Authorization' => $auth
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $pn,
            'kode_branch' => $branch
          ],
        ])
      ])->setHeaders([
        'Authorization' => $auth
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

    public function activity_by_customer(Request $request)
    {
      $list_ao = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_tenaga_pemasar',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      $list_fo = RestwsHc::setBody([
        'request' => json_encode([
          'requestMethod' => 'get_list_fo',
          'requestData' => [
            'id_user' => $request->header('pn'),
            'kode_branch' => $request->header('branch')
          ],
        ])
      ])->setHeaders([
        'Authorization' => $request->header('Authorization')
      ])->post('form_params');

      if($list_fo!=NULL && $list_ao!=NULL){
        $fo_list = array_column($list_fo['responseData'], 'PERNR');
        $ao_list = array_column($list_ao['responseData'], 'PERNR');
        $list_pn = array_merge_recursive($fo_list, $ao_list);
        $result = $this->pemasar($request->header('pn'), $request->header('branch'), $request->header('Authorization'));
        $pn_name = array_column($result, 'SNAME', 'PERNR');
      } else {
        $list_pn = '';
        $pn_name =[];
      }

      $cif = $request->input('cif');
      $nik = $request->input('nik');

      $customerActivity = [];
      $list=[];
      // $list = Marketing::where('cif', $cif)->where('nik', $nik)->get();
      foreach (Marketing::where('cif', $cif)->where('nik', $nik)->get() as $key => $marketing) {
        // echo $marketing->pn.'  ';
        $list[]=[
         $marketing->id
        ];
      }
      foreach (MarketingActivity::whereIn('marketing_id', $list)->orderBy('created_at', 'desc')->get() as $activity) {
        $rescheduled = rescheduleActivity::where('activity_id',$activity->id)->count();
        $followUp = MarketingActivityFollowup::where('activity_id',$activity->id)->count();
        $customerActivity[] = [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'pn_name' => array_key_exists($activity->pn, $pn_name) ? $pn_name[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join, $pn_name) ? $pn_name[$activity->pn_join]:'',
          'desc' => $activity->desc,
          'address' => $activity->address,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
        ];
      }
      // return $list;
      // dd($list);die();
      return response()->success( [
<<<<<<< HEAD
          'message' => 'Success get marketing Activity by customer',
=======
          'message' => 'Success get marketing Activity by Customer',
>>>>>>> 3174fd15758e6b49bea04538582e877ae05b9ee8
          'contents' => $customerActivity
      ]);

    }

    public function deleteAll(Request $request)
    {

      MarketingActivityFollowup::where('id', '!=', 0)->delete();
      MarketingActivity::where('id', '!=', 0)->delete();
      Marketing::where('id', '!=', 0)->delete();

      return response()->success([
          'message' => 'Semua data activity telah dihapus'
      ], 200);
    }
}
