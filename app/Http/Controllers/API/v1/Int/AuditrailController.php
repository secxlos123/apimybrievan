<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EForm;
use App\Models\Customer;
use App\Models\CustomerDetail;
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
        				->where('user_id', $value->user_id)
        				->orderBy('created_at', 'desc')
        				->first();	
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

    public function show( $id )
    {
        $eform2 = Eform::where('nik', '=', $id)->first();
       // dd($eform->id);
        $eform_id = $eform2->id;

        $eform = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );
     //     dd($eform);              

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $eform,
        ], 200 );
    }

    public function getNik(Request $request)
    {
        $getNik = \DB::table('eforms')
                    ->selectRaw("eforms.nik, concat(users.first_name, ' ', users.last_name) as nama_pemohon")
                    ->leftJoin("users", "users.id", "=", "eforms.user_id")
                    ->where(function ($auditrail) use ($request) {
               /**
                * This query for search by Nama Customer or Nik Customer .
                *
                * @param $request->search
                * @return \Illuminate\Database\Eloquent\Builder
                */

                    if($request->has('search')){
                        $auditrail->where(\DB::raw('LOWER(nik)'), 'like', '%'.strtolower($request->input('search')).'%');
                        $auditrail->Orwhere(\DB::raw("concat(lower(users.first_name), ' ', lower(users.last_name))"), 'like', '%'.strtolower($request->input('search')).'%');
                  
                        }
                    })
                    ->paginate( $request->input( 'limit' ) ?: 10 );
                    \Log::info($getNik);
                    return response()->success( [
                        'message' => 'Sukses',
                        'contents' => $getNik
                        ], 200 );
    }

    /**
     * This function for list-modulname auditrail tab pengajuan kredit
     * @param Illuminate\Http\Request
     */

    public function modulNamePengajuanKredit(Request $request)
    {
        $getModulName = \DB::table('auditrail_pengajuankredit')
                        ->selectRaw("distinct(modul_name)")
                        ->where(function($auditrail) use ($request){
                    /**
                     * This query for search by Modul Name .
                     *
                     * @param $request->search
                     * @return \Illuminate\Database\Eloquent\Builder
                     */
                        if($request->has('search')){
                            $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', '%'.strtolower($request->input('search')).'%');
                        }

                        })
                          ->where(function ($auditrail) use (&$request, &$query){
                    
                        $eform = 'app\\models\\eform';
                        $data_action = ['pengajuan kredit', 'tambah leads', 'pengajuan kredit via ao', 'eform tambah leads via ao', 'disposisi kredit', 'verifikasi data nasabah'];
                        $appointment = 'app\\models\\appointment';
                        $propertyItem = 'app\\models\\propertyitem';
                        $auditrail->whereNotNull('username');
                        $auditrail->where(\DB::raw('LOWER(new_values)'), '!=', '[]');
                        $auditrail->whereIn(DB::raw('lower(modul_name)'), $data_action);
                        $auditrail->where('auditable_type', '!=', $appointment);
                        $auditrail->where('auditable_type', '!=', $propertyItem);
                        })->paginate( $request->input( 'limit' ) ? : 10 );
                        return response()->success([
                            'message' => 'Sukses',
                            'contents'=> $getModulName
                        ], 200);
    }

    /**
     * This function for list-modulname auditrail tab Admin Developer
     * @param Illuminate\Http\Request
     */

    public function modulNameAdminDev(Request $request)
    {
        $getModulName = \DB::table('auditrail_new_admin_dev')
                        ->selectRaw("distinct(modul_name)")
                        ->where( function( $auditrail ) use ( $request ){
                        
                        /**
                         * This query for search by Modul Name .
                         *
                         * @param $request->search
                         * @return \Illuminate\Database\Eloquent\Builder
                         */
                            if($request->has('search')){
                                $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', '%'.strtolower($request->input('search')).'%');
                            }
                        })
                        ->where( function( $auditrail ) use ( $request ){
                        
                        /**
                         * This query for Auditrail Admin Developer
                         */
                        $slug = 'developer';
                        $action = ['undefined action','login','logout'];
                        $model_type = 'app\\models\\audit';
                        $auditrail->where('auditable_type', '!=', $model_type);
                        $auditrail->where(\DB::raw('LOWER(new_values)'), 'not like', '[]');
                        $auditrail->whereIn(\DB::raw('LOWER(modul_name)'), ['tambah admin dev','banned admin dev','unbanned admin dev','edit proyek','tambah agen','ubah admin dev','unbanned agen','banned agen','edit tipe property','tambah tipe property','tambah proyek','edit agen','tambah unit property']);

                        })->paginate( $request->input( 'limit' ) ? : 10 );
                        return response()->success([
                            'message' => 'Success',
                            'contents'=> $getModulName
                        ], 200);
    }

}
