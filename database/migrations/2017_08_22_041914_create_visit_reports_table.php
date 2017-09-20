<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'visit_reports', function ( Blueprint $table ) {
            $table->increments( 'id' )->nullable();
            $table->integer( 'eform_id' )->nullable();
            $table->string( 'visitor_name' )->nullable();
            $table->text( 'place' )->nullable();
            $table->date( 'date' )->nullable();
            $table->string( 'name' )->nullable();
            $table->string( 'job' )->nullable();
            $table->string( 'phone' )->nullable();
            $table->string( 'account' )->nullable();
            $table->bigInteger( 'amount' )->nullable();
            $table->string( 'type' )->nullable();
            $table->string( 'purpose_of_visit' )->nullable();
            $table->string( 'result' )->nullable();
            $table->string( 'source' )->nullable();
            $table->bigInteger( 'income' )->nullable();
            $table->bigInteger( 'income_salary' )->nullable();
            $table->bigInteger( 'income_allowance' )->nullable();
            $table->string( 'income_mutation_type' )->nullable();
            $table->bigInteger( 'income_mutation_number' )->nullable();
            $table->string( 'income_salary_image' )->nullable();
            $table->bigInteger( 'business_income' )->nullable();
            $table->string( 'business_mutation_type' )->nullable();
            $table->bigInteger( 'bussiness_mutation_number' )->nullable();
            $table->string( 'bussiness_other' )->nullable();
            $table->string( 'mutation_file' )->nullable();
            $table->string( 'photo_with_customer' )->nullable();
            $table->text( 'pros' )->nullable();
            $table->text( 'cons' )->nullable();
            $table->string( 'seller_name' )->nullable();
            $table->string( 'seller_address' )->nullable();
            $table->string( 'seller_phone' )->nullable();
            $table->bigInteger( 'selling_price' )->nullable();
            $table->text( 'reason_for_sale' )->nullable();
            $table->string( 'relation_with_seller' )->nullable();
            $table->timestamps();

            $table->foreign( 'eform_id' )->references( 'id' )->on( 'eforms' )->onUpdate( 'cascade' )->onDelete( 'cascade' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'visit_reports' );
    }
}
