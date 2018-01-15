<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ref_id')->unique()->index();//REF<00 tahun><00 bulan><00000 kode cabang><000 urut>
            $table->string('nik')->index()->default('null');
            $table->string('cif')->index()->default('null');
            $table->string('name');
            $table->string('phone')->default('null');
            $table->text('address');
            $table->string('product_type');
            $table->string('officer_ref')->index();//PERNR FO/AO
            $table->string('status')->default('ref');
            $table->string('created_by')->index();//PERNR CS
            $table->string('point')->index()->default('1');
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
        Schema::dropIfExists('referrals');
    }
}
