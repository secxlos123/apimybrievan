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
        $eforms = EForm::filter( $request )->orderBy( 'id', 'desc' )->paginate( $limit );
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
        $eform = EForm::with( 'visit_report' )->findOrFail( $eform_id );
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
        $eform = EForm::findOrFail( $request->id );
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
        $eform = EForm::findOrFail( $id );
        $ao_id = substr( '00000000' . $request->ao_id, -8 );
        $eform->update( [ 'ao_id' => $ao_id ] );

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
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function approve( EFormRequest $request, $eform_id )
    {
        DB::beginTransaction();
        $eform = EForm::approve( $eform_id, $request );
        if( $eform instanceof EForm ) {
            DB::commit();
            return response()->success( [
                'message' => 'E-form berhasil diapprove.',
                'contents' => $eform
            ], 201 );
            // event( new Approved( $eform ) );
        } else {
            DB::rollback();
            return response()->success( [
                'message' => 'E-form gagal diapprove.',
                'contents' => $eform
            ], 404 );
        }
    }

    /**
     * Insert data to core BRI.
     *
     * @param integer $step_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function insertCoreBRI( Request $request, $eform_id, $step_id )
    {
        DB::beginTransaction();
        $eform = EForm::findOrFail( $eform_id );
        $result = $eform->insertCoreBRI( $step_id );

        DB::commit();
        dd( $result );
    }
}
