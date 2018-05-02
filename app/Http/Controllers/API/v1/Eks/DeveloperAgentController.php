<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserDeveloper;
use App\Models\User;
use JWTAuth;
use App\Helpers\Traits\ManageUserTrait;
use App\Http\Requests\API\v1\DeveloperAgent\CreateRequest;
use App\Http\Requests\API\v1\DeveloperAgent\UpdateRequest;
use Activation;
use App\Events\Customer\CustomerRegistered;
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

        $email = strtolower($request->input('email'));
        $password = $this->randomPassword(8,"lower_case,upper_case,numbers");
        $request->merge(['email' => $email , 'password' => bcrypt($password)]);
        $user = User::create($request->all());
        $user->history()->create($request->only('password'));
        $activation = Activation::create($user);
        Activation::complete($user, $activation->code);
        event(new CustomerRegistered($user, $password,$request->input('role_id')));
        $token = JWTAuth::fromUser( $user );
         //\Log::info($token);
        $user->roles()->sync($request->input('role_id'));

        if ($user) {
            $saveData = new UserDeveloper();
            $saveData->user_id = $user->id;
            $saveData->birth_date = $request->birth_date;
            $saveData->join_date = $request->join_date;
            $saveData->admin_developer_id = $request->user()->id;
            $saveData->bound_project = $request->bound_project;
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
            $saveData->birth_date = date("Y-m-d", strtotime($request->birth_date));
            $saveData->join_date = date("Y-m-d", strtotime($request->join_date));
            $saveData->bound_project = $request->bound_project;
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

        if ($user->is_banned) {
            $user->update( ['is_banned' => false] );

             return response()->success([
            'message' => 'Unbanned Agent Developer Berhasil !',
            'contents' => $user,
            ], 200);

        } else {
            $user->update( ['is_banned' => true] );

             return response()->success([
            'message' => 'Banned Agent Developer Berhasil !',
            'contents' => $user,
            ], 200);

        }
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

    /**
     * Generate Random Password
     * @param  [type] $length     [description]
     * @param  [type] $characters [description]
     * @return [type]             [description]
     */
    public function randomPassword($length,$characters) {
        // $length - the length of the generated password
        // $characters - types of characters to be used in the password
        // define variables used within the function
        $symbols = array();
        $passwords = array();
        $used_symbols = '';
        $pass = '';
        // an array of different character types
        $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
        $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols["numbers"] = '1234567890';
        $symbols["special_symbols"] = '!?~@#-_+<>[]{}';
        $characters = explode(",",$characters); // get characters types to be used for the passsword
        foreach ($characters as $key=>$value) {
            $used_symbols .= $symbols[$value]; // build a string with all characters
        }
        $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1
            for ($i = 0; $i < $length; $i++) {
                $n = rand(0, $symbols_length); // get a random character from the string with all characters
                $pass .= $used_symbols[$n]; // add the character to the password string
            }

        return $pass; // return the generated password
    }
}
