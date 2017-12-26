<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalDataChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_data_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('related_id')->nullable();
            $table->string('related_type', 100)->nullable();
            $table->integer('city_id')->nullable()->unsigned();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('CASCADE');
            $table->string('company_name', 100)->nullable();
            $table->text('summary')->nullable(); // ringkasan
            $table->string('logo')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('mobile_phone', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->text('remark')->nullable();
            $table->string('approval_by')->nullable();
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
        Schema::dropIfExists('approval_data_changes');
    }
}
