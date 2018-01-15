<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingFieldOtsNine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ots_nines', function (Blueprint $table) {
            $table->date('receipt_date_notary')->nullable();
            $table->text('information_notary')->nullable();
            $table->date('receipt_date_takeover')->nullable();
            $table->text('information_takeover')->nullable();
            $table->date('receipt_date_credit')->nullable();
            $table->text('information_credit')->nullable();
            $table->date('receipt_date_skmht')->nullable();
            $table->text('information_skmht')->nullable();
            $table->date('receipt_date_imb')->nullable();
            $table->text('information_imb')->nullable();
            $table->date('receipt_date_shgb')->nullable();
            $table->text('information_shgb')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ots_nines', function (Blueprint $table) {
            $table->dropColumn(['receipt_date_notary','information_notary','receipt_date_takeover','information_takeover','receipt_date_credit','information_credit','receipt_date_skmht','information_skmht','receipt_date_imb','information_imb','receipt_date_shgb','information_shgb']);
        });
    }
}
