<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\User\CreateRequest;
use App\Http\Requests\API\v1\User\UpdateRequest;
use App\Helpers\Traits\ManageUserTrait;
use App\Models\User;

class UserController extends Controller
{
    use ManageUserTrait;

    /**
     * {@inheritDoc}
     */
    protected $activedFor = 'user';

    /**
     * {@inheritDoc}
     */
    protected $relation = 'detail';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('has.user.int', ['except' => ['index', 'store'] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $users = User::getLists($request)->paginate($limit);
        return response()->success(['users' => $users]);
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
        if ($user) return response()->success(['message' => 'Data user berhasil ditambahkan.', 'data' => $user]);
        return response()->error(['data' => (object) null, 'message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, User $user)
    {
        $user = $this->storeUpdate($request, $user);
        if ($user) return response()->success(['message' => 'Data user berhasil dirubah.', 'data' => $user]);
        return response()->error(['data' => (object) null, 'message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Prepare for response user.
     *
     * @param  \App\Models\User  $user
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
            'is_actived' => $user->is_actived,
            'image' => $user->image,
            'office_id' => $user->detail ? $user->detail->office_id : null,
            'office_name' => $user->detail ? $user->detail->office->name : null,
            'nip' => $user->detail ? $user->detail->nip : null,
            'position' => $user->detail ? $user->detail->position : null,
            'role_id' => $user->roles->first()->id,
            'role_name' => $user->roles->first()->name,
            'role_slug' => $user->roles->first()->slug,
        ];
    }
}
