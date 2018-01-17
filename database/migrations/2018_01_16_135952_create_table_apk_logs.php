<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableApkLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apk_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('apk_type')->nullable();
            $table->string('version_type')->nullable();//REF<00 tahun><00 bulan><00000 kode cabang><000 urut>
            $table->integer('version_number')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apk_logs');
    }
}
