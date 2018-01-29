<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColumnOfficerRefOnReferrals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->string('officer_ref')->default('null')->nullable()->change();
            $table->string('cif')->default('null')->nullable()->change();
            $table->string('status')->default('null')->nullable()->change();
            $table->text('note')->after('status')->default('null')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referrals', function (Blueprint $table) {
          // $table->string('officer_ref')->default('null')->change();
          // $table->string('nik')->index()->default('null')->change();
          // $table->string('cif')->index()->default('null')->change();
            // $table->dropColumn('officer_ref');
            $table->dropColumn('note');
        });
    }
}
