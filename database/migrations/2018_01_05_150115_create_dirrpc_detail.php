<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirrpcDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'dirrpc_detail', function ( Blueprint $table ) {
            $table->increments( 'no' );
            $table->text( 'id_header' )->nullable();
            $table->text( 'penghasilan_maksimal' )->nullable();
            $table->text( 'penghasilan_minimal' )->nullable();
            $table->text( 'dir_persen' )->nullable();
            $table->text( 'payroll' )->nullable();
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
        Schema::dropIfExists('dirrpc_detail');
    }
}
