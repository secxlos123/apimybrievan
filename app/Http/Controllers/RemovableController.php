<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Database\Schema\Blueprint;
use Schema;

class RemovableController extends Controller
{
    public function run( Request $request ) {
        if( $request->header( 'password' ) == 'yudi.y@smooets.com' ) {
            $update_message = [];
            if( ! Schema::hasColumn( 'users', 'image' ) ) {
                Schema::table( 'users', function ( Blueprint $table ) {
                    $table->string( 'image' )->nullable();
                } );
                $update_message[] = 'Add image field on user table!';
            }

            if( empty( $update_message ) ) {
                return response()->json( [
                    'message' => 'No update'
                ], 200 );
            } else {
                return response()->json( [
                    'message' => $update_message
                ], 200 );
            }
        } else {
            return response()->json( [
                'message' => 'Not authorized!'
            ], 400 );
        }
    }
}
