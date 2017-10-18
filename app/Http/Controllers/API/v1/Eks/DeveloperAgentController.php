<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserDeveloper;
use App\Helpers\Traits\ManageUserTrait;
use App\Http\Requests\API\v1\DeveloperAgent\CreateRequest;
use App\Http\Requests\API\v1\DeveloperAgent\UpdateRequest;
use Illuminate\Support\Facades\Log;

class DeveloperAgentController extends Controller
{
    use ManageUserTrait;

    /**
     * {@inheritDoc}
     */
    protected $activedFor = 'developer-sales';

    /**
     * {@inheritDoc}
     */
    protected $relation = 'userdeveloper';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $developers = UserDeveloper::getLists($request)->paginate($limit);
        $developers->transform(function ($developer) {
            $temp = $developer->toArray();
            $temp['image'] = $developer->image ? url("uploads/avatars/{$developer->image}") : asset('img/noimage.jpg');
            return $temp;
        });
        log::info('dev = '.$developers);
        return response()->success(['contents' => $developers]);
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
        $user = $this->storeUpdate($request, []);
        log::info('req = '.$user);
        if ($user) return $this->redirectTo($user, 'disimpan');
        return response()->error(['message' => 'Maaf server sedang gangguan.'], 500);
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
    public function update(UpdateRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
