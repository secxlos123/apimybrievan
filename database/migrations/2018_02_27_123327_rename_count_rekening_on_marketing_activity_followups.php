<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCountRekeningOnMarketingActivityFollowups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketing_activity_followups', function (Blueprint $table) {
            $table->renameColumn('count_rekening', 'account_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketing_activity_followups', function (Blueprint $table) {
            $table->renameColumn( 'account_number', 'count_rekening');
        });
    }
}
