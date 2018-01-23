<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('marketing_id')->unsigned()->index();
            $table->string('pn')->index();
            $table->string('pn_name')->index();
            $table->text('note');
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
        Schema::dropIfExists('marketing_notes');
    }
}
