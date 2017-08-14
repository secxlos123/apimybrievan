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
            } else if( ! Schema::hasColumn( 'users', 'citizenship' ) ) {
                Schema::table( 'users', function ( Blueprint $table ) {
                    $table->string( 'citizenship' )->nullable();
                    $table->integer( 'status' )->nullable();
                    $table->string( 'address_status' )->nullable();
                    $table->string( 'mother_name' )->nullable();
                    $table->string( 'mobile_phone' )->nullable();
                    $table->string( 'emergency_contact' )->nullable();
                    $table->string( 'emergency_relation' )->nullable();
                    $table->string( 'identity' )->nullable();
                    $table->string( 'npwp' )->nullable();
                    $table->string( 'image' )->nullable();
                    $table->string( 'work_type' )->nullable();
                    $table->string( 'work' )->nullable();
                    $table->string( 'company_name' )->nullable();
                    $table->string( 'work_field' )->nullable();
                    $table->string( 'position' )->nullable();
                    $table->string( 'work_duration' )->nullable();
                    $table->string( 'office_address' )->nullable();
                    $table->string( 'salary' )->nullable();
                    $table->string( 'other_salary' )->nullable();
                    $table->string( 'loan_installment' )->nullable();
                    $table->string( 'dependent_amount' )->nullable();
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
