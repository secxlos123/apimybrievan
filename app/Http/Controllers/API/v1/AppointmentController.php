<?php

namespace App\Http\Controllers\API\v1;

use Sentinel;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Appointment\StatusRequest;
use App\Http\Requests\API\v1\Appointment\CreateRequest;
use App\Http\Requests\API\v1\Appointment\UpdateRequest;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserServices;
use App\Models\UserNotification;
use App\Notifications\NewSchedulerCustomer;
use App\Notifications\UpdateSchedulerCustomer;
use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use FCM;


class AppointmentController extends Controller
{
    public function __construct(User $user, UserServices $userservices, UserNotification $userNotification, Appointment $appointment)
    {
        $this->userServices = new UserServices;
        $this->user = $user;
        $this->userservices = $userservices;
        $this->userNotification = $userNotification;
        $this->appointment = $appointment;
    }

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
          if ($request->has('month') || $request->has('year')) {
              $data->customer($request->user()->id, $request->month, $request->year);
            }
          else
            {
              $data->where('eforms.user_id',$request->user()->id);
            }
            $data = $data->get();
        } else {
            $user_login = \RestwsHc::getUser();
            if ( $user_login['role'] === 'ao' ) {
                $data = $data->ao(
                    $request->header('pn')
                    , $request->month
                    , $request->year
                )->paginate(300);
            } else {
                $data = $data->pinca(
                    $user_login['branch_id']
                    , $request->month
                    , $request->year
                )->paginate(300);
            }
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
        \Log::info("===========================POST Schedule=======================");
        \Log::info($request->only($postTaken));
        $save = Appointment::create($request->only($postTaken));

        if ($save) {
            $typeModule = getTypeModule(Appointment::class);
            notificationIsRead($save->eform_id, $typeModule);

            $usersModel = User::FindOrFail($save->user_id);     /*send notification*/
            $usersModel->notify(new NewSchedulerCustomer($save));

            // Push Notification
            $credentials = [
                'data'  => [
                    'user_id'    => $request->user_id,
                    'ref_number' => $request->ref_number,
                    'eform_id'   => $request->eform_id
                ],
            ];

            // Call the helper of push notification function
            pushNotification($credentials, 'createSchedule');

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
        ->withEform();
        if ($request->has('month') || $request->has('year')) {
            $appointment->customer($request->user()->id, $request->month, $request->year);
        }else
        {
        $appointment->where('eforms.user_id',$request->user()->id);
        }
        $appointment = $appointment->where((new Appointment)->getTable() . '.id', $id)
        ->first();

        return response()->success([
          'contents' => $appointment ? $appointment : (object)[]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function detail(Appointment $appointment,Request $request, $type, $id)
    {
        $appointment = $appointment->visibleColumn()
        ->withEform()
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
            $Update = $data->update($request->all());
            $data->save();
            $typeModule = getTypeModule(Appointment::class);
            notificationIsRead($id, $typeModule);

            $usersModel = User::FindOrFail($data->user_id);     /*send notification*/
            $usersModel->notify(new UpdateSchedulerCustomer($data));

            if($type == 'int'){
                $role = 'ao';
            }else{
                $role = 'customer';
            }

            $credentials = [
                'data'  => $data,
                'role'  => $role,
            ];

            // Call the helper of push notification function
            pushNotification($credentials, 'updateSchedule');

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
