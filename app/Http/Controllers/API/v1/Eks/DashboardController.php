<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Models\EForm;
class DashboardController extends Controller
{
    protected $request;
    protected $modelDeveloper;
    protected $modelProperties;

    public function __construct(Request $request)
    {
        $this->request 		   = $request;
        $this->modelDeveloper  = new Developer;
        $this->modelEforms     = new EForm;
    }

    public function dashboard(Request $request)
    {
        $user_id = $request->user();
        $user_id = $user_id->id;

    	$params       = $this->request->all();
    	$startList    = (isset($params['startList'])) ? $params['startList'] : '';
    	$endList      = (isset($params['endList'])) ? $params['endList'] : '';
    	$startChart   = (isset($params['startChart'])) ? $params['startChart'] : '';
    	$endChart     = (isset($params['endChart'])) ? $params['endChart'] : '';

    	$userList  	  = $this->modelDeveloper->getListUserProperties($startList, $endList, $user_id);
    	$chartData	  = $this->modelEforms->getChartEForm($startChart, $endChart, $user_id);
        return response()->success([
            'contents' => [
                'user_list' => $userList,
                'chart'		=> $chartData,
            ]
        ]);
    }
}
