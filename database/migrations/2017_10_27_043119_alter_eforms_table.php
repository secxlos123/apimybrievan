<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eforms', function (Blueprint $table) {
            $table->text('recommendation')->nullable();
            $table->boolean('recommended')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eforms', function (Blueprint $table) {
            $table->dropColumn(['recommendation', 'recommended']);

        });
    }
}
