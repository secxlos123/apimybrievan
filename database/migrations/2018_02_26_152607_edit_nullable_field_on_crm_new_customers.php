<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditNullableFieldOnCrmNewCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_new_customers', function (Blueprint $table) {
		$table->string('nik')->nullable()->change();
		$table->string('email')->nullable()->change();
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
            $table->string('nik')->unique()->change();
          $table->string('email')->unique()->change();
        });
    }
}
