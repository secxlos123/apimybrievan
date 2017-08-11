<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Database\Schema\Blueprint;
use Schema;

class RemovableController extends Controller
{
    public function run( Request $request ) {
        if( $request->header( 'password' ) == 'yudi.y@smooets.com' ) {
            if( ! Schema::hasColumn( 'users', 'city' ) ) {
                Schema::table( 'users', function ( Blueprint $table ) {
                    $table->string( 'birth_place' )->nullable();
                    $table->date( 'birth_date' )->nullable();
                    $table->text( 'address' )->nullable();
                    $table->text( 'gender' )->nullable();
                    $table->text( 'city' )->nullable();
                    $table->text( 'phone' )->nullable();
                } );
                return response()->json( [
                    'message' => 'User table updated!'
                ], 200 );
            } else {
                return response()->json( [
                    'message' => 'No update'
                ], 200 );
            }
        } else {
            return response()->json( [
                'message' => 'Not authorized!'
            ], 400 );
        }
    }
}
