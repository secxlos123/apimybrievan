<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCollaterals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collaterals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('developer_id')->nullable()->unsigned();
            $table->foreign('developer_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->integer('property_id')->nullable()->unsigned();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('CASCADE');
            $table->integer('staff_id')->nullable();
            $table->string('staff_name')->nullable();
            $table->string('status', 50)->nullable();
            $table->text('remark')->nullable();
            $table->string('approved_by')->nullable();
            // $table->foreign('approved_by')->references('id')->on('users')->onDelete('CASCADE');
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
        Schema::dropIfExists('collaterals');
    }
}
