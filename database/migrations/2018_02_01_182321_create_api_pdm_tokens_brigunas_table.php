<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiPdmTokensBrigunasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_pdm_tokens_brigunas', function (Blueprint $table) {           
            $table->increments('id');
            $table->string('access_token');
            $table->string('expires_in');
            $table->string('token_type');
            $table->string('scope');
            $table->string('clientid');
            $table->string('clientsecret');
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
        Schema::dropIfExists('api_pdm_tokens_brigunas');
    }
}
