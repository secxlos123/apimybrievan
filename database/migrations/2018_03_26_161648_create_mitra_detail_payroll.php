<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitraDetailPayroll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create( 'mitra_detail_payroll', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'payroll' );
            $table->text( 'no_rek_mitra1' );
            $table->text( 'no_cif_mitra' );
            $table->text( 'tipe_account1' );
            $table->text( 'tgl_pembayaran' );
            $table->text( 'tgl_gajian1' );
            $table->text( 'id_header' );
            $table->text( 'bank_payroll' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists( 'mitra_detail_payroll' );
    }
}








