<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCustomerDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_details', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'user_id' )->unsigned();
            $table->integer( 'city_id' )->unsigned()->nullable();
            $table->string( 'nik' )->nullable();
            $table->integer( 'birth_place_id' )->nullable();
            $table->date( 'birth_date' )->nullable();
            $table->text( 'address' )->nullable();
            $table->string( 'citizenship_id' )->nullable();
            $table->integer( 'status' )->nullable();
            $table->string( 'address_status' )->nullable();
            $table->string( 'mother_name' )->nullable();
            $table->string( 'emergency_contact' )->nullable();
            $table->string( 'emergency_relation' )->nullable();
            $table->string( 'identity' )->nullable();
            $table->string( 'npwp' )->nullable();
            $table->string( 'legal_document' )->nullable();
            $table->string( 'salary_slip' )->nullable();
            $table->string( 'bank_statement' )->nullable();
            $table->string( 'family_card' )->nullable();
            $table->string( 'marrital_certificate' )->nullable();
            $table->string( 'diforce_certificate' )->nullable();
            $table->string( 'job_type_id' )->nullable();
            $table->string( 'job_id' )->nullable();
            $table->string( 'company_name' )->nullable();
            $table->string( 'job_field_id' )->nullable();
            $table->string( 'position' )->nullable();
            $table->string( 'work_duration' )->nullable();
            $table->string( 'office_address' )->nullable();
            $table->string( 'salary' )->nullable();
            $table->string( 'other_salary' )->nullable();
            $table->string( 'loan_installment' )->nullable();
            $table->string( 'dependent_amount' )->nullable();
            $table->string( 'couple_nik' )->nullable();
            $table->string( 'couple_name' )->nullable();
            $table->integer( 'couple_birth_place_id' )->nullable();
            $table->date( 'couple_birth_date' )->nullable();
            $table->string( 'couple_identity' )->nullable();
            $table->boolean( 'is_verified' )->default( false );

            $table->foreign( 'user_id' )
                ->references( 'id' )->on( 'users' )
                ->onUpdate( 'cascade' )->onDelete( 'cascade' );

            $table->foreign( 'city_id' )
                ->references( 'id' )->on( 'cities' )
                ->onUpdate( 'cascade' )->onDelete( 'set null' );

            $table->foreign( 'birth_place_id' )
                ->references( 'id' )->on( 'cities' )
                ->onUpdate( 'cascade' )->onDelete( 'set null' );

            $table->foreign( 'couple_birth_place_id' )
                ->references( 'id' )->on( 'cities' )
                ->onUpdate( 'cascade' )->onDelete( 'set null' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_details');
    }
}
