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

    public function auditCollateral(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsCollateral($request)->paginate($limit);
        \Log::info($auditrail);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditProperty(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsProperty($request)->paginate($limit);
        \Log::info($auditrail);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditUserActivity(Request $request, Audit $audit)
    {
         $limit = $request->input('limit') ?: 10;
         $page = $request->input('page') ;
        // $auditrail = $audit->getuser($request)->paginate($limit);;
        // \Log::info($auditrail);
         if($page == 0 || $page == 1){
         $pages = 0;
         
         }elseif($page > 1){
         	$pages = ($page*10)-10;
         }
       //  dd($pages);
         $table_count = \DB::table('auditrail_admin_developer')
                            ->select(\DB::raw('DISTINCT(user_id)'))
                            ->whereNotNull('user_id')
                            ->where(\DB::raw('user_id'),'!=', 0)
                            ->where(\DB::raw('user_id'),'!=', 123)
                            ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                            ->get();
         
        $table_created_at = \DB::table('auditrail_admin_developer')
                            ->select(\DB::raw('DISTINCT(user_id)'))
                            ->whereNotNull('user_id')
                            ->where(\DB::raw('user_id'),'!=', 0)
                            ->where(\DB::raw('user_id'),'!=', 123)
                            ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama User.
                *
                * @param $request->username
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('username')){
                        $auditrail->where(\DB::raw('LOWER(username)'), 'like', '%'.strtolower($request->input('username')).'%');
                  
                    }
                })
                            //->groupBy('user_id')
                            ->limit($limit)->offset($pages)
                            ->get();
                            //dd(json_encode($table_created_at));
                            $count = count($table_created_at);
         if($count > 0){
        		foreach ($table_created_at as $key => $value) {
        		$data[] = $table = \DB::table('auditrail_admin_developer')
        				->select(\DB::raw('*'))
        				->where('user_id', $value->user_id)->first();	
        			}
		}else{
			$data = [];
		}
        $data_list = [
	            "current_page" => 1,
	            "data" => $data,
	            "from" => 1,
	            "last_page" => 1,
	            "next_page_url" => url('auditrail/useractivity'.'?page='.$request->input('page')),
	            "path" => url('auditrail/useractivity'),
	            "per_page" => $count,
	            "prev_page_url" => null,
	            "to" => 1,
	            "total" => count($table_count)
	        	];

        return response()->success(['contents' => $data_list]);
    }

     public function auditUserActitiyDetail($id, Request $request){
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::GetListsDetailActivity($request, $id)->paginate($limit);
        \Log::info($auditrail);
        return response()->success(['contents' => $auditrail]);
    }

}
