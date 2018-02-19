<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\Crm\Marketing\CreateRequest;
// use App\Http\Request\API\v1\Crm\Marketing\UpdateRequest;
use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingNote;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\rescheduleActivity;
use App\Models\Crm\MarketingActivityFollowup;
use App\Models\Crm\ActivityType;
use App\Models\Crm\ProductType;
use App\Models\Crm\Status;
use App\Models\User;

use RestwsHc;

class MarketingController extends Controller
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

      $marketings = [];
      foreach (Marketing::where('pn',$pn)->with('activity')->get() as $marketing) {

        $marketingActivity = [];

        foreach (MarketingActivity::where('marketing_id', $marketing->id)->with('marketing')->get() as $activity) {
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

          $pnName = array_key_exists($activity->pn, $pemasar_name) ? $pemasar_name[$activity->pn]:'';

          $marketingActivity[]= [
            'id' => $activity->id,
            'pn' => $activity->pn,
            'marketing_activity_type' => $activity->marketing->activity_type,
            'pn_name' => $pnName,
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
            'join_name' => array_key_exists($activity->pn_join,$pemasar_name)? $pemasar_name[$activity->pn_join]: '',
            'desc' => $activity->desc,
            'address' => $activity->address,
            'ownership' => $ownership,
            'followup'=> $followUp,
            'rescheduled'=> $rescheduled,
            ];

        }

        $marketings[]=[
          'id'=> $marketing->id,
          'pn'=> $marketing->pn,
          'pn_name' => $pnName,
          'product_type'=> $marketing->product_type,
          'activity_type'=> $marketing->activity_type,
          'target'=> $marketing->target,
          'account_id'=> $marketing->account_id,
          'nama'=> $marketing->nama,
          'nik'=> $marketing->nik,
          'cif'=> $marketing->cif,
          'status'=> $marketing->status,
          'ref_id'=> $marketing->ref_id,
          'activities'=>$marketingActivity,
          'target_closing_date'=> date('Y-m-d', strtotime($marketing->target_closing_date)),
          'created_at' => date('m-Y', strtotime(str_replace('/', '-', $marketing->created_at)))
        ];

      }
      return response()->success( [
          'message' => 'Sukses get Marketing',
          'contents' => $marketings
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function by_branch(Request $request)
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

      $ext_nul = [
        1=>'0',
        2=>'00',
        3=>'000',
        4=>'0000'
      ];

      $marketings = [];
      foreach (Marketing::whereIn('pn',$list_pn)->get() as $marketing) {
        $marketingActivity = [];
        foreach (MarketingActivity::where('marketing_id', $marketing->id)->with('marketing')->get() as $activity) {
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
            'pn_join' => $activity->pn_join,
            'join_name' => array_key_exists($activity->pn_join,$pemasar_name)? $pemasar_name[$activity->pn_join]: '',
            'desc' => $activity->desc,
            'address' => $activity->address,
            'ownership' => $ownership,
            'followup'=> $followUp,
            'rescheduled'=> $rescheduled,
            ];
        }

        $marketings[]=[
          'id'=> $marketing->id,
          'pn'=> $marketing->pn,
          'pn_name'=> array_key_exists($ext_nul[8-strlen($marketing->pn)].$marketing->pn, $pemasar_name) ? $pemasar_name[$ext_nul[8-strlen($marketing->pn)].$marketing->pn]:'',
          'product_type'=> $marketing->product_type,
          'activity_type'=> $marketing->activity_type,
          'target'=> $marketing->target,
          'account_id'=> $marketing->account_id,
          'nama'=> $marketing->nama,
          'nik'=> $marketing->nik,
          'cif'=> $marketing->cif,
          'status'=> $marketing->status,
          'ref_id'=> $marketing->ref_id,
          'activities'=> $marketingActivity,
          'target_closing_date'=> date('Y-m-d', strtotime($marketing->target_closing_date)),
          'created_at' => date('m-Y', strtotime(str_replace('/', '-', $marketing->created_at)))
        ];
      }
      return response()->success( [
          'message' => 'Sukses get marketing by Branch',
          'contents' => $marketings
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $customers = User::getCustomers( $request )->get();
        $data['product_type'] = ProductType::all();
        $data['activity_type'] = ActivityType::all();
        $data['status'] = Status::all();
        $data['accounts'] = $customers;

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
      $data['branch'] = $request->header('branch');
      $data['product_type'] = $request['product_type'];
      $data['activity_type'] = $request['activity_type'];
      $data['target'] = $request['target'];
      $data['account_id'] = $request['account_id'];
      $data['nama'] = $request['nama'];
      $data['nik'] = ($request['nik'] == "") ? null : $request['nik'];
      $data['cif'] = ($request['cif'] == "") ? null : $request['cif'];
      $data['status'] = $request['status'];
      $data['ref_id'] = $request['ref_id'];
      $data['target_closing_date'] = date('Y-m-d', strtotime($request['target_closing_date']));

      $save = Marketing::create($data);
      if ($save) {
          $this->first_activity($request->header('pn'), $save->id, $request);

          return response()->success([
              'message' => 'Data Marketing berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Marketing Tidak Dapat Ditambah.',
      ], 500);
    }

    public function first_activity($pn, $id, $request)
    {
      $data['pn'] = $pn;
      // $data['pn_name'] = $request['pn_name'];
      $data['object_activity'] = "Create Marketing ".$request['activity_type'];
      $data['action_activity'] = "Create Marketing ".$request['activity_type'];
      $data['start_date'] = date('Y-m-d H:i:s');
      $data['end_date'] = date('Y-m-d H:i:s');

      if(isset($request['longitude']) && isset($request['latitude']) ){
        $data['longitude'] = $request['longitude'];
        $data['latitude'] = $request['latitude'];
      }else{
        $data['longitude'] = 'unset';
        $data['latitude'] = 'unset';
      }

      $data['address'] = 'null';
      $data['marketing_id'] = $id;
      $data['pn_join'] = ($request['pn_join']!='' ? $request['pn_join'] : 'null');
      // $data['join_name'] = $request['join_name'];
      $data['desc'] = 'first';

      $first_activity = MarketingActivity::create($data);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marketing = Marketing::find($id);
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $marketing
          ]);
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
      $data = Marketing::find($id);

      $update['pn'] = $request->header('pn');
      $update['product_type'] = $request['product_type'];
      $update['activity_type'] = $request['activity_type'];
      $update['target'] = $request['target'];
      $update['account_id'] = $request['account_id'];
      $update['nama'] = $request['nama'];
      $update['nik'] = $request['nik'];
      $update['cif'] = $request['cif'];
      $update['status'] = $request['status'];
      $update['target_closing_date'] = $request['target_closing_date'];

      // return $request->only($postTaken);die();
      if($data) {
        $data->update($update);

        return response()->success([
          'message' => 'Data Marketing berhasil diupdate.',
          'contents' => Marketing::find($id),
        ], 201);
      }

      return response()->error([
          'message' => 'Data Marketing Tidak Dapat Diupdate.',
      ], 500);
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

    // Marketing Notes
    public function getNote(Request $request)
    {
      $marketing_id = $request['marketing_id'];
      $marketing_note = MarketingNote::where('marketing_id', $marketing_id)->get();
      return response()->success( [
          'message' => 'Sukses get List Marketing Note',
          'contents' => $marketing_note
        ]);
    }

    public function store_note(Request $request)
    {
      $data['marketing_id'] = $request['marketing_id'];
      $data['pn'] = $request->header('pn');
      $data['pn_name'] = $request->header('name');
      $data['note'] = $request['note'];

      $save = MarketingNote::create($data);
      if ($save) {
          return response()->success([
              'message' => 'Marketing Notes berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Marketing Notes Tidak Dapat Ditambah.',
      ], 500);
    }

}
