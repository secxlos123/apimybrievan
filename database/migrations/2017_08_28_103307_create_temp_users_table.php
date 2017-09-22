<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Temporary table developer before update
         */
        Schema::create('temp_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('city_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('company_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('image')->nullable();
            $table->text('address');
            $table->text('summary');
            $table->timestamps();

            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'cascade' );

            $table->foreign( 'city_id' )->references( 'id' )->on( 'cities' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'set null' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_users');
    }
}
