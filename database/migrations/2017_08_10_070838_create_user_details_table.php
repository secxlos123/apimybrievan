<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('office_id')->unsigned()->nullable();
            $table->string('nip')->unique()->index();
            $table->string('position')->index();
            $table->timestamps();

            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'cascade' );
                
            $table->foreign( 'office_id' )->references( 'id' )->on( 'offices' )
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
        Schema::dropIfExists('user_details');
    }
}
