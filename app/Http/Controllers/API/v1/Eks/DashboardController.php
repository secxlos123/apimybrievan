<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
class DashboardController extends Controller
{
    protected $request;
    protected $modelUser;

    public function __construct(Request $request)
    {
        $this->request   = $request;
        $this->modelUser = new User;
    }

    public function dashboard()
    {
    	$params    = $this->request->all();
    	$start 	   = (isset($params['start'])) ? $params['start'] : '';
    	$end 	   = (isset($params['end'])) ? $params['end'] : '';
    	$userList  = $this->modelUser->getListUserProperties($start, $end);
    	$chartData = "";
        return response()->success([
            'contents' => [
                'user_list' => $userList,
                'chart'		=> $chartData,
            ]
        ]);
    }
}
