<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingDeleteRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_delete_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pn');
            $table->string('branch');
            $table->integer('marketing_id')->index()->unsigned();
            $table->enum('status',['req','deleted'])->default('req');
            $table->timestamps();

            $table->foreign('marketing_id')
                  ->references('id')
                  ->on('marketings')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketing_delete_requests');
    }
}
