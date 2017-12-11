<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\Crm\Marketing\CreateRequest;
// use App\Http\Request\API\v1\Crm\Marketing\UpdateRequest;
use App\Models\Crm\Marketing;
use App\Models\Crm\ActivityType;
use App\Models\Crm\ProductType;
use App\Models\Crm\Status;
// use App\Models\User;

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
      $marketings = Marketing::where('pn',$pn)->get();
      return response()->success( [
          'message' => 'Sukses',
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
        $data['np'] = $request->header('pn');
        $data['product_type'] = ProductType::all();
        $data['activity_type'] = ActivityType::all();
        $data['status'] = Status::all();

        for ($i=1; $i < 50 ; $i++) {
          $data['account'][] = [
            $i => 'Nasabah - '.$i
          ];
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
      $postTaken = [
        'pn',
        'product_type',
        'activity_type',
        'target',
        'account_id',
        'status',
        'target_closing_date'
      ];

      // return $request->only($postTaken);die();

      $save = Marketing::create($request->only($postTaken));
      if ($save) {
          return response()->success([
              'message' => 'Data Marketing berhasil ditambah.',
              'contents' => collect($save)->merge($request->all()),
          ], 201);
      }

      return response()->error([
          'message' => 'Data Marketing Tidak Dapat Ditambah.',
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

      $postTaken = [
        'pn',
        'product_type',
        'activity_type',
        'target',
        'account',
        'status',
        'target_closing_date'
      ];

      return $request->only($postTaken);die();
      if($data) {
        $data->update($request->only($postTaken));

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
}
