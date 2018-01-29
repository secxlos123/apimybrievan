<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Audit;
use DB;

class AuditrailController extends Controller
{
    public function index(Request $request)
    {
    	$limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getLists($request)->paginate($limit);

        return response()->success(['contents' => $auditrail]);
    }

    public function auditAppointment(Request $request)
    {
    	$limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsAppointment($request)->paginate($limit);
        \Log::info($auditrail);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditAdmindev(Request $request)
    {
    	$limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsAdmindev($request)->paginate($limit);
        \Log::info($auditrail);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditLogin(Request $request)
    {
    	$limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsLogin($request)->paginate($limit);
        \Log::info($auditrail);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditAgendev(Request $request)
    {
    	$limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsAgenDev($request)->paginate($limit);
        \Log::info($auditrail);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditEdit(Request $request)
    {
    	$limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsEdit($request)->paginate($limit);
        \Log::info($auditrail);
        return response()->success(['contents' => $auditrail]);
    }
}
