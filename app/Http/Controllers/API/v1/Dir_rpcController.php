<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\DirrpcRequest;
use App\Models\User;
use App\Models\DIRRPC;
use App\Models\DIRRPC_DETAIL;
use App\Models\UserServices;
use DB;

class Dir_rpcController extends Controller
{
    public function __construct(User $user, UserServices $userservices)
    {
        $this->user = $user;
        $this->userservices = $userservices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        \Log::info($request->all());
        $limit = $request->input( 'limit' ) ?: 10;
		if($request->act=='search'){
			$newDir = DIRRPC::filter( $request )->paginate( $limit );
		}elseif($request->act=='maintance'){
			$newDir = DIRRPC_DETAIL::filter( $request )->paginate( $limit );
		}
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newDir
        ], 200 );
    }
}
