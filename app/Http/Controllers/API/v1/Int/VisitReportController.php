<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\VisitReportRequest;
use App\Models\VisitReport;
use DB;

class VisitReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param integer $eform_id
     * @param  \App\Http\Requests\API\v1\VisitReportRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( $eform_id, VisitReportRequest $request )
    {
        DB::beginTransaction();
        $visit_report = VisitReport::create( [ 'eform_id' => $eform_id ] + $request->all() );

        DB::commit();
        return response()->success( [
            'message' => 'Data LKN berhasil ditambahkan.',
            'contents' => $visit_report
        ], 201 );
    }
}
