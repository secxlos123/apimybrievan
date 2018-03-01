<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPosCodeOnMarketMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('market_mappings', function (Blueprint $table) {
            $table->string('pos_code')->nullable()->default('null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('market_mappings', function (Blueprint $table) {
          $table->dropColumn('pos_code');
        });
    }
}
