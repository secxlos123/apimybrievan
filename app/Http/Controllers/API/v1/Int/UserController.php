<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\User\CreateRequest;
use App\Models\User;
use Activation;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $user = $this->storeUpdate($request, []);
        if ($user) return response()->success(['data' => compact('user') ]);
        return response()->error(['data' => (object) null, 'message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = $this->responseUser($user);
        return response()->success(['data' => compact('user')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user = $this->storeUpdate($request, $user);
        if ($user) return response()->success(['data' => compact('user') ]);
        return response()->error(['data' => (object) null, 'message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Store update user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array|\App\Models\User  $user
     * @return array
     */
    private function storeUpdate(Request $request, $user)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image')->storeAs('avatar', 'public');
            $request->merge(['image' => $image]);
        }

        \DB::beginTransaction();
        try {
            if ( ! $user instanceof User ) {
                $user = User::create($request->input());
                $activation = Activation::create($user);
                Activation::complete($user, $activation->code);
            } else {
                $user->update($request->input());
            }

            $user->roles()->sync($request->input('role_id'));
            $detail = $user->detail()->updateOrCreate(['user_id' => $user->id], $request->input());
            \DB::commit();
            return $this->responseUser($user);
        } catch (\Exception $e) {
            \DB::rollback();
            return false;
        }
    }

    /**
     * Store update user.
     *
     * @param  array|\App\Models\User  $user
     * @return array
     */
    private function responseUser($user)
    {
        $user->load(['roles', 'detail']);

        return [
            'id' => $user->id,
            'fullname' => $user->fullname,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'mobile_phone' => $user->mobile_phone,
            'gender' => $user->gender,
            'office_id' => $user->detail->office_id,
            'office' => $user->detail->office->name,
            'nip' => $user->detail->nip,
            'position' => $user->detail->position,
            'role_id' => $user->roles->first()->id,
            'role' => $user->roles->first()->name,
        ];
    }
}
