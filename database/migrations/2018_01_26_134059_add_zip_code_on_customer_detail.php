<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZipCodeOnCustomerDetail extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('customer_details', function (Blueprint $table) {
			$table->string('zip_code')->nullable();
			$table->string('kelurahan')->nullable();
			$table->string('kecamatan')->nullable();
			$table->string('kabupaten')->nullable();
			$table->string('zip_code_current')->nullable();
			$table->string('kelurahan_current')->nullable();
			$table->string('kecamatan_current')->nullable();
			$table->string('kabupaten_current')->nullable();
			$table->string('zip_code_office')->nullable();
			$table->string('kelurahan_office')->nullable();
			$table->string('kecamatan_office')->nullable();
			$table->string('kabupaten_office')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('customer_details', function (Blueprint $table) {
			$table->dropColumn(['zip_code', 'kelurahan', 'kecamatan', 'kabupaten', 'zip_code_current', 'kelurahan_current', 'kecamatan_current', 'kabupaten_current', 'zip_code_office', 'kelurahan_office', 'kecamatan_office', 'kabupaten_office']);
		});
	}
}
