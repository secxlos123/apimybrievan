<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGimmickKredit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
    {
         Schema::create( 'gimmick_kredit', function ( Blueprint $table ) {
            $table->increments( 'id_gimmick' );
            $table->text( 'gimmick_name' )->nullable();
            $table->text( 'gimmick_level' )->nullable();
            $table->text( 'area_level' )->nullable();
            $table->text( 'segmen_level' )->nullable();
            $table->text( 'mitra_kerjasama' )->nullable();
            $table->text( 'mitra_kerjasama2,' )->nullable();
            $table->text( 'mitra_kerjasama3' )->nullable();
            $table->text( 'mitra_kerjasama4' )->nullable();
            $table->text( 'tgl_mulai' )->nullable();
            $table->text( 'tgl_berakhir' )->nullable();
            $table->text( 'payroll' )->nullable();
            $table->text( 'admin_fee' )->nullable();
            $table->text( 'admin_minimum' )->nullable();
            $table->text( 'provisi_fee' )->nullable();
            $table->text( 'waktu_minimum' )->nullable();
            $table->text( 'waktu_maksimum' )->nullable();
            $table->text( 'dir_rpc' )->nullable();
            $table->text( 'asuransi_jiwa' )->nullable();
            $table->text( 'perhitungansuku_bunga' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'gimmick_kredit' );
    }
}
