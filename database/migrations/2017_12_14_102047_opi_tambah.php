<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OpiTambah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'briguna', function ( Blueprint $table ) {
            $table->text( 'no_npwp')->nullable();
            $table->text( 'no_dan_tanggal_sk_awal')->nullable();
            $table->text( 'no_dan_tanggal_sk_akhir')->nullable();
            $table->text( 'branch_name')->nullable();
            $table->text( 'baru_atau_perpanjang')->nullable();
            $table->text( 'total_exposure')->nullable();
            $table->text( 'program_asuransi')->nullable();
            $table->text( 'kredit_take_over')->nullable();
            $table->text( 'pemrakarsa_name')->nullable();
			
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
                'no_npwp','no_dan_tanggal_sk_akhir','no_dan_tanggal_sk_awal',
                'branch_name','baru_atau_perpanjang','total_exposure',
                'program_asuransi','kredit_take_over','pemrakarsa_name'
            ]);
        });
    }
}
