<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EForm;
use App\Models\Customer;
use App\Models\CustomerDetail;
use App\Models\Audit;
use App\Models\Developer;
use App\Models\Collateral;
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

    public function getEformCustomer(Request $request, $id)
    {
        $eform2 = Eform::where('nik', '=', $id)->first();

        $eform_id = $eform2->id;

        $eform =  Eform::select('*')
                    ->where('id', $eform_id)
                    ->paginate( $request->input( 'limit' ) ?: 10 );
       // dd($eform);

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
        $limit = $request->input('limit') ?: 2;
        $page = $request->input('page') ;

        $getCountModulName = \DB::table('auditrail_pengajuankredit')
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
                        })->get();
        
         $count = count($getCountModulName);

         if($page == 0 ){
            $pages = 0;
         
         }elseif($page >= 1){
            $pages = 0;
            $limit = $count; //$limit*2;
         }

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
                        })->limit($limit)->offset($pages)->get();
        $ReqPage = $request->input('page') +1 ;
        $data_list = [
                "current_page" => 1,
                "data" => $getModulName,
                "from" => 1,
                "last_page" => 1,
                "next_page_url" => url('auditrail/list-mnpengajuan'.'?page='.$ReqPage),
                "path" => url('auditrail/list-mnpengajuan'),
                "per_page" => $limit,
                "prev_page_url" => null,
                "to" => 1,
                "total" => $count
            ];

                        return response()->success([
                            'message' => 'Sukses',
                            'contents'=> $data_list
                        ], 200);
    }

    /**
     * This function for list-modulname auditrail tab Admin Developer
     * @param Illuminate\Http\Request
     */

    public function modulNameAdminDev(Request $request)
    {
        $limit = $request->input('limit') ?: 2;
        $page = $request->input('page') ;
       
        $getCountModulName = \DB::table('auditrail_new_admin_dev')
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

                        })->get();

        $count = count($getCountModulName);

        if($page == 0 ){
            $pages = 0;
            //$limit = $limit*2;
         
         }elseif($page >= 1){
            $pages = 0;
            $limit = $count;
         }

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

                        })->limit($limit)->offset($pages)->get();
        
        $ReqPage = $request->input('page') +1 ;
        $data_list = [
                "current_page" => 1,
                "data" => $getModulName,
                "from" => 1,
                "last_page" => 1,
                "next_page_url" => url('auditrail/list-mnadmindev'.'?page='.$ReqPage),
                "path" => url('auditrail/list-mnadmindev'),
                "per_page" => $limit,
                "prev_page_url" => null,
                "to" => 1,
                "total" => $count
            ];

                        return response()->success([
                            'message' => 'Sukses',
                            'contents'=> $data_list
                        ], 200);
    }

    /**
     * This function for list-modulname auditrail tab Appointment
     * @param Illuminate\Http\Request
     */

    public function modulNameAppointment(Request $request)
    {
        $page = $request->input('page') ;
        $getCountModulName = \DB::table('auditrail_type_one')
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
                         * This query for Auditrail Appointment
                         */

                         $appointment = 'app\models\appointment';

                         $auditrail->where('auditable_type', $appointment);

                        })->get();

        $count = count($getCountModulName);

        if($page == 0 ){
            $pages = 0;
            //$limit = $limit*2;
         
         }elseif($page >= 1){
            $pages = 0;
            $limit = $count;
         }

        $getModulName = \DB::table('auditrail_type_one')
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
                         * This query for Auditrail Appointment
                         */

                         $appointment = 'app\models\appointment';

                         $auditrail->where('auditable_type', $appointment);

                        })->limit($limit)->offset($pages)->get();
        
        $ReqPage = $request->input('page') +1 ;
        $data_list = [
                "current_page" => 1,
                "data" => $getModulName,
                "from" => 1,
                "last_page" => 1,
                "next_page_url" => url('auditrail/list-mnappointment'.'?page='.$ReqPage),
                "path" => url('auditrail/list-mnappointment'),
                "per_page" => $limit,
                "prev_page_url" => null,
                "to" => 1,
                "total" => $count
            ];

                        return response()->success([
                            'message' => 'Sukses',
                            'contents'=> $data_list
                        ], 200);
    }

    /**
     * This function for list-modulname auditrail tab Collateral
     * @param Illuminate\Http\Request
     */

    public function modulNameCollateral(Request $request)
    {
        $page = $request->input('page');
        $getCountModulName = \DB::table('auditrail_collaterals')
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
                         * This query for Auditrail Collateral
                         */

                         $model_type = ['app\models\collateral','app\models\otsdoc'];
                         $auditrail->wherein('auditable_type', $model_type);
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%tambah proyek%');
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%undefined%');
                         $auditrail->Orwhere(\DB::raw('LOWER(modul_name)'), 'like', '%ots%');

                        })->get();

        $count = count($getCountModulName);

        if($page == 0 ){
            $pages = 0;
            //$limit = $limit*2;
         
         }elseif($page >= 1){
            $pages = 0;
            $limit = $count;
         }

        $getModulName = \DB::table('auditrail_collaterals')
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
                         * This query for Auditrail Collateral
                         */

                         $model_type = ['app\models\collateral','app\models\otsdoc'];
                         $auditrail->wherein('auditable_type', $model_type);
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%tambah proyek%');
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%undefined%');
                         $auditrail->Orwhere(\DB::raw('LOWER(modul_name)'), 'like', '%ots%');

                        })->limit($limit)->offset($pages)->get();
        
        $ReqPage = $request->input('page') +1 ;
        $data_list = [
                "current_page" => 1,
                "data" => $getModulName,
                "from" => 1,
                "last_page" => 1,
                "next_page_url" => url('auditrail/list-mncollateral'.'?page='.$ReqPage),
                "path" => url('auditrail/list-mncollateral'),
                "per_page" => $limit,
                "prev_page_url" => null,
                "to" => 1,
                "total" => $count
            ];

                        return response()->success([
                            'message' => 'Sukses',
                            'contents'=> $data_list
                        ], 200);
    }

    /**
     * This function for list-modulname auditrail tab Agen Developer
     * @param Illuminate\Http\Request
     */

    public function modulNameAgenDev(Request $request)
    {
        $page = $request->input('page');
        $getCountModulName = \DB::table('auditrail_admin_developer')
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
                         * This query for Auditrail Agen Developer
                         */

                         $slug = 'developer-sales';
                         $action = 'undefined action';
                         $auditrail->where('role', $slug);
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'login');
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'logout');

                        })->get();

        $count = count($getCountModulName);

        if($page == 0 ){
            $pages = 0;
            //$limit = $limit*2;
         
         }elseif($page >= 1){
            $pages = 0;
            $limit = $count;
         }

        $getModulName = \DB::table('auditrail_admin_developer')
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
                         * This query for Auditrail Agen Developer
                         */

                         $slug = 'developer-sales';
                         $action = 'undefined action';
                         $auditrail->where('role', $slug);
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'login');
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'logout');

                        })->limit($limit)->offset($pages)->get();
        
        $ReqPage = $request->input('page') +1 ;
        $data_list = [
                "current_page" => 1,
                "data" => $getModulName,
                "from" => 1,
                "last_page" => 1,
                "next_page_url" => url('auditrail/list-mnagendev'.'?page='.$ReqPage),
                "path" => url('auditrail/list-mnagendev'),
                "per_page" => $limit,
                "prev_page_url" => null,
                "to" => 1,
                "total" => $count
            ];

                        return response()->success([
                            'message' => 'Sukses',
                            'contents'=> $data_list
                        ], 200);
    }

    /**
     * This function for list-modulname auditrail tab Property
     * @param Illuminate\Http\Request
     */

    public function modulNameProperty(Request $request)
    {
        $getModulName = \DB::table('auditrail_property')
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
                         * This query for Auditrail Property
                         */

                         $auditrail->where(\DB::raw('LOWER(auditable_type)'), 'like', '%property%');
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%undefined action%');
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%pengajuan%');
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%col%');

                        })->paginate( $request->input( 'limit' ) ? : 10 );
                        return response()->success([
                            'message' => 'Success',
                            'contents'=> $getModulName
                        ], 200);
    }

    /**
     * This function for list-modulname auditrail tab Detail User Activity
     * @param Illuminate\Http\Request
     */

    public function modulNameDetailUserActivity(Request $request, $id)
    {
        $getModulName = \DB::table('auditrail_admin_developer')
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
                        ->where( function( $auditrail ) use ( &$request, &$id ){
                        
                        /**
                         * This query for Auditrail Detail User Activity
                         */

                         $action = 'undefined action';
                         $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
                         $auditrail->where('user_id', '=', $id);

                        })->paginate( $request->input( 'limit' ) ? : 10 );
                        return response()->success([
                            'message' => 'Success',
                            'contents'=> $getModulName
                        ], 200);
    }

    //Collateral detail
     /**
     * Show collateral list
     * @return \Illuminate\Http\Response
     */
    public function collaterlDeveloper(Request $request)
    {
      
      $developer_id = env('DEVELOPER_KEY',1);
      $data = Collateral::with('property','developer')->where('developer_id','!=',$developer_id); 
      if ($request->has('status')) $data->where('status', $request->input('status'));
      if($request->has('staff_name')){
                $data->where(\DB::raw('LOWER(staff_name)'), 'like', '%'.strtolower($request->input('staff_name')).'%');
      }
      if($request->has('manager_id')){
                $data->where(\DB::raw('manager_id'), '=', $request->input('manager_id'));
      } 
      if($request->has('manager_name')){
                $data->where(\DB::raw('LOWER(manager_name)'), 'like', '%'.strtolower($request->input('manager_name')).'%');
      }
      
      if($request->has('region_id')){
            $data->whereHas('property',function($property) use ($request)
        {
          $property->where('region_id','ilike','%'.$request->input('region_id').'%');
 
        });
      } 
      if($request->has('region_name')){
        $data->whereHas('property',function($property) use ($request)
        {
          $property->where('region_name','ilike','%'.$request->input('region_name').'%');
        });
      }
      if ($request->has('created_at')){
        $data->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
      }  
        
      $data->orderBy('created_at', 'desc');
      return response()->success([
        'contents' => $data->paginate($request->has('limit') ? $request->limit : 10)
      ]);
    }

    /**
     * Show Collateral Non Kerjasama
     * @return \Illuminate\Http\Response
     */
    public function collateralNon(Request $request)
    {
      $developer_id = env('DEVELOPER_KEY',1);

      // $data = Collateral::GetLists($request)->where('developer_id','=',$developer_id);
        $data = \DB::table('collateral_view_table')
                    ->selectRaw("properties.region_name,collaterals.manager_id,
                        collaterals.manager_name,
                        collaterals.created_at,
                           collateral_view_table.*")
                    ->join("properties", "properties.id", "=", "collateral_view_table.property_id")
                    ->join("collaterals", "collaterals.id", "=", "collateral_view_table.collaterals_id");
        if ($request->has('status')) $data->where('collaterals.status', $request->input('status'));
      if($request->has('manager_id')){
        $data->where(\DB::raw('manager_id'), '=',$request->input('manager_id'));
      } 
      if($request->has('manager_name')){
        $data->where(\DB::raw('LOWER(manager_name)'), 'like', '%'.strtolower($request->input('manager_name')).'%');
      } 
      if($request->has('region_id')){
        $data->where('collateral_view_table.region_id','ilike','%'.$request->input('region_id').'%');
      }
      if($request->has('region_name')){
        $data->where('region_name','ilike','%'.$request->input('region_name').'%');
      }         
      if ($request->has('created_at')){
        $data->where(\DB::raw('DATE(collaterals.created_at)'), $request->input('created_at'));
      }   
      if($request->has('staff_name')){
            $data->where(\DB::raw('LOWER(collateral_view_table.staff_name)'), 'like', '%'.strtolower($request->input('staff_name')).'%');
      }
           $data->orderBy('collaterals.created_at', 'desc');        

      return response()->success([
        'contents' => $data->paginate($request->has('limit') ? $request->limit : 10)
      ]);
    }

    /*
        Show data collateral
    */

    public function showCollateraldetail($developerId, $propertyId){
            if($developerId ==1){

            }else{
                return $this->makeResponse(
                $this->collateral->withAll()->where('developer_id', $developerId)->where('property_id', $propertyId)->firstOrFail()
                );
            }
    }


     /**
     * Build response json
     * @param  mixed $data
     * @return \Illuminate\Http\Response
     */
    private function makeResponse($data)
    {
      return response()->success([
        'contents' => $data
      ]);
    }

}
