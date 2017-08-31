<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevelopersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('developers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('city_id')->unsigned()->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('approved_by')->unsigned()->nullable();
            $table->string('company_name')->index();
            $table->string('pks_number');
            $table->string('plafond');
            $table->text('address');
            $table->text('summary');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('created_by')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('approved_by')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('city_id')->references('id')->on('cities')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('developers');
    }
}
