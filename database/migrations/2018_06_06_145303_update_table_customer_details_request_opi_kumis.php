<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableCustomerDetailsRequestOpiKumis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table( 'customer_details', function ( Blueprint $table ) {
            $table->text( 'identity_selfie')->nullable();
            $table->text( 'couple_identity_selfie')->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
		Schema::table('customer_details', function (Blueprint $table) {
            $table->dropColumn(['identity_selfie', 'couple_identity_selfie']);
        });
    }
}
