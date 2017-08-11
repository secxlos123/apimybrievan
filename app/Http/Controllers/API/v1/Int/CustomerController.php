<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $limit = $request->input( 'limit' ) ?: 10;
        $customers = User::getCustomers( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'customers' => $customers
        ], 200 );
    }
}
