<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\ThirdParty\CreateRequest;
use App\Http\Requests\API\v1\ThirdParty\UpdateRequest;
use App\Models\ThirdParty;
use App\Helpers\Traits\ManageUserTrait;
use Illuminate\Http\Request;
use App\Models\User;
use DB;

class ThirdpartyController extends Controller
{
    use ManageUserTrait;

    /**
     * {@inheritDoc}
     */
    protected $activedFor = 'others';

    /**
     * {@inheritDoc}
     */
    protected $relation = 'thirdparty';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $thirdparty = ThirdParty::GetLists($request)->paginate($limit);
        if (count($thirdparty) !=0) {
            return response()->success([
            'contents' => $thirdparty,
        ], 200);
        }
        return response()->error([
            'message' => 'Data pihak ke-3 Tidak Ada.',
        ], 500);
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
    public function store(CreateRequest $request)
    {

        $save = $this->storeUpdate($request, []);
        if ($save) {
            return response()->success([
                'message' => 'Data pihak ke-3 berhasil ditambah.',
                'contents' => $save,
            ], 201);
        }

        return response()->error([
            'message' => 'Data pihak ke-3 Tidak Dapat Ditambah.',
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
        $detail = ThirdParty::GetDetail($id)->get();
        if (count($detail)!= 0) {
            return response()->success([
                'contents' => $detail,
            ], 200);
        }

        return response()->error([
            'message' => 'Data pihak ke-3 Tidak Ada.',
        ], 500);
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
    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try{
        $update = ThirdParty::find($id);
        $update->name = $request->name;
        $update->address = $request->address;
        $update->city_id = $request->city_id;
        $update->phone_number = $request->phone_number;
        if ($update) {
        $user = User::find($id);
        list($first_name, $last_name) = name_separator($request->name);
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->save();
        $update->save();
        }else
        {
             \DB::rollback();
            return response()->error([
            'message' => 'Data pihak ke-3 Tidak Dapat Diupdate.',
        ], 500);
        }
        if ($update && $user) {
            return response()->success([
                'message' => 'Data pihak ke-3 berhasil Diupdate.',
                'contents' => $update,
            ], 200);
        }

        }catch (\Exception $e) {
            \DB::rollback();
            return response()->error([
            'message' => 'Data pihak ke-3 Tidak Dapat Diupdate.',
        ], 500);
        }
        

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = ThirdParty::destroy($id);

        if ($delete) {
            return response()->success([
                'message' => 'Data pihak ke-3 berhasil Dihapus.',
            ], 200);
        }

        return response()->error([
            'message' => 'Data pihak ke-3 Tidak Dapat Dihapus.',
        ], 500);
    }
}
