<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'briguna', function ( Blueprint $table ) {
            $table->text( 'jumlah_pekerja')->nullable();
            $table->text( 'jumlah_debitur')->nullable();         
            $table->text( 'scoring_mitra')->nullable();           
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
            $table->dropColumn([
                'jumlah_debitur','jumlah_pekerja','scoring_mitra'
            ]);
        });
    }
}
