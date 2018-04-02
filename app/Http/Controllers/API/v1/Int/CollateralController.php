<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Collateral;

class CollateralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
    	$limit = $request->input('limit') ?: 10;
        $collateral = Collateral::GetLists($request)->paginate($limit);
        return response()->success([
            'contents' => $collateral,
        ], 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $collateral = Collateral::GetDetails($id);
        return response()->success([
            'contents' => $collateral,
        ], 200);

    }
}
