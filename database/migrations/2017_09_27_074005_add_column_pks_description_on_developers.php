<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPksDescriptionOnDevelopers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'developers', function ( Blueprint $table ) {
            $table->text( 'pks_description' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'developers', function ( Blueprint $table ) {
            $table->dropColumn( 'pks_description' );
        } );
    }
}
