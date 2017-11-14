<?php

namespace App\Http\Controllers\API\v1\Int;

use App\Events\Developer\CreateOrUpdate;
use App\Helpers\Traits\ManageUserTrait;
use App\Http\Controllers\API\v1\Eks\PropertyController;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Developer\CreateRequest;
use App\Http\Requests\API\v1\Developer\UpdateRequest;
use App\Models\Developer;
use App\Models\User;
use Illuminate\Http\Request;

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
        $developers->transform(function ($developer) {
            $temp = $developer->toArray();
            $temp['image'] = $developer->image ? url("uploads/avatars/{$developer->image}") : asset('img/noimage.jpg');
            return $temp;
        });
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
        $user = $this->storeUpdate($request, []);
        if ($user) return $this->redirectTo($user, 'disimpan');
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
        if ($user) return $this->redirectTo($user, 'dirubah');
        return response()->error(['message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function properties(Request $request, User $model)
    {
        return app(PropertyController::class)->index($request, $model->id);
    }

    /**
     * Return if this store or update success
     *
     * @param  array $user   [description]
     * @param  string $method [description]
     * @return \Illuminate\Http\Response
     */
    public function redirectTo($user, $method)
    {
        event( new CreateOrUpdate ( $user['developer'] ) );
        return response()->success([
            'message'  => "Data developer berhasil {$method}.",
            'contents' => array_except($user, 'developer')
        ]);
    }
}
