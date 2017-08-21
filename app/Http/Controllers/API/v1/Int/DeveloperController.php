<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Developer\CreateRequest;
use App\Models\Developer;
use App\Models\User;

class DeveloperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $developers = Developer::getLists($request)->paginate($limit);
        return response()->success(['developers' => $developers]);
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
        if ($user) return response()->success(['message' => 'Data developer berhasil dirubah.', 'data' => $user]);
        return response()->error(['data' => (object) null, 'message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $developer
     * @return \Illuminate\Http\Response
     */
    public function show(User $developer)
    {
        $developer = $this->responseUser($developer);
        return response()->success(['data' => $developer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $developer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $developer)
    {
        $user = $this->storeUpdate($request, $developer);
        if ($user) return response()->success(['message' => 'Data developer berhasil dirubah.', 'data' => $user]);
        return response()->error(['data' => (object) null, 'message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Store update user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array|\App\Models\User  $user
     * @return array
     */
    private function storeUpdate($request, $user)
    {
        \DB::beginTransaction();
        try {
            $user      = User::createOrUpdate($request, $user);
            $developer = $user->developer()->updateOrCreate(['user_id' => $user->id], $request->input());
            \DB::commit();
            return $this->responseUser($user);
        } catch (\Exception $e) {
            \DB::rollback();
            return false;
        }
    }

    /**
     * Prepare for response user.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function responseUser($user)
    {
        $user->load(['roles', 'developer']);

        return [
            'id' => $user->id,
            'fullname' => $user->fullname,
            'email' => $user->email,
            'phone' => $user->phone,
            'mobile_phone' => $user->mobile_phone,
            'is_actived' => $user->is_actived,
            'image' => $user->image,
            'role_id' => $user->roles->first()->id,
            'role_name' => $user->roles->first()->name,
            'role_slug' => $user->roles->first()->slug,
        ];
    }
}
