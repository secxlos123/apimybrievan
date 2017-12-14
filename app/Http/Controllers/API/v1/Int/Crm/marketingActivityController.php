<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Crm\Marketing;
use App\Models\Crm\MarketingActivity;
use App\Models\Crm\ObjectActivity;
use App\Models\Crm\ActionActivity;

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
      $data['start_date'] = $request['start_date'];
      $data['end_date'] = $request['end_date'];
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
