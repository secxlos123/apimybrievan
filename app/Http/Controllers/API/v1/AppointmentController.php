<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Appointment\StatusRequest;
use App\Http\Requests\API\v1\Appointment\CreateRequest;
use App\Http\Requests\API\v1\Appointment\UpdateRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Appointment::visibleColumn()->withEform();
        if ($request->is('api/v1/eks/schedule')) {
            $data = $data->atTime($request->month, $request->year)->get();
        } else {
          $data = $data->ao($request->header('pn'),  $request->month, $request->year)->paginate(300);
        }

        if ($data) {
            return response()->success([
                'contents' => $data,
            ], 200);
        }

        return response()->error([
            'message' => 'Data schedule User Tidak ada.',
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
    public function store(Request $request)
    {
        $save = Appointment::create($request->all());
        if ($save) {
            return response()->success([
                'message' => 'Data schedule User berhasil ditambah.',
                'contents' => $save,
            ], 201);
        }

        return response()->error([
            'message' => 'Data schedule User Tidak Dapat Ditambah.',
        ], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $type, $id)
    {
        $data = Appointment::find($id);

        if ($data) {
            $Update = Appointment::updateOrCreate(array('id' => $id), $request->all());
            return response()->success([
                'message' => 'Data schedule User berhasil Dirubah.',
                'contents' => $Update,
            ], 201);
        }

        return response()->error([
            'message' => 'Data schedule User Tidak Dapat Dirubah.',
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }

    /**
     * Change Status Schedule
     * @return [type] [description]
     */
    public function status(StatusRequest $request, $type, $id)
    {
        $data = Appointment::find($id);
        if ($data) {
            $appointment = Appointment::updateOrCreate(array('id' => $id), $request->input());
            return response()->success([
                'message' => "Status schedule di Update Menjadi {$status}.",
                'contents' => $appointment,
            ]);
        }

        return response()->error([
            'message' => 'Status schedule User Tidak Dapat Dirubah.',
        ], 500);

    }
}
