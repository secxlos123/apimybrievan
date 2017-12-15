<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingActivityFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_activity_followups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->unsigned()->index();
            $table->text('desc');
            $table->string('fu_result');
            $table->string('count_rekening')->nullable();
            $table->integer('amount')->nullable();
            $table->string('target_commitment_date')->nullable();
            $table->timestamps();

            $table->foreign('activity_id')
                  ->references('id')
                  ->on('marketing_activities')
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
        Schema::dropIfExists('marketing_activity_followups');
    }
}
