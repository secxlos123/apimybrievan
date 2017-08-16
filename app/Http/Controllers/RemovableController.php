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

            if( ! Schema::hasColumns( 'users', ['gender', 'phone', 'mobile_phone', 'is_actived'] ) ) {
                Schema::table( 'users', function ( Blueprint $table ) {
                    $table->string( 'phone' )->nullable();
                    $table->string( 'mobile_phone' )->nullable();
                    $table->enum( 'gender', ['L', 'P'] )->default('L');
                    $table->boolean( 'is_actived' )->default(true);
                } );
                $update_message[] = 'Add gender, phone, mobile_phone, is_actived fields on user table!';
            }

            if( Schema::hasColumns( 'customer_details', ['gender', 'phone', 'mobile_phone'] ) ) {
                Schema::table( 'customer_details', function ( Blueprint $table ) {
                    $table->dropColumn( ['gender', 'phone', 'mobile_phone'] );
                } );
                $update_message[] = 'Remove gender, phone, mobile_phone fields on customer_details table!';
            }

            if( ! Schema::hasColumn( 'customer_details', 'nik' ) ) {
                Schema::table( 'customer_details', function ( Blueprint $table ) {
                    $table->string( 'nik' )->nullable();
                } );
                $update_message[] = 'Add nik field on customer_details table!';
            }

            if( ! Schema::hasColumn( 'eforms', 'nik' ) ) {
                Schema::table( 'eforms', function ( Blueprint $table ) {
                    $table->string( 'nik' )->nullable();
                } );
                $update_message[] = 'Add nik field on eforms table!';
            }

            if( ! Schema::hasColumn( 'eforms', 'office_id' ) ) {
                Schema::table( 'eforms', function ( Blueprint $table ) {
                    $table->integer( 'office_id' )->nullable();
                    $table->foreign( 'office_id' )->references( 'id' )->on( 'offices' )->onUpdate( 'cascade' )->onDelete( 'cascade' );
                } );
                $update_message[] = 'Add office_id field on eforms table!';
            }

            if( Schema::hasColumn( 'eforms', 'branch' ) ) {
                Schema::table( 'eforms', function ( Blueprint $table ) {
                    $table->dropColumn( 'branch' )->nullable();
                } );
                $update_message[] = 'Remove branch field on eforms table!';
            }

            if( ! Schema::hasColumn( 'roles', 'is_default' ) ) {
                Schema::table( 'roles', function ( Blueprint $table ) {
                    $table->boolean('is_default')->default(false);
                } );
                $update_message[] = 'Add is_default field on roles table!';
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
