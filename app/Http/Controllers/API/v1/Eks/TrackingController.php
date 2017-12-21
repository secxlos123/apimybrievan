<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EForm;

class TrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $eforms = array();

        if( $user->inRole( 'customer' ) ) {
            $eforms = EForm::selectRaw("eforms.id
                , eforms.ao_name as ao
                , concat(users.first_name, ' ', users.last_name) as nama_pemohon
                , developers.company_name as developer_name
                , kpr.property_item_name as property_name
                , eforms.product_type as product_type
                , case when eforms.is_approved = false and eforms.recommended = true then 'Kredit Ditolak'
                    when eforms.is_approved = true then 'Proses CLF'
                    when visit_reports.id is not null then 'Prakarsa'
                    when eforms.ao_id is not null then 'Disposisi Pengajuan'
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
                $eforms = EForm::selectRaw("eforms.id
                , eforms.ao_name as ao
                , concat(users.first_name, ' ', users.last_name) as nama_pemohon
                , developers.company_name as developer_name
                , kpr.property_item_name as property_name
                , eforms.product_type as product_type
                , eforms.ref_number as ref_number
                , case when eforms.is_approved = false and eforms.recommended = true then 'Kredit Ditolak'
                    when eforms.is_approved = true then 'Proses CLF'
                    when visit_reports.id is not null then 'Prakarsa'
                    when eforms.ao_id is not null then 'Disposisi Pengajuan'
                    else 'Pengajuan Kredit' end as status
                ")
                ->leftJoin("users", "users.id", "=", "eforms.user_id")
                ->leftJoin("kpr", "kpr.eform_id", "=", "eforms.id")
                ->leftJoin("developers", "developers.id", "=", "kpr.developer_id")
                ->leftJoin("visit_reports", "eforms.id", "=", "visit_reports.eform_id")
                ->where( "eforms.sales_dev_id", $user->id )
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
    public function show($id)
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
