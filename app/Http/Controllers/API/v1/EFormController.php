<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\EFormRequest;
use App\Events\EForm\Approved;
use App\Models\EForm;
use App\Models\KPR;
use DB;

class EFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $limit = $request->input( 'limit' ) ?: 10;
        $eforms = EForm::orderBy( 'id', 'desc' )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $eforms
        ], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  string $type
     * @param  integer $eform_id
     * @return \Illuminate\Http\Response
     */
    public function show( $type, $eform_id )
    {
        $eform = EForm::with( 'visit_report' )->find( $eform_id );
        return response()->success( [
            'contents' => $eform
        ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( EFormRequest $request )
    {
        DB::beginTransaction();
        $kpr = KPR::create( $request->all() );

        DB::commit();
        return response()->success( [
            'message' => 'Data e-form berhasil ditambahkan.',
            'contents' => $kpr
        ], 201 );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function submitScreening( EFormRequest $request )
    {
        DB::beginTransaction();
        $eform = EForm::find( $request->id );
        $eform->update( [ 'prescreening_status' => $request->prescreening_status ] );

        DB::commit();
        return response()->success( [
            'message' => 'Screening e-form berhasil disimpan.',
            'contents' => $eform
        ], 201 );
    }

    /**
     * Set E-Form AO disposition.
     *
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function disposition( EFormRequest $request, $id )
    {
        DB::beginTransaction();
        $eform = EForm::find( $id );
        $eform->update( [ 'ao_id' => $request->ao_id ] );

        DB::commit();
        return response()->success( [
            'message' => 'Disposisi e-form berhasil disimpan.',
            'contents' => $eform
        ], 201 );
    }

    /**
     * Set E-Form AO disposition.
     *
     * @param integer $eform_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function approve( Request $request, $eform_id )
    {
        DB::beginTransaction();
        $eform = EForm::find( $eform_id );
        $eform->update( [ 'is_approved' => true ] );
        event( new Approved( $eform ) );

        DB::commit();
        return response()->success( [
            'message' => 'E-form berhasil diapprove.',
            'contents' => $eform
        ], 201 );
    }
}
