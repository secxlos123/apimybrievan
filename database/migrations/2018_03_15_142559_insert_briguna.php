<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertBriguna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           Schema::table( 'briguna', function ( Blueprint $table ) {
            $table->text( 'gaji_pensiun' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('briguna', function (Blueprint $table) {
             $table->dropColumn('gaji_pensiun');
        });
    }
}
