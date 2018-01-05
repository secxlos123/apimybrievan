<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MitraRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create( 'mitra_relation', function ( Blueprint $table ) {
            $table->increments( 'kode' );
			$table->text( 'BRANCH_CODE' )->nullable();
			$table->text( 'NAMA_INSTANSI' )->nullable();
			$table->text( 'idMitrakerja' )->nullable();
			$table->text( 'segmen' )->nullable();
			$table->foreign( 'kode' )->references( 'kode' )->on( 'mitra_relation' );
        } );

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mitra_relation', function (Blueprint $table) {
            $table->dropColumn([
                'BRANCH_CODE','NAMA_INSTANSI','idMitrakerja','segmen','kode'
            ]);
        });
    }
}
