<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\CustomerRequest;
use App\Models\User;
use Sentinel;

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
        $user = Sentinel::registerAndActivate( $request->all() );
        $role = Sentinel::findRoleBySlug( 'customer' );
        $role->users()->attach( $user );
        $data = [
            'user_id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'fullname'   => $user->fullname,
            'role' => $user->roles->first()->slug,
            'permission' => $user->roles->first()->permissions
        ];

        return response()->success( [
            'message' => 'Data customer berhasil ditambahkan.',
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
        $customer = User::find( $id );
        $customer->update( $request->input() );
        return response()->success( [
            'message' => 'Data role berhasil dirubah.',
            'data' => $customer
        ] );
    }
}
