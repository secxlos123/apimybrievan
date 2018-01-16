<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('nik')->index();
            $table->string('cif')->index();
            $table->string('category')->index();
            $table->integer('map_id')->unsigned()->index();
            $table->foreign('map_id')
                  ->references('id')
                  ->on('marketing_maps')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('created_by')->index();//pn AO/FO
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
        Schema::dropIfExists('customer_groups');
    }
}
