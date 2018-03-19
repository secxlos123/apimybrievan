<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('uker_tables', function (Blueprint $table) {
            $table->string('id_uker');
            $table->string('kanwil');
            $table->string('unit_kerja');
            $table->string('unit_induk');
            $table->string('kanca_induk');
            $table->string('jenis_uker');
            $table->string('kode_uker');
            $table->string('dati2');
            $table->string('dati1');
            $table->string('alamat');
            $table->string('no_telp');
            $table->string('no_fax');
            $table->string('koordinat');
            $table->string('latitude');
            $table->string('longitude');
		  });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uker_tables');
    }
}
