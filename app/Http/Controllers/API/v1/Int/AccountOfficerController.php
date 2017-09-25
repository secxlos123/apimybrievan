<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountOfficerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        // $limit = $request->input( 'limit' ) ?: 10;
        // $account_officers = AccountOfficer::filter( $request )->paginate( $limit );
        $account_officers = [
            "current_page" => 1,
            "data" => [
                [
                    'id' => '1',
                    'nip' => '123',
                    'name' => 'Dummy One',
                    'position' => 'Account Officer',
                    'email' => 'dummy@one.com',
                    'gender' => 'Laki-laki',
                    'image' => url( 'img/avatar.jpg' )
                ],
                [
                    'id' => '2',
                    'nip' => '1234',
                    'name' => 'Dummy Two',
                    'position' => 'Account Officer',
                    'email' => 'dummy@two.com',
                    'gender' => 'Laki-laki',
                    'image' => url( 'img/avatar.jpg' )
                ]
            ],
            "from" => 1,
            "last_page" => 1,
            "next_page_url" => null,
            "path" => "http://api.bri.dev/api/v1/int/account-officers",
            "per_page" => 10,
            "prev_page_url" => null,
            "to" => 1,
            "total" => 1
        ];
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $account_officers
        ], 200 );
    }
}
