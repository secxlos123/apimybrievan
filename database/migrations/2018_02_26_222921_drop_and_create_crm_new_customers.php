<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAndCreateCrmNewCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::dropIfExists('crm_new_customers');
      Schema::create('crm_new_customers', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('pn')->unsigned()->index();
          $table->integer('branch')->unsigned()->index();
          $table->string('name');
          $table->string('nik')->nullable();
          $table->string('email')->nullable();
          $table->string('phone');
          $table->string('address');
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
      Schema::dropIfExists('crm_new_customers');
      Schema::create('crm_new_customers', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('pn')->unsigned()->index();
          $table->integer('branch')->unsigned()->index();
          $table->string('name');
          $table->string('nik')->unique();
          $table->string('email')->unique();
          $table->string('phone');
          $table->string('address');
          $table->timestamps();
      });
    }
}
