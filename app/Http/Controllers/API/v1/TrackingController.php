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
        $eforms = array();
        if ($type == 'eks') {
            $user = $request->user();


            if( $user->inRole( 'customer' ) ) {
                $eforms = \DB::table('eforms')->selectRaw("eforms.id
                    , eforms.ao_name as ao
                    , concat(users.first_name, ' ', users.last_name) as nama_pemohon
                    , developers.company_name as developer_name
                    , kpr.property_item_name as property_name
                    , eforms.product_type as product_type
                    , date(eforms.created_at) as tanggal_pengajuan
                    , kpr.request_amount as jumlah_pengajuan
                    , case when eforms.is_approved = false and eforms.recommended = true then 'Kredit Ditolak'
                        when eforms.is_approved = true then 'Proses Analisa Pengajuan'
                        when visit_reports.id is not null then 'Proses Analisa Pengajuan'
                        when eforms.ao_id is not null then 'Pengajuan Diterima'
                        else 'Pengajuan Kredit' end as status
                    ")
                    ->leftJoin("users", "users.id", "=", "eforms.user_id")
                    ->leftJoin("kpr", "kpr.eform_id", "=", "eforms.id")
                    ->leftJoin("developers", "developers.id", "=", "kpr.developer_id")
                    ->leftJoin("visit_reports", "eforms.id", "=", "visit_reports.eform_id")
                    ->where( "eforms.user_id", $user->id )
                    ->paginate( $request->input( 'limit' ) ?: 10 );

            }

            else if( $user->inRole('developer-sales') ) {
                    $eforms = \DB::table('eforms')->selectRaw("eforms.id
                    , eforms.ao_name as ao
                    , concat(users.first_name, ' ', users.last_name) as nama_pemohon
                    , developers.company_name as developer_name
                    , kpr.property_item_name as property_name
                    , kpr.request_amount as nominal
                    , eforms.product_type as product_type
                    , eforms.ref_number as ref_number
                    , date(eforms.created_at) as tanggal_pengajuan
                    , kpr.request_amount as jumlah_pengajuan
                    , case when eforms.is_approved = false and eforms.recommended = true then 'Kredit Ditolak'
                        when eforms.is_approved = true then 'Proses Analisa Pengajuan'
                        when visit_reports.id is not null then 'Proses Analisa Pengajuan'
                        when eforms.ao_id is not null then 'Pengajuan Diterima'
                        else 'Pengajuan Kredit' end as status
                    ")
                    ->leftJoin("users", "users.id", "=", "eforms.user_id")
                    ->leftJoin("kpr", "kpr.eform_id", "=", "eforms.id")
                    ->leftJoin("developers", "developers.id", "=", "kpr.developer_id")
                    ->leftJoin("visit_reports", "eforms.id", "=", "visit_reports.eform_id")
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
                                if(strtolower($request->input('search')) == "proses analisa pengajuan")
                                {
                                    $item->Orwhere('eforms.is_approved', 'true');
                                   // $item->OrwhereNotNull('eforms.ao_id');
                                }
                                elseif(strtolower($request->input('search')) == "kredit ditolak")
                                {
                                    $item->where('eforms.is_approved', 'false');
                                    $item->where('eforms.recommended', 'true');
                                }
                                elseif(strtolower($request->input('search')) == "pengajuan diterima")
                                {
                                    $ao_id = DB::table('eforms')->selectRaw('eforms.ao_id')->get();
                                    foreach ($ao_id as $key => $value) {
                                        \Log::info("==========AO_ID=============");
                                        \Log::info($value->ao_id);
                                        $item->Orwhere('eforms.ao_id',  'like','%'.$value->ao_id.'%');
                                    }

                                }
                                elseif(strtolower($request->input('search')) == "pengajuan kredit")
                                {
                                    $item->Orwhere('eforms.ao_id',  NULL);
                                }
                            }
                    })
                    ->paginate( $request->input( 'limit' ) ?: 10 );
                // $limit = $request->input('limit') ?: 10;
                // $eforms = EForm::Tracking($request)->paginate($limit);
                // return response()->success(['contents' => $eforms]);

            }
        }
            if( $request->header('pn') ) {
                $eforms = \DB::table('eforms')->selectRaw("eforms.id
                , eforms.ao_name as ao
                , concat(users.first_name, ' ', users.last_name) as nama_pemohon
                , developers.company_name as developer_name
                , kpr.property_item_name as property_name
                , eforms.product_type as product_type
                , eforms.ref_number as ref_number
                , eforms.prescreening_status
                , date(eforms.created_at) as tanggal_pengajuan
                , kpr.request_amount as jumlah_pengajuan
                , case when (eforms.is_approved = false and eforms.recommended = true) or eforms.status_eform = 'Rejected' then 'Kredit Ditolak'
                    when eforms.status_eform = 'Approval1' then 'Kredit Disetujui'
                    when eforms.status_eform = 'Approval2' then 'Rekontes Kredit'
                    when eforms.is_approved = true then 'Proses CLF'
                    when visit_reports.id is not null then 'Prakarsa'
                    when eforms.ao_id is not null then 'Disposisi Pengajuan'
                    else 'Pengajuan Kredit' end as status
                ")
                ->leftJoin("users", "users.id", "=", "eforms.user_id")
                ->leftJoin("kpr", "kpr.eform_id", "=", "eforms.id")
                ->leftJoin("developers", "developers.id", "=", "kpr.developer_id")
                ->leftJoin("visit_reports", "eforms.id", "=", "visit_reports.eform_id")
                ->where( "eforms.ao_id", $request->header('pn') )
                ->where(function($item) use (&$request){
                        if($request->has('status')){
                            $status = 'Pengajuan Kredit';
                            if($request->input('status') == "Rejected") {
                                $status = 'Kredit Ditolak';
                            } elseif($request->input('status') == "Dispose") {
                                $status = 'Disposisi Pengajuan';
                            } elseif($request->input('status') == "Rekomend") {
                                $status = 'Pengajuan Kredit';
                            } elseif($request->input('status') == "Submit") {
                                $status = 'Proses CLF';
                            } elseif($request->input('status') == "Initiate") {
                                $status = 'Prakarsa';
                            } elseif($request->input('status') == "Approval1") {
                                $status = 'Kredit Disetujui';
                            } elseif($request->input('status') == 'Approval2') {
                                $status = 'Rekontes Kredit';
                            }

                            $item->whereRaw("case when (eforms.is_approved = false and eforms.recommended = true) or eforms.status_eform = 'Rejected' then 'Kredit Ditolak'
                                when eforms.status_eform = 'Approval1' then 'Kredit Disetujui'
                                when eforms.status_eform = 'Approval2' then 'Rekontes Kredit'
                                when eforms.is_approved = true then 'Proses CLF'
                                when visit_reports.id is not null then 'Prakarsa'
                                when eforms.ao_id is not null then 'Disposisi Pengajuan'
                                else 'Pengajuan Kredit' end", $status);
                        }
                    })
                    // ->where(function($item) use (&$request){
                    //     if ($request->has('search')){
                    //          $item->where(\DB::raw('LOWER(users.first_name)'), 'like', '%'.strtolower($request->input('search')).'%');
                    //         $item->Orwhere(\DB::raw('LOWER(users.last_name)'), 'like', '%'.strtolower($request->input('search')).'%');
                    //         $item->Orwhere(\DB::raw('LOWER(kpr.property_item_name)'), 'like', '%'.strtolower($request->input('search')).'%');
                    //         $item->Orwhere(\DB::raw('LOWER(eforms.product_type)'), 'like', '%'.strtolower($request->input('search')).'%');
                    //         $item->Orwhere(\DB::raw('LOWER(eforms.ref_number)'), 'like', '%'.strtolower($request->input('search')).'%');
                    //             if($request->input('search') == "Kredit Ditolak" || $request->input('search') == "kredit ditolak")
                    //             {
                    //                 $item->Orwhere('eforms.is_approved', 'false');
                    //                 $item->where('eforms.recommended', 'true');
                    //             }
                    //             elseif(strtolower($request->input('search')) == "pengajuan diterima")
                    //             {
                    //                 $ao_id = DB::table('eforms')->selectRaw('eforms.ao_id')->get();
                    //                 foreach ($ao_id as $key => $value) {
                    //                     \Log::info("==========AO_ID=============");
                    //                     \Log::info($value->ao_id);
                    //                     $item->Orwhere('eforms.ao_id',  'like','%'.$value->ao_id.'%');
                    //                 }
                    //             }
                    //             elseif(strtolower($request->input('search')) == "pengajuan kredit")
                    //             {
                    //                 $item->Orwhere('eforms.ao_id',  NULL);
                    //             }
                    //             elseif(strtolower($request->input('search')) == "disposisi pengajuan")
                    //             {
                    //                 $item->OrwhereNotNull('eforms.ao_id');
                    //                 $item->whereNotNull('eforms.ao_name');
                    //                 $item->whereNotNull('eforms.ao_position');
                    //                 $item->where('eforms.is_approved', 'false');
                    //                 $item->where('eforms.status_eform', NULL);
                    //                 $item->whereNull('visit_reports.created_at');
                    //             }
                    //             elseif(strtolower($request->input('search')) == "prakarsa")
                    //             {
                    //                 $item->OrwhereNotNull('visit_reports.created_at');
                    //                 $item->where('eforms.is_approved', 'false');
                    //                // $item->where('eforms.status_eform', 'approved');
                    //             }
                    //             elseif(strtolower($request->input('search')) == "proses clf")
                    //             {
                    //                 $item->OrwhereNotNull('eforms.ao_id');
                    //                 $item->whereNotNull('eforms.ao_name');
                    //                 $item->whereNotNull('eforms.ao_position');
                    //                 $item->where('eforms.is_approved', 'true');
                    //                 $item->whereNotNull('visit_reports.created_at');
                    //             }
                    //             elseif(strtolower($request->input('search')) == "kredit disetujui")
                    //             {
                    //                 $item->Orwhere('eforms.is_approved', 'true');
                    //                 $item->where('eforms.recommended', 'true');
                    //                 $item->where('eforms.status_eform', 'Approval1');
                    //             }
                    //         }
                    // })
                ->paginate( $request->input( 'limit' ) ?: 10 );
        }
        \Log::info($eforms);
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
