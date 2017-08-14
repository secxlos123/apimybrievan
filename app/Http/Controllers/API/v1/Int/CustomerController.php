<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\CustomerRequest;
use App\Jobs\SendPasswordEmail;
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
        $password = str_random( 8 );
        $user = Sentinel::registerAndActivate( $request->all() + [ 'password' => $password ] );
        $role = Sentinel::findRoleBySlug( 'customer' );
        $role->users()->attach( $user );
        $user = User::find( $user->id );
        dispatch( new SendPasswordEmail( $user, $password ) );
        $data = [
            'id' => $user->id,
            'email' => $user->email,
            'name'   => $user->fullname
        ];

        DB::commit();
        return response()->success( [
            'message' => 'Data nasabah berhasil ditambahkan.',
            'data' => $data
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
        $customer = User::find( $id );
        $customer->update( $request->only( [ 'first_name', 'last_name', 'phone', 'mobile_phone', 'gender' ] ) );
        $customer->updateCustomerDetail( $request->except( [ 'first_name', 'last_name', 'phone', 'mobile_phone', 'gender' ] ) );
        $customer->refresh();

        DB::commit();
        return response()->success( [
            'message' => 'Data nasabah berhasil dirubah.',
            'data' => $customer
        ] );
    }
}
