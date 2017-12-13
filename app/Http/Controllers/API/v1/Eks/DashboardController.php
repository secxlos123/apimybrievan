<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
class DashboardController extends Controller
{
    protected $request;
    protected $modelUser;
    protected $modelProperties;

    public function __construct(Request $request)
    {
        $this->request 		   = $request;
        $this->modelUser 	   = new User;
        $this->modelProperties = new Property;
    }

    public function dashboard(Request $request)
    {
        $user_id = $request->user()->id;

    	$params       = $this->request->all();
    	$startList    = (isset($params['startList'])) ? $params['startList'] : '';
    	$endList      = (isset($params['endList'])) ? $params['endList'] : '';
    	$startChart   = (isset($params['startChart'])) ? $params['startChart'] : '';
    	$endChart     = (isset($params['endChart'])) ? $params['endChart'] : '';

    	$userList  	  = $this->modelUser->getListUserProperties($startList, $endList, $user_id);
    	$chartData	  = $this->modelProperties->getChartProperties($startChart, $endChart);
        return response()->success([
            'contents' => [
                'user_list' => $userList,
                'chart'		=> $chartData,
            ]
        ]);
    }
}
