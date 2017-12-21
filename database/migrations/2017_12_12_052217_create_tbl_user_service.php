<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblUserService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_services', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('pn')->default(0);
          $table->integer('branch_id')->default(0);
          $table->integer('hilfm')->default(0);
          $table->string('role')->nullable();
          $table->string('name')->nullable();
          $table->string('tipe_uker')->nullable();
          $table->string('htext')->nullable();
          $table->string('posisi')->nullable();
          $table->datetime('last_activity')->nullable();
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
        Schema::dropIfExists('user_services');
    }
}
