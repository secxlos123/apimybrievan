<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\EFormRequest;
use App\Models\EForm;
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
        $eforms = EForm::paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'eforms' => $eforms
        ], 200 );
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
        $eform = EForm::create( $request->all() );
        $eform->saveImages( $request->images );

        DB::commit();
        return response()->success( [
            'message' => 'Data e-form berhasil ditambahkan.',
            'data' => $eform
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
            'data' => $eform
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
            'data' => $eform
        ], 201 );
    }
}
