<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserDeveloper;
class DashboardController extends Controller
{
    protected $request;
    protected $property;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->property = new UserDeveloper;
    }

    public function dashboard()
    {
    	$userList  = $this->property->getListUserDeveloper();
    	$chartData = $this->property->getDataChart();
        return response()->success([
            'contents' => [
                'user_list' => $userList,
                'chart'		=> $chartData,
            ]
        ]);
    }
}
