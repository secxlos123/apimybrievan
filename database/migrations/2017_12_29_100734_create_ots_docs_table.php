<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->string('collateral_binding_doc')->nullable();
            $table->string('collateral_insurance_doc')->nullable();
            $table->string('life_insurance_doc')->nullable();
            $table->string('ownership_doc')->nullable();
            $table->string('building_permit_doc')->nullable();
            $table->string('sales_law_doc')->nullable();
            $table->string('property_tax_doc')->nullable();
            $table->string('sale_value_doc')->nullable();
            $table->string('progress_one_doc')->nullable();
            $table->string('progress_two_doc')->nullable();
            $table->string('progress_three_doc')->nullable();
            $table->string('progress_four_doc')->nullable();
            $table->string('progress_five_doc')->nullable();
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
        Schema::dropIfExists('ots_docs');
    }
}
