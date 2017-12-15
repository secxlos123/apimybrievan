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
      $marketingActivity = MarketingActivity::where('pn',$pn)->get();
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
      $data['longitude'] = $request['longitude'];
      $data['latitude'] = $request['latitude'];
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
     public function reSchedule(Request $request, $id)
     {
       $marketingActivity = MarketingActivity::find($id);
       $reSchedule['activity_id'] = $id;
       $reSchedule['desc'] = $request['desc'];
       $reSchedule['reason'] = $request['reason'];
       $reSchedule['origin_date'] = $marketingActivity->start_date;
       $reSchedule['reschedule_date'] = date('Y-m-d H:i:s', strtotime($request['start_date'].$request['start_time']));

       $updateMarketing['start_date'] = $reSchedule['reschedule_date'];
       $updateMarketing['end_date'] = date('Y-m-d H:i:s', strtotime($request['end_date'].$request['end_time']));

       $save = rescheduleActivity::create($reSchedule);

       if($save) {
         $marketingActivity->update($updateMarketing);

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
      public function storeFollowUp(Request $request, $id)
      {
        $marketingActivity = MarketingActivity::find($id);
        $marketing_id = $marketingActivity->marketing_id;
        $marketing = Marketing::find($marketing_id);

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
        if ($save) {
            $marketing->update($updateMarketingStatus);
            return response()->success([
                'message' => 'Data Tindakan berhasil ditambah.',
                'contents' => collect($save)->merge($request->all()),
            ], 201);
        }

        return response()->error([
            'message' => 'Data Marketing Tidak Dapat Ditambah.',
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
