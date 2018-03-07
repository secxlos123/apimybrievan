<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldFotoLainnya extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('briguna', function ( Blueprint $table ) {
            $table->text('lainnya1')->nullable();
            $table->text('lainnya2')->nullable();
            $table->text('lainnya3')->nullable();
            $table->text('lainnya4')->nullable();
            $table->text('lainnya5')->nullable();
            $table->dateTime('tgl_pencairan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('briguna', function (Blueprint $table) {
            $table->dropColumn([
                'lainnya1','lainnya2','lainnya3',
                'lainnya4','lainnya5','tgl_pencairan'
            ]);
        });
    }
}
