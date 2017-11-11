<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserDeveloper;
use App\Models\User;
use App\Helpers\Traits\ManageUserTrait;
use App\Http\Requests\API\v1\DeveloperAgent\CreateRequest;
use App\Http\Requests\API\v1\DeveloperAgent\UpdateRequest;
use Activation;
use App\Jobs\SendPasswordEmail;
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
        \log::info('dev = '.$developers);
        // $developers->transform(function ($developer) {
        //     $temp = $developer->toArray();
        //     $temp['image'] = $developer->image ? url("uploads/avatars/{$developer->image}") : asset('img/noimage.jpg');
        //     return $temp;
        // });

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
        $role_id = \Sentinel::findRoleBySlug('developer-sales')->id;
        list($first_name, $last_name) = name_separator($request->input('name'));
        $request->merge( compact( 'role_id', 'first_name', 'last_name','mobile_phone' ) );

        $password = str_random(8);
        $request->merge(['password' => bcrypt($password)]);
        $user = User::create($request->all());
        $activation = Activation::create($user);
        Activation::complete($user, $activation->code);
        $email = dispatch(new SendPasswordEmail($user, $password, 'registered'));
        \Log::info($email);
        $user->roles()->sync($request->input('role_id'));

        if ($user) {
            $saveData = new UserDeveloper();
            $saveData->user_id = $user->id;
            $saveData->birth_date = $request->birth_date;
            $saveData->join_date = $request->join_date;
            $saveData->admin_developer_id = $request->user()->id;
            $saveData->save();
            return response()->success([
                'message' => 'Data agent developer berhasil ditambah.',
                'contents' => $user,
            ], 201);
        }

        return response()->error([
            'message' => 'Data agent developer Tidak Dapat Ditambah.',
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
        $data = User::with('userdeveloper')->where('id',$id)->first();
       
       if ($data->userdeveloper) {
           return response()->success([
            'contents' => $data
        ],200);
       }

        return response()->error([
            'message' => 'Id agent developer Tidak Valid.',
        ], 500);
        
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
        $user = User::findOrFail($id);
        $user->update($request->all());
        if ($user) {
            $saveData = UserDeveloper::where('user_id', $user->id)->first();
            $saveData->birth_date = $request->birth_date;
            $saveData->join_date = $request->join_date;
            $saveData->save();
            return response()->success([
                'message' => 'Data agent developer berhasil diubah.',
                'contents' => $user,
            ], 201);
        }

        return response()->error([
            'message' => 'Data agent developer Tidak Dapat diubah.',
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
        //
    }

    /**
    * Update Data Developer Agent Banned
    * @param int $id
    * @return \Illuminate\Http\Response
    */
    public function banned(Request $request, $id)
    {
        $user = User::findOrFail($id);
     
        $user->update($request->all());
        return response()->success([
            'message' => 'Data Activated Agent Developer ini Berhasil update',
            'contents' => $user,
            ], 201);
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
