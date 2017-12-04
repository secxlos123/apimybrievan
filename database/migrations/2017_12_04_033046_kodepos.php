<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Kodepos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::dropIfExists( 'tbl_kodepos' );
    }
}
