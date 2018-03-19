<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertJenisPinjaman3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table( 'jenis_pinjaman', function ( Blueprint $table ) {
            $table->text( 'id' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('jenis_pinjaman', function (Blueprint $table) {
             $table->dropColumn('id');
        });
    }
}
