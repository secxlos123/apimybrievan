<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSecondCifAndNikTypeOnMarketings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
       Schema::table('marketings', function (Blueprint $table) {
           $table->string('nik')->nullable()->change();
           $table->string('cif')->nullable()->change();
       });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
       Schema::table('marketings', function (Blueprint $table) {
           $table->string('nik')->default('null')->change();
           $table->string('cif')->default('null')->change();
       });
     }
}
