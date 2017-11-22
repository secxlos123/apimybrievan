<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\EFormRequest;
use App\Events\EForm\Approved;
use App\Models\Screening;
use App\Models\KPR;
use DB;

class PrescreeningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $limit = $request->input( 'limit' ) ?: 10;
        $screening = Screening::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $screening
        ], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  string $type
     * @param  integer $eform_id
     * @return \Illuminate\Http\Response
     */
}
