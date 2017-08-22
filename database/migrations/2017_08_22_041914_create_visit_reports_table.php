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
            $table->increments( 'id' );
            $table->integer( 'eform_id' );
            $table->string( 'visitor_name' );
            $table->text( 'place' );
            $table->date( 'date' );
            $table->string( 'name' );
            $table->string( 'job' );
            $table->string( 'phone' );
            $table->string( 'account' );
            $table->bigInteger( 'amount' );
            $table->string( 'type' );
            $table->string( 'purpose_of_visit' );
            $table->string( 'result' );
            $table->string( 'source' );
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
            $table->string( 'mutation_file' );
            $table->string( 'photo_with_customer' );
            $table->text( 'pros' );
            $table->text( 'cons' );
            $table->string( 'seller_name' );
            $table->string( 'seller_address' );
            $table->string( 'seller_phone' );
            $table->bigInteger( 'selling_price' );
            $table->text( 'reason_for_sale' );
            $table->string( 'relation_with_seller' );
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
