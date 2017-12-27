<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
      $marketingActivity = [];
      foreach (MarketingActivity::where('pn', $pn)->get() as $activity) {
        $marketingActivity[]= [
          'id' => $activity->id,
          'pn' => $activity->pn,
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
          'desc' => $activity->desc,
          'address' => $activity->address,
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
      $data['pn_join'] = $request['pn_join'];
      $data['desc'] = $request['desc'];

      $save = MarketingActivity::create($data);

      if ($save) {
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

        $updateMarketingStatus['status'] = $request['fu_result'];

        $save = MarketingActivityFollowup::create($followUp);
        $marketing->update($updateMarketingStatus);

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
}
