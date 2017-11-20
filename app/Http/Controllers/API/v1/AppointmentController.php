<?php

namespace App\Http\Controllers\API\v1;

use Sentinel;
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
        $eks = $request->is('api/v1/eks/schedule');
        if ($eks) {
            $data = $data->customer($request->user()->id, $request->month, $request->year)->get();
        } else {
          $data = $data->ao($request->header('pn'),  $request->month, $request->year)->paginate(300);
        }
        if ($data) {
            if (count($data) > 0) {
              if ($eks) {
                $data = [
                  'data' => $data
                ];
              }
              return response()->success([
                  'contents' => $data
              ], 200);
            } else {
              return response()->success([
                  'contents' => [
                    'data' => []
                  ],
              ], 200);
            }
        }

        return response()->error([
            'message' => 'Data schedule User Tidak ada.',
            'contents' => [
              'data' => []
            ]
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
        $postTaken = ['title', 'appointment_date', 'user_id', 'ao_id', 'eform_id', 'ref_number', 'address', 'latitude', 'longitude', 'guest_name', 'desc', 'status'];
        $save = Appointment::create($request->only($postTaken));
        if ($save) {
            return response()->success([
                'message' => 'Data schedule User berhasil ditambah.',
                'contents' => collect($save)->merge($request->all()),
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
    public function show(Appointment $appointment, Request $request, $id)
    {
      $appointment = $appointment->visibleColumn()
        ->withEform()
        ->customer($request->user()->id, $request->month, $request->year)
        ->where((new Appointment)->getTable() . '.id', $id)
        ->first();

        return response()->success([
          'contents' => $appointment ? $appointment : (object)[]
        ]);
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
                'contents' => Appointment::visibleColumn()->withEform()->find($id),
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
