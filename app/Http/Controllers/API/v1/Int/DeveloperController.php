<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Developer\CreateRequest;
use App\Http\Requests\API\v1\Developer\UpdateRequest;
use App\Http\Controllers\API\v1\PropertyTypeController;
use App\Helpers\Traits\ManageUserTrait;
use App\Models\Developer;
use App\Models\User;

class DeveloperController extends Controller
{
    use ManageUserTrait;

    /**
     * {@inheritDoc}
     */
    protected $activedFor = 'developer';

    /**
     * {@inheritDoc}
     */
    protected $relation = 'developer';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('has.user.dev', ['except' => ['index', 'store'] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $developers = Developer::getLists($request)->paginate($limit);
        return response()->success(['contents' => $developers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $request->merge(['created_by' => $request->user()->id]);
        $user = $this->storeUpdate($request, []);
        if ($user) return response()->success(['message' => 'Data developer berhasil disimpan.', 'contents' => $user]);
        return response()->error(['message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $developer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, User $developer)
    {
        $user = $this->storeUpdate($request, $developer);
        if ($user) return response()->success(['message' => 'Data developer berhasil dirubah.', 'contents' => $user]);
        return response()->error(['message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function properties(Request $request, User $model)
    {
        return app(PropertyTypeController::class)->index($request, $model->id);
    }
}
