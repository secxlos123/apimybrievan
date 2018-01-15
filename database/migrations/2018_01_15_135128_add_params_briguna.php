<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParamsBriguna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('briguna', function (Blueprint $table) {
            $table->text( 'pernah_pinjam')->nullable();
            $table->text( 'trans_normal_harian')->nullable();         
            $table->text( 'tgl_mulai_kerja')->nullable();           
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
                'pernah_pinjam','trans_normal_harian','tgl_mulai_kerja'
            ]);
        });
    }
}
