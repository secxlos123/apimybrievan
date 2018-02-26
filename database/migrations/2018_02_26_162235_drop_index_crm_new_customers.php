<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropIndexCrmNewCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_new_customers', function (Blueprint $table) {
        	$table->dropUnique('nik');
		$table->dropUnique('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_new_customers', function (Blueprint $table) {
		$table->unique('nik');
		$table->unique('email');
        });
    }
}
