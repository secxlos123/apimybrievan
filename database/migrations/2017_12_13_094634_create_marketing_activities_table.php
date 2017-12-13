<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pn')->index();
            $table->string('object_activity')->index();
            $table->string('action_activity')->index();
            $table->string('start_date');
            $table->string('end_date');
            $table->text('longitude');
            $table->text('latitude');
            $table->integer('marketing_id')->unsigned()->index();
            $table->string('pn_join');
            $table->text('desc');
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
        Schema::dropIfExists('marketing_activities');
    }
}
