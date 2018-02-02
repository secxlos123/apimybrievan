<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketCustomerMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_customer_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_name');
            $table->string('cif')->nullable()->index();
            $table->string('nik')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->integer('market_mapping_id')->unsigned()->index();
            $table->string('created_by')->index();//pn_creator
            $table->string('creator_name')->index();//creator name
            $table->string('branch')->index();//branch_creator
            $table->string('uker')->index();//branch_creator
            $table->timestamps();

            $table->foreign('market_mapping_id')->references('id')->on('market_mappings')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_customer_mappings');
    }
}
