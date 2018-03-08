<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKartuKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    
    public function up()
    {
        Schema::create('kartu_kredit_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id');
            $table->string('hp');
            $table->string('email');
            
            
            $table->string('jenis_kelamin');

            $table->string('nama');

            $table->string('telephone');
            $table->string('pendidikan');

            $table->string('pekerjaan');
            $table->string('tiering_gaji');
            $table->string('agama');
            $table->string('jenis_nasabah');
            $table->string('pilihan_kartu');
            $table->string('penghasilan_perbulan');
            $table->string('jumlah_penerbit_kartu');
            $table->boolean('memiliki_kk_bank_lain')->default(false);
            $table->string('limit_tertinggi');
            $table->string('image_npwp');
            $table->string('image_ktp');
            $table->string('image_slip_gaji');
            $table->string('image_nametag');
            $table->string('image_kartu_bank_lain');

            // $table->foreign('customer_id')
            // ->references('id')
            // ->on('customer_details')
            // ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kartu_kredit_details');
    }
}
