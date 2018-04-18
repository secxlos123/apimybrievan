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
use File;

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
        return response()->success(['contents' => $auditrail]);
    }

    public function auditAdmindev(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsAdmindev($request)->paginate($limit);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditLogin(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsLogin($request)->paginate($limit);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditAgendev(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsAgenDev($request)->paginate($limit);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditEdit(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsEdit($request)->paginate($limit);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditCollateral(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsCollateral($request)->paginate($limit);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditProperty(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::getListsProperty($request)->paginate($limit);
        return response()->success(['contents' => $auditrail]);
    }

    public function auditUserActivity(Request $request, Audit $audit)
    {
        $limit = $request->input('limit') ?: 10;
        $page = $request->input('page') ;

        if ( $page == 0 || $page == 1 ) {
            $pages = 0;

        } else if ( $page > 1 ) {
            $pages = ( $page * 10 ) - 10;

        }

        $table_count = \DB::table('auditrail_admin_developer')
            ->select( \DB::raw( 'DISTINCT(user_id)' ) )
            ->whereNotNull( 'user_id' )
            ->where(\DB::raw('user_id'),'!=', 0)
            ->where(\DB::raw('user_id'),'!=', 123)
            ->where(function ($auditrail) use ($request) {
                $lowerValue = '%'.strtolower($request->input('username')).'%';
                if($request->has('username')){
                    $auditrail->where(\DB::raw('LOWER(username)'), 'like', $lowerValue);
                }
            })
            ->get();

        $table_created_at = \DB::table('auditrail_admin_developer')
            ->select(\DB::raw('DISTINCT(user_id)'))
            ->whereNotNull('user_id')
            ->where(\DB::raw('user_id'),'!=', 0)
            ->where(\DB::raw('user_id'),'!=', 123)
            ->where(function ($auditrail) use ($request) {
                $lowerValue = '%'.strtolower($request->input('username')).'%';
                if($request->has('username')){
                    $auditrail->where(\DB::raw('LOWER(username)'), 'like', $lowerValue);
                }
            })
            ->limit($limit)
            ->offset($pages)
            ->get();

        $count = count($table_created_at);

        if( $count > 0 ){
            foreach ($table_created_at as $key => $value) {
                $data[] = $table = \DB::table('auditrail_admin_developer')
                    ->select(\DB::raw('*'))
                    ->where('user_id', $value->user_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

        } else {
            $data = [];

        }

        $data_list = [
            "current_page" => 1
            , "data" => $data
            , "from" => 1
            , "last_page" => 1
            , "next_page_url" => url('auditrail/useractivity'.'?page='.$request->input('page'))
            , "path" => url('auditrail/useractivity')
            , "per_page" => $count
            , "prev_page_url" => null
            , "to" => 1
            , "total" => count($table_count)
        ];

        return response()->success(['contents' => $data_list]);
    }

    public function auditUserActitiyDetail($id, Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $auditrail = Audit::GetListsDetailActivity($request, $id)->paginate($limit);
        return response()->success(['contents' => $auditrail]);
    }

    public function show( $id )
    {
        $eform2 = Eform::where('nik', '=', $id)->first();
        $eform_id = $eform2->id;

        $eform = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $eform,
        ], 200 );
    }

    public function getEformCustomer(Request $request, $id)
    {
        if (!empty($id)) {
            $eform2 = Eform::where('nik', '=', $id)->first();
            $eform_id = $eform2->id;
            $eform = Eform::select('*')
                ->where('id', $eform_id)
                ->paginate( $request->input( 'limit' ) ?: 10 );

        } else {
            $eform = Eform::select('*')
                ->paginate( $request->input( 'limit' ) ?: 10 );

        }

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $eform,
        ], 200 );
    }

    /**
     * This function for list-nik and name auditrail tab pengajuan kredit
     * @param Illuminate\Http\Request
     */
    public function getNik(Request $request)
    {
        $getNik = \DB::table('eforms')
            ->selectRaw("eforms.nik, concat(users.first_name, ' ', users.last_name) as nama_pemohon")
            ->leftJoin("users", "users.id", "=", "eforms.user_id")
            ->where(function ($auditrail) use ($request) {
                $lower = '%' . strtolower($request->input('search')) . '%';
                if($request->has('search')){
                    $auditrail->where(\DB::raw('LOWER(nik)'), 'like', $lower);
                    $auditrail->Orwhere(\DB::raw("concat(lower(users.first_name), ' ', lower(users.last_name))"), 'like', $lower);
                }
            })
            ->paginate( $request->input( 'limit' ) ?: 10 );

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $getNik
        ], 200 );
    }

    /**
     * This function for list-branch_id and branch_name auditrail tab pengajuan kredit
     * @param Illuminate\Http\Request
     */
    public function getBranch(Request $request)
    {
        $getBranch = \DB::table('eforms')
            ->selectRaw("branch_id, branch")
            ->where(function ($auditrail) use ($request) {
                $lower = '%' . strtolower($request->input('search')) . '%';
                if($request->has('search')){
                    $auditrail->where(\DB::raw('LOWER(branch)'), 'like', $lower);
                }

                if($request->has('branch_id')){
                    $lowerBranch = '%' . strtolower($request->input('branch_id')) . '%';
                    $auditrail->where(\DB::raw('LOWER(branch_id)'), 'like', $lowerBranch);
                }
            })
            ->groupBy('branch_id', 'branch')
            ->paginate( $request->input( 'limit' ) ?: 10 );

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $getBranch
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
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where(function ($auditrail) use (&$request, &$query){
                $eform = 'app\\models\\eform';
                $data_action = ['pengajuan kredit', 'tambah leads', 'pengajuan kredit via ao', 'eform tambah leads via ao', 'disposisi kredit', 'verifikasi data nasabah', 'approval kredit'];
                $appointment = 'app\\models\\appointment';
                $propertyItem = 'app\\models\\propertyitem';
                $auditrail->whereNotNull('username');
                $auditrail->where(\DB::raw('LOWER(new_values)'), '!=', '[]');
                $auditrail->whereIn(DB::raw('lower(modul_name)'), $data_action);
                $auditrail->where('auditable_type', '!=', $appointment);
                $auditrail->where('auditable_type', '!=', $propertyItem);
            })->get();

         $count = count($getCountModulName);

         if ( $page == 0 ) {
            $pages = 0;

         } else if ( $page >= 1 ) {
            $pages = 0;
            $limit = $count;

         }

        $getModulName = \DB::table('auditrail_pengajuankredit')
            ->selectRaw("distinct(modul_name)")
            ->where(function($auditrail) use ($request){
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where(function ($auditrail) use (&$request, &$query){
                $eform = 'app\\models\\eform';
                $data_action = ['pengajuan kredit', 'tambah leads', 'pengajuan kredit via ao', 'eform tambah leads via ao', 'disposisi kredit', 'verifikasi data nasabah', 'approval kredit'];
                $appointment = 'app\\models\\appointment';
                $propertyItem = 'app\\models\\propertyitem';
                $auditrail->whereNotNull('username');
                $auditrail->where(\DB::raw('LOWER(new_values)'), '!=', '[]');
                $auditrail->whereIn(DB::raw('lower(modul_name)'), $data_action);
                $auditrail->where('auditable_type', '!=', $appointment);
                $auditrail->where('auditable_type', '!=', $propertyItem);
            })
            ->limit($limit)
            ->offset($pages)
            ->get();

            $ReqPage = $request->input('page') +1 ;
            $url = 'list-mnpengajuan';
            $data_list = $this->dataList($getModulName, $ReqPage, $limit, $count, $url);

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
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $slug = 'developer';
                $action = ['undefined action','login','logout'];
                $model_type = 'app\\models\\audit';
                $auditrail->where('auditable_type', '!=', $model_type);
                $auditrail->where(\DB::raw('LOWER(new_values)'), 'not like', '[]');
                $auditrail->whereIn(\DB::raw('LOWER(modul_name)'), ['tambah admin dev','banned admin dev','unbanned admin dev','edit proyek','tambah agen','ubah admin dev','unbanned agen','banned agen','edit tipe property','tambah tipe property','tambah proyek','edit agen','tambah unit property']);
            })->get();

        $count = count($getCountModulName);

        if ( $page == 0 ) {
            $pages = 0;

        } else if ( $page >= 1 ) {
            $pages = 0;
            $limit = $count;

        }

        $getModulName = \DB::table('auditrail_new_admin_dev')
            ->selectRaw("distinct(modul_name)")
            ->where( function( $auditrail ) use ( $request ){
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $slug = 'developer';
                $action = ['undefined action','login','logout'];
                $model_type = 'app\\models\\audit';
                $auditrail->where('auditable_type', '!=', $model_type);
                $auditrail->where(\DB::raw('LOWER(new_values)'), 'not like', '[]');
                $auditrail->whereIn(\DB::raw('LOWER(modul_name)'), ['tambah admin dev','banned admin dev','unbanned admin dev','edit proyek','tambah agen','ubah admin dev','unbanned agen','banned agen','edit tipe property','tambah tipe property','tambah proyek','edit agen','tambah unit property']);
            })
            ->limit($limit)
            ->offset($pages)
            ->get();

        $ReqPage = $request->input('page') +1 ;
        $url = 'list-mnadmindev';
        $data_list = $this->dataList($getModulName, $ReqPage, $limit, $count, $url);

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
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $appointment = 'app\models\appointment';
                $auditrail->where('auditable_type', $appointment);
            })
            ->get();

        $count = count($getCountModulName);

        if ( $page == 0 ) {
            $pages = 0;

         } else if ( $page >= 1 ) {
            $pages = 0;
            $limit = $count;

         }

        $getModulName = \DB::table('auditrail_type_one')
            ->selectRaw("distinct(modul_name)")
            ->where( function( $auditrail ) use ( $request ){
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $appointment = 'app\models\appointment';
                $auditrail->where('auditable_type', $appointment);
            })
            ->limit($limit)
            ->offset($pages)
            ->get();

        $ReqPage = $request->input('page') +1 ;
        $url = 'list-mnappointment';
        $data_list = $this->dataList($getModulName, $ReqPage, $limit, $count, $url);

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
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $model_type = ['app\models\collateral','app\models\otsdoc'];
                $auditrail->wherein('auditable_type', $model_type);
                $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%tambah proyek%');
                $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%undefined%');
                $auditrail->Orwhere(\DB::raw('LOWER(modul_name)'), 'like', '%ots%');
            })
            ->get();

            $count = count($getCountModulName);

            if ( $page == 0 ) {
                $pages = 0;

            } else if ( $page >= 1 ) {
                $pages = 0;
                $limit = $count;

            }

        $getModulName = \DB::table('auditrail_collaterals')
            ->selectRaw("distinct(modul_name)")
            ->where( function( $auditrail ) use ( $request ){
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $model_type = ['app\models\collateral','app\models\otsdoc'];
                $auditrail->wherein('auditable_type', $model_type);
                $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%tambah proyek%');
                $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%undefined%');
                $auditrail->Orwhere(\DB::raw('LOWER(modul_name)'), 'like', '%ots%');
            })
            ->limit($limit)
            ->offset($pages)
            ->get();

        $ReqPage = $request->input('page') +1 ;
        $url = 'list-mncollateral';
        $data_list = $this->dataList($getModulName, $ReqPage, $limit, $count, $url);

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
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $slug = 'developer-sales';
                $action = 'undefined action';
                $auditrail->where('role', $slug);
                $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
                $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'login');
                $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'logout');
            })->get();

        $count = count($getCountModulName);

        if ( $page == 0 ) {
            $pages = 0;

        } else if ( $page >= 1 ) {
            $pages = 0;
            $limit = $count;

        }

        $getModulName = \DB::table('auditrail_admin_developer')
            ->selectRaw("distinct(modul_name)")
            ->where( function( $auditrail ) use ( $request ){
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $slug = 'developer-sales';
                $action = 'undefined action';
                $auditrail->where('role', $slug);
                $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
                $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'login');
                $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', 'logout');
            })
            ->limit($limit)->offset($pages)->get();

        $ReqPage = $request->input('page') +1 ;
        $url = 'list-mnagendev';
        $data_list = $this->dataList($getModulName, $ReqPage, $limit, $count, $url);

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
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( $request ){
                $auditrail->where(\DB::raw('LOWER(auditable_type)'), 'like', '%property%');
                $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%undefined action%');
                $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%pengajuan%');
                $auditrail->where(\DB::raw('LOWER(modul_name)'), 'not like', '%col%');
            })
            ->paginate( $request->input( 'limit' ) ? : 10 );

        return response()->success([
            'message' => 'Success',
            'contents'=> $getModulName
        ], 200);
    }

    /**
     * This Function For array List
     * @return array $data_list
     */
    public function dataList($getModulName, $ReqPage, $limit, $count, $url)
    {
        $data_list = [
            "current_page" => 1,
            "data" => $getModulName,
            "from" => 1,
            "last_page" => 1,
            "next_page_url" => url('auditrail/'.$url.'?page='.$ReqPage),
            "path" => url('auditrail/'.$url),
            "per_page" => $limit,
            "prev_page_url" => null,
            "to" => 1,
            "total" => $count
        ];
        return $data_list;
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
                if($request->has('search')){
                    $lowerSearch = '%'.strtolower($request->input('search')).'%';
                    $auditrail->where(\DB::raw('lower(modul_name)'), 'ilike', $lowerSearch);
                }
            })
            ->where( function( $auditrail ) use ( &$request, &$id ){
                $action = 'undefined action';
                $auditrail->where(\DB::raw('LOWER(modul_name)'), '!=', $action);
                $auditrail->where('user_id', '=', $id);
            })->paginate( $request->input( 'limit' ) ? : 10 );

        return response()->success([
            'message' => 'Success',
            'contents'=> $getModulName
        ], 200);
    }

     /**
     * Show collateral list
     * @return \Illuminate\Http\Response
     */
    public function collaterlDeveloper(Request $request)
    {
        //Collateral Detail
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
            $data->whereHas('property',function($property) use ($request) {
                $property->where('region_id','ilike','%'.$request->input('region_id').'%');
            });
        }

        if($request->has('region_name')){
            $data->whereHas('property',function($property) use ($request) {
              $property->where('region_name','ilike','%'.$request->input('region_name').'%');
            });
        }

        if ($request->has('created_at')){
            $data->where(\DB::raw('DATE(created_at)'), $request->input('created_at'));
        }

        if ($request->has('search')){
            $data->whereHas('property',function($property) use ($request){
                $lowerValue = '%'.$request->input('search').'%';
                $property->where(\DB::raw('LOWER(name)'), 'ilike', $lowerValue);
                $property->Orwhere(\DB::raw('LOWER(pic_name)'), 'ilike', $lowerValue);
            });
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

        $data = \DB::table('collateral_view_table')
            ->selectRaw("properties.region_name,collaterals.manager_id, collaterals.manager_name, collaterals.created_at, collateral_view_table.*")
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

        if($request->has('search')){
            $lowerValue = '%'.$request->input('search').'%';
            $data->where(\DB::raw("concat(lower(collateral_view_table.first_name), ' ', lower(collateral_view_table.last_name))"), 'ilike', $lowerValue);
            $data->Orwhere(\DB::raw('lower(home_location)'), 'ilike', $lowerValue);
        }

        $data->orderBy('collaterals.created_at', 'desc');

        return response()->success([
            'contents' => $data->paginate($request->has('limit') ? $request->limit : 10)
        ]);
    }

    /**
     * Build response json
     * @param  int $developerId
     * @param  int $propertyId
     * @return \Illuminate\Http\Response
     */
    public function showCollateraldetail($developerId, $propertyId){
        if( $developerId != 1 ){
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

    /**
     * This Fucntion for get Image on File Upload
     * @param $request by nik
     * @return \Illuminate\Http\Response
     */
    public function getImageUpload(Request $request)
    {
        $path = public_path('uploads/' .$request->nik);
        if (is_dir($path)) {
            $files = File::allFiles($path);
            if (count($files) > 0) {
                $image = array();
                foreach ($files as $file) {
                    if ( !empty($file) ) {
                        $image[]['name'] = url('uploads/'.$request->nik.'/'.$file->getFilename());
                    }
                }
                return response()->success(['message' => 'Success','contents' => $image], 200);
            }
        }

        return response()->success(['message' => 'Fails','contents' => ['image'=>'Nik Tidak ditemukan']], 404);
    }
}
