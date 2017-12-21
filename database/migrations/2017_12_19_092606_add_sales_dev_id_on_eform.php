<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalesDevIdOnEform extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add field sales_dev_id on table eforms
         Schema::table('eforms', function (Blueprint $table) {
            $table->integer('sales_dev_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop field sales_dev_id on table eforms
         Schema::table('eforms', function (Blueprint $table) {
            $table->dropColumn(['sales_dev_id']);
        });
    }
}
