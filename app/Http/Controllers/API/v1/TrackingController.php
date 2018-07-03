<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EForm;
use DB;

class TrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type ,Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort') ) : ['id', 'asc'];
        $eforms = array();

        if ($type == 'eks') {
            $user = $request->user();

            if( $user->inRole( 'customer' ) ) {
                $eforms = \DB::table('eforms')
                    ->selectRaw("eforms.id
                        , eforms.ao_name as ao
                        , concat(users.first_name, ' ', users.last_name) as nama_pemohon
                        , case when eforms.product_type='kpr' then developers.company_name
                        else ''
                        end as developer_name
                        , case when eforms.product_type='kpr' then kpr.property_item_name
                        else ''
                        end as property_name
                        , eforms.product_type as product_type
                        , date(eforms.created_at) as tanggal_pengajuan
                        , case when eforms.product_type='kpr' then kpr.request_amount else  briguna.request_amount end as jumlah_pengajuan
                        , case when (eforms.is_approved = false and eforms.recommended = true) or eforms.status_eform = 'Rejected' then 'Kredit Ditolak'
                        when eforms.status_eform = 'Approval1' then 'Kredit Disetujui'
                        when eforms.status_eform = 'Pencairan' then 'Proses Pencairan'
                        when eforms.is_approved = true and eforms.product_type='kpr' then 'Proses Analisa Pengajuan'
						when eforms.status_eform = 'Approval' then 'Disetujui Briguna'
						when eforms.status_eform = 'Disbursed' then 'Disbursed'
                        when visit_reports.id is not null and eforms.product_type='kpr' then 'Proses Analisa Pengajuan'
                        when eforms.ao_id is not null and eforms.product_type='kpr' then 'Pengajuan Diterima'
                        else
						(case when eforms.product_type='briguna' and eforms.status_eform is not null then 'Menunggu Putusan' else 	'Pengajuan Kredit' end)
						 end as status
                    ")
                    ->leftJoin("users", "users.id", "=", "eforms.user_id")
                    ->leftJoin('kpr', function($join) {
                        $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                        $join->on('kpr.eform_id', '=', 'eforms.id');
                    } )
                    ->leftJoin('briguna', function($join) {
                        $join->on('eforms.product_type', '=', DB::raw("'briguna'"));
                        $join->on('briguna.eform_id', '=', 'eforms.id');
                    })
                    ->leftJoin('developers', function($join) {
                        $join->on('developers.user_id', '=', 'kpr.developer_id');
                        $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                    })
                    ->leftJoin('visit_reports', function($join) {
                        $join->on('eforms.id', '=', 'visit_reports.eform_id');
                        $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                    })
                    ->where( "eforms.user_id", $user->id )
                    ->paginate( $request->input( 'limit' ) ?: 10 );

            } else if( $user->inRole('developer-sales') ) {
                    $eforms = \DB::table('eforms')->selectRaw("eforms.id
                    , eforms.ao_name as ao
                    , concat(users.first_name, ' ', users.last_name) as nama_pemohon
                    , case when eforms.product_type = 'kpr' then developers.company_name
                      else ''
                      end as developer_name
                    , case when eforms.product_type = 'kpr' then kpr.property_item_name
                      else ''
                      end as property_name
					, case when eforms.product_type = 'kpr' then kpr.request_amount
                      else briguna.request_amount
                      end as nominal
                    , eforms.product_type as product_type
                    , eforms.ref_number as ref_number
                    , date(eforms.created_at) as tanggal_pengajuan
					, case when eforms.product_type='kpr' then kpr.request_amount
                      else briguna.request_amount
                      end as jumlah_pengajuan
                    , case when (eforms.is_approved = false and eforms.recommended = true) or eforms.status_eform = 'Rejected' then 'Kredit Ditolak'
                        when eforms.status_eform = 'Approval1' then 'Kredit Disetujui'
                        when eforms.status_eform = 'Pencairan' then 'Proses Pencairan'
                        when eforms.is_approved = true  and eforms.product_type='kpr' then 'Proses Analisa Pengajuan'
                        when visit_reports.id is not null then 'Proses Analisa Pengajuan'
                        when eforms.ao_id is not null and eforms.product_type='kpr' then 'Pengajuan Diterima'
                        else 'Pengajuan Kredit' end as status
                    ")
                    ->leftJoin("users", "users.id", "=", "eforms.user_id")
                    ->leftJoin('kpr', function($join)
                         {
                             $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                             $join->on('kpr.eform_id', '=', 'eforms.id');
                         })
                    ->leftJoin('briguna', function($join)
                         {
                             $join->on('eforms.product_type', '=', DB::raw("'briguna'"));
                             $join->on('briguna.eform_id', '=', 'eforms.id');
                         })
                    ->leftJoin('developers', function($join)
                         {
                             $join->on('developers.user_id', '=', 'kpr.developer_id');
                             $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                         })
                    ->leftJoin('visit_reports', function($join)
                         {
                             $join->on('eforms.id', '=', 'visit_reports.eform_id');
                             $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                         })
                    ->where( "eforms.sales_dev_id", $user->id )
                    ->where(function($item) use (&$request){
                        if($request->has('status'))
                            $item->where('eforms.is_approved', $request->input('status'));
                    })
                    ->where(function($item) use (&$request){
                        if ($request->has('search')){
                             $item->where(\DB::raw('LOWER(users.first_name)'), 'like', '%'.strtolower($request->input('search')).'%');
                            $item->Orwhere(\DB::raw('LOWER(users.last_name)'), 'like', '%'.strtolower($request->input('search')).'%');
                            $item->Orwhere(\DB::raw('LOWER(kpr.property_item_name)'), 'like', '%'.strtolower($request->input('search')).'%');
                            $item->Orwhere(\DB::raw('LOWER(eforms.product_type)'), 'like', '%'.strtolower($request->input('search')).'%');
                            $item->Orwhere(\DB::raw('LOWER(eforms.ref_number)'), 'like', '%'.strtolower($request->input('search')).'%');
                                if(strtolower($request->input('search')) == "proses analisa pengajuan") {
                                    $item->Orwhere('eforms.is_approved', 'true');
                                } else if(strtolower($request->input('search')) == "kredit ditolak") {
                                    $item->where('eforms.is_approved', 'false');
                                    $item->where('eforms.recommended', 'true');
                                } else if(strtolower($request->input('search')) == "pengajuan diterima") {
                                    $ao_id = DB::table('eforms')->selectRaw('eforms.ao_id')->get();
                                    foreach ($ao_id as $key => $value) {
                                        \Log::info("==========AO_ID=============");
                                        \Log::info($value->ao_id);
                                        $item->Orwhere('eforms.ao_id',  'like','%'.$value->ao_id.'%');
                                    }
                                } elseif(strtolower($request->input('search')) == "pengajuan kredit") {
                                    $item->Orwhere('eforms.ao_id',  NULL);
                                }
                            }
                    })
                    ->orderBy($sort[0], $sort[1])->paginate( $request->input( 'limit' ) ?: 10 );

            }
        }

        if( $request->header('pn') ) {
            $statusQuery = "case when (eforms.is_approved = false and eforms.recommended = true) or eforms.status_eform = 'Rejected' then 'Kredit Ditolak'
			when eforms.status_eform = 'Approval1' then 'Kredit Disetujui'
			when eforms.status_eform = 'Approval2' then 'Rekontes Kredit'
                        when eforms.status_eform = 'Pencairan' then 'Proses Pencairan'
			when eforms.is_approved = true  and eforms.product_type='kpr' then 'Proses CLS'
			when visit_reports.id is not null then 'Prakarsa'
            when eforms.is_approved = true and eforms.product_type='kpr' then 'Proses Analisa Pengajuan'
			when eforms.status_eform = 'Approval' then 'Disetujui Briguna'
			when eforms.status_eform = 'Disbursed' then 'Disbursed'
			when eforms.ao_id is not null and eforms.product_type='kpr' then 'Disposisi Pengajuan'
			else
			(case when eforms.product_type='briguna'  and eforms.status_eform is not null then 'MenungguPutusan' else 	'Pengajuan Kredit' end)
			end";
            $eforms = \DB::table('eforms')->selectRaw("eforms.id
                , eforms.ao_name as ao
                , concat(users.first_name, ' ', users.last_name) as nama_pemohon
                , case when eforms.product_type='kpr' then developers.company_name
                      else ''
                      end as developer_name
                 , case when eforms.product_type='kpr' then kpr.property_item_name
                      else ''
                      end as property_name
                , eforms.product_type as product_type
                , eforms.ref_number as ref_number
                , eforms.prescreening_status
                , date(eforms.created_at) as tanggal_pengajuan
                , case when eforms.product_type='kpr' then kpr.request_amount
                      else  briguna.request_amount
                      end as jumlah_pengajuan
                , " . $statusQuery . " as status
                ")
                ->leftJoin("users", "users.id", "=", "eforms.user_id")
                ->leftJoin('kpr', function($join){
                    $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                    $join->on('kpr.eform_id', '=', 'eforms.id');
                })
                ->leftJoin('briguna', function($join){
                    $join->on('eforms.product_type', '=', DB::raw("'briguna'"));
                    $join->on('briguna.eform_id', '=', 'eforms.id');
                })
                ->leftJoin('developers', function($join) {
                    $join->on('developers.user_id', '=', 'kpr.developer_id');
                    $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                })
                ->leftJoin('visit_reports', function($join) {
                    $join->on('eforms.id', '=', 'visit_reports.eform_id');
                    $join->on('eforms.product_type', '=', DB::raw("'kpr'"));
                })
                ->where( "eforms.ao_id", $request->header('pn') )
                ->where( function($item) use (&$request) {
                    if($request->has('search')){
                        $lowerSearch = '%' . strtolower($request->input('search')) . '%';
                        $item->where(\DB::raw('LOWER(users.first_name)'), 'ilike', $lowerSearch);
                        $item->Orwhere(\DB::raw('LOWER(users.last_name)'), 'ilike', $lowerSearch);
                        $item->Orwhere(\DB::raw('LOWER(kpr.property_item_name)'), 'ilike', $lowerSearch);
                        $item->Orwhere(\DB::raw('LOWER(eforms.product_type)'), 'ilike', $lowerSearch);
                        $item->Orwhere(\DB::raw('LOWER(eforms.ref_number)'), 'ilike', $lowerSearch);
                    }
                })
                ->where( function($item) use (&$request, $statusQuery) {
                    if($request->has('status')){
                        if ( $request->input('status') != "All" ) {
                            $status = 'Pengajuan Kredit';
                            if($request->input('status') == "Rejected") {
                                $status = 'Kredit Ditolak';
                            } elseif($request->input('status') == "Dispose") {
                                $status = 'Disposisi Pengajuan';
                            } elseif($request->input('status') == "Rekomend") {
                                $status = 'Pengajuan Kredit';
                            } elseif($request->input('status') == "Submit") {
                                $status = 'Proses CLS';
                            } elseif($request->input('status') == "Initiate") {
                                $status = 'Prakarsa';
                            } elseif($request->input('status') == "Approval1") {
                                $status = 'Kredit Disetujui';
                            } elseif($request->input('status') == 'Approval2') {
                                $status = 'Rekontes Kredit';
                            }elseif($request->input('status') == 'Disbursed') {
                                $status = 'Disbursed';
                            }elseif($request->input('status') == 'MenungguPutusan') {
                                $status = 'MenungguPutusan';
                            }

                            $item->whereRaw('('.$statusQuery . " = '" . $status . "')");
                        }
                    }
                })
                ->paginate( $request->input( 'limit' ) ?: 10 );

        }

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $eforms
        ], 200 );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($type,$id)
    {
        $eform = EForm::with( 'visit_report.mutation.bankstatement', 'kpr' )->find($id);

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $eform
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
