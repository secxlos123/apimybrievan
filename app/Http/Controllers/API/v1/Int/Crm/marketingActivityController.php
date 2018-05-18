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
use App\Models\Crm\Referral;
use App\Models\Crm\PhoneDuration;

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
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);
      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
      } else {
        $pemasar_name = [];
      }

      // print_r($pemasar_name);
      $marketingActivity = [];
      foreach (MarketingActivity::where('pn', $pn)->where('desc', '!=', 'first')->orwhere('pn_join', $pn)->with('marketing')->with('fu_result')->get() as $activity) {
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

        $fu_result = MarketingActivityFollowup::where('activity_id',$activity->id)->first();
        $url = env('APP_URL').'uploads/crm/lkn/';

        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'marketing_activity_type' => $activity->marketing->activity_type,
          'pn_name' => array_key_exists($activity->pn, $pemasar_name) ? $pemasar_name[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'nama' => $activity->marketing->nama,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join,$pemasar_name)? $pemasar_name[$activity->pn_join]: '',
          'notes' => $activity->desc,
          'address' => $activity->address,
          'ownership' => $ownership,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          // 'fu_result'=>MarketingActivityFollowup::where('activity_id',$activity->id)->first()
          'desc'=> $fu_result['desc'],
          'fu_result'=> $fu_result['fu_result'],
          'account_number'=> $fu_result['account_number'],
          'amount'=> $fu_result['amount'],
          'target_commitment_date'=> $fu_result['target_commitment_date'],
          'img_lkn'=> $url.$fu_result['img_lkn'],
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
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
      } else {
        $pemasar_name = [];
      }
      // print_r($pemasar);
      $marketingActivity = [];
      foreach (MarketingActivity::where('pn', $pn)->where('desc', '!=', 'first')->orwhere('pn_join', $pn)->with('marketing')->with('fu_result')->get() as $activity) {
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

        $fu_result = MarketingActivityFollowup::where('activity_id',$activity->id)->first();
        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'pn_name' => array_key_exists($activity->pn, $pemasar_name) ? $pemasar_name[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'nama' => $activity->marketing->nama,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join,$pemasar_name)? $pemasar_name[$activity->pn_join]: '',
          'notes' => $activity->desc,
          'address' => $activity->address,
          'ownership' => $ownership,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          // 'fu_result'=>MarketingActivityFollowup::where('activity_id',$activity->id)->first()
          'desc'=> $fu_result['desc'],
          'fu_result'=> $fu_result['fu_result'],
          'account_number'=> $fu_result['account_number'],
          'amount'=> $fu_result['amount'],
          'target_commitment_date'=> $fu_result['target_commitment_date'],
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
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store_phoneDuration(Request $request)
    {
      $data['pn'] = $request->header('pn');
      if ($request->has('cif')) {
        $data['cif'] = $request['cif'];
      } else {
        $data['nik'] = $request['nik'];
      }
      $data['phone_number'] = $request['phone_number'];
      $data['duration'] = $request['duration'];
      $data['date'] = $request['date'];
      $data['name'] = $request['name'];

      $save = PhoneDuration::create($data);

      if ($save) {
          return response()->success([
              'message' => 'Data Activity Phone duration berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Activity Phone duration Tidak Dapat Ditambah.',
      ], 500);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function phoneDuration_byCustomer(Request $request)
    {
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);
      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
      } else {
        $pemasar_name = [];
      }

      $listPhoneActivity = PhoneDuration::getListDurationByCustomer($request)->get();
      $data = [];

      foreach ($listPhoneActivity as $key => $value) {
        $data []= [
          'pn' => $value->pn,
          'officer' =>array_key_exists($value->pn, $pemasar_name) ? $pemasar_name[$value->pn]:'',
          'nik' => $value->nik,
          'cif' => $value->cif,
          'phone_number' => $value->phone_number,
          'duration' => $value->duration,
          'date' => $value->date,
          'name' => $value->name
        ];
      }
      return response()->success( [
          'message' => 'Sukses get list Phone Activity by Customer',
          'contents' => $data
        ]);
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
          $followUp['account_number'] = (is_array($request['account_number']) || is_object($request['account_number'])) ?   implode (", ", $request['account_number']) : $request['account_number'] ;
          $followUp['amount'] = $request['amount'];
          $followUp['target_commitment_date'] = $request['target_commitment_date'];
        }

        $followUp['longitude'] = $request['longitude'];
        $followUp['latitude'] = $request['latitude'];

        if ($request->hasFile('img_lkn')) {
          $image = $request->file('img_lkn');
          $name = md5($image->getClientOriginalName()).'-'.time().'.'.$image->getClientOriginalExtension();
          $destinationPath = public_path('uploads/crm/lkn');
          $image->move($destinationPath, $name);
          $followUp['img_lkn'] = $name;
          $request['img_filename'] = $name;
        }
        $save = MarketingActivityFollowup::create($followUp);


        $updateMarketingStatus['status'] = $request['fu_result'];

        $marketing->update($updateMarketingStatus);

        if ($marketing->ref_id != null) {
          $referral = Referral::where('ref_id', $marketing->ref_id);
          $referral_update['status'] = $request['fu_result'];
          if($request['fu_result']=='Done' || $request['fu_result']=='done') {
            $referral_update['point'] = '2';
          }
          $referral->update($referral_update);
        }

        if ($save) {
            return response()->success([
                'message' => 'Data Tindakan berhasil ditambah.',
                'contents' => collect($save),
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
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $marketingActivity = [];
      foreach (MarketingActivity::where('desc', '!=', 'first')->whereIn('pn', $list_pn)->with('marketing')->with('fu_result')->get() as $activity) {
        $fu_result = MarketingActivityFollowup::where('activity_id',$activity->id)->first();
        $rescheduled = rescheduleActivity::where('activity_id',$activity->id)->count();
        $followUp = MarketingActivityFollowup::where('activity_id',$activity->id)->count();
        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'pn_name' => array_key_exists($activity->pn, $pemasar_name) ? $pemasar_name[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'nama'=>$activity->marketing->nama,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join, $pemasar_name) ? $pemasar_name[$activity->pn_join]:'',
          'notes' => $activity->desc,
          'address' => $activity->address,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          // 'fu_result'=>MarketingActivityFollowup::where('activity_id',$activity->id)->first()
          'desc'=> $fu_result['desc'],
          'fu_result'=> $fu_result['fu_result'],
          'account_number'=> $fu_result['account_number'],
          'amount'=> $fu_result['amount'],
          'target_commitment_date'=> $fu_result['target_commitment_date'],
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
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);
      $marketing_id = $request['marketing_id'];

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        $list_pn =[];
      }

      $marketingActivity = [];
      foreach (MarketingActivity::where('marketing_id', $marketing_id)->with('marketing')->with('fu_result')->get() as $activity) {
                $fu_result = MarketingActivityFollowup::where('activity_id',$activity->id)->first();
        $rescheduled = rescheduleActivity::where('activity_id',$activity->id)->count();
        $followUp = MarketingActivityFollowup::where('activity_id',$activity->id)->count();
        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'pn_name' => array_key_exists($activity->pn, $pemasar_name) ? $pemasar_name[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'nama' => $activity->marketing->nama,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join, $pemasar_name) ? $pemasar_name[$activity->pn_join]:'',
          'notes' => $activity->desc,
          'address' => $activity->address,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          // 'fu_result'=>MarketingActivityFollowup::where('activity_id',$activity->id)->first()
          'desc'=> $fu_result['desc'],
          'fu_result'=> $fu_result['fu_result'],
          'account_number'=> $fu_result['account_number'],
          'amount'=> $fu_result['amount'],
          'target_commitment_date'=> $fu_result['target_commitment_date'],
          ];
      }

       // return array_column($marketingActivity,'pn');die();
      return response()->success( [
          'message' => 'Success get list Activity by Marketing',
          'contents' => $marketingActivity
        ]);

    }

    public function activity_by_customer(Request $request)
    {
      $pn = $request->header('pn');
      $branch = $request->header('branch');
      $auth = $request->header('Authorization');
      $pemasar = $this->pemasar($pn,$branch,$auth);
      $marketing_id = $request['marketing_id'];

      if ($pemasar != null) {
        $pemasar_name = array_column($pemasar, 'SNAME','PERNR' );
        // $list_pn = array_column($pemasar, 'PERNR');
      } else {
        $pemasar_name = [];
        // $list_pn =[];
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
      foreach (MarketingActivity::whereIn('marketing_id', $list)->orderBy('created_at', 'desc')->with('marketing')->with('fu_result')->get() as $activity) {
        $fu_result = MarketingActivityFollowup::where('activity_id',$activity->id)->first();
        $rescheduled = rescheduleActivity::where('activity_id',$activity->id)->count();
        $followUp = MarketingActivityFollowup::where('activity_id',$activity->id)->count();
        $customerActivity[] = [
          'id' => $activity->id,
          'pn' => $activity->pn,
          'pn_name' => array_key_exists($activity->pn, $pemasar_name) ? $pemasar_name[$activity->pn]:'',
          'object_activity' => $activity->object_activity,
          'action_activity' => $activity->action_activity,
          'start_date' => date('Y-m-d', strtotime($activity->start_date)),
          'end_date' => date('Y-m-d', strtotime($activity->end_date)),
          'start_time' => date('H:i', strtotime($activity->start_date)),
          'end_time' => date('H:i', strtotime($activity->end_date)),
          'longitude' => $activity->longitude,
          'latitude' => $activity->latitude,
          'marketing_id' => $activity->marketing_id,
          'nama' => $activity->marketing->nama,
          'pn_join' => $activity->pn_join,
          'join_name' => array_key_exists($activity->pn_join, $pemasar_name) ? $pemasar_name[$activity->pn_join]:'',
          'notes' => $activity->desc,
          'address' => $activity->address,
          'followup'=> $followUp,
          'rescheduled'=> $rescheduled,
          // 'fu_result'=>MarketingActivityFollowup::where('activity_id',$activity->id)->first()
          'desc'=> $fu_result['desc'],
          'fu_result'=> $fu_result['fu_result'],
          'account_number'=> $fu_result['account_number'],
          'amount'=> $fu_result['amount'],
          'target_commitment_date'=> $fu_result['target_commitment_date'],
        ];
      }
      // return $list;
      // dd($list);die();
      return response()->success( [
          'message' => 'Success get marketing Activity by Customer',
          'contents' => $customerActivity
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
