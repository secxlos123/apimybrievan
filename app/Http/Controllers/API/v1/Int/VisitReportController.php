<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\VisitReportRequest;
use App\Models\EForm;
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
\Log::info($request->all());
	$data = $request->all();
	if (!isset($data['mutations'])){
$data['mutations'] = array();
}
        $eform = EForm::find($eform_id);
        $eform->update([
            'address' => $request->input('address')
            , 'appointment_date' => $request->input('date')
        ]);

        $visit_report = VisitReport::create( [ 'eform_id' => $eform_id ] + $data );

        DB::commit();
        return response()->success( [
            'message' => 'Data LKN berhasil dikirim',
            'contents' => $visit_report
        ], 201 );
    }
}
