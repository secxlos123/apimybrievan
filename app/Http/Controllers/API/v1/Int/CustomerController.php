<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\CustomerRequest;
use App\Jobs\SendPasswordEmail;
use App\Models\Customer;
use App\Models\User;
use Sentinel;
use DB;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\CustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( CustomerRequest $request )
    {
        DB::beginTransaction();
        $customer = Customer::create( $request->all() );
        dispatch( new SendPasswordEmail( $customer, '$password' ) );

        DB::commit();
        return response()->success( [
            'message' => 'Data nasabah berhasil ditambahkan.',
            'data' => $customer
        ], 201 );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\CustomerRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update( CustomerRequest $request, $id )
    {
        DB::beginTransaction();
        $customer = Customer::find( $id );
        $customer->update( $request->all() );

        DB::commit();
        return response()->success( [
            'message' => 'Data nasabah berhasil dirubah.',
            'data' => $customer
        ] );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $customer = Customer::find( $id );
        return response()->success( [
            'message' => 'Sukses',
            'data' => $customer
        ], 200 );
    }
}
