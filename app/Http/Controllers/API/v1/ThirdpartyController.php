<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\ThirdParty\ThirdPartyRequest;
use App\Http\Requests\API\v1\ThirdParty\UpdateThirdPartyRequest;
use App\Models\ThirdParty;
use Illuminate\Http\Request;

class ThirdpartyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $thirdparty = ThirdParty::with('city')->paginate($limit);
        return response()->success([
            'contents' => $thirdparty,
        ], 200);
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
    public function store(ThirdPartyRequest $request)
    {
        $save = ThirdParty::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat_perusahaan' => $request->alamat_perusahaan,
            'city_id' => $request->city_id,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        if ($save) {
            return response()->success([
                'message' => 'Data pihak ke-3 berhasil ditambah.',
                'contents' => $request->all(),
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
    public function update(UpdateThirdPartyRequest $request, $id)
    {
        $update = ThirdParty::find($id);
        $update->nama_perusahaan = $request->nama_perusahaan;
        $update->alamat_perusahaan = $request->alamat_perusahaan;
        $update->city_id = $request->city_id;
        $update->no_telp = $request->no_telp;
        $update->email = $request->email;
        $update->status = $request->status;

        $update->save();
        if ($update) {
            return response()->success([
                'message' => 'Data pihak ke-3 berhasil Diupdate.',
                'contents' => $request->all(),
            ], 200);
        }

        return response()->error([
            'message' => 'Data pihak ke-3 Tidak Dapat Diupdate.',
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
