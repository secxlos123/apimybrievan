<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGimmickPemutus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create( 'gimmick_pemutus', function ( Blueprint $table ) {
            $table->integer( 'id_gimmick' )->nullable();
            $table->text( 'pemutus_name' )->nullable();
            $table->text( 'jabatan' )->nullable();
			$table->foreign('id_gimmick')->references('id_gimmick')->on('gimmick_kredit');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'gimmick_pemutus' );
    }

}
