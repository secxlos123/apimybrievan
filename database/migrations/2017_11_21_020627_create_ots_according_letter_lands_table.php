<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsAccordingLetterLandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_according_letter_lands', function (Blueprint $table) {
            $stringType = 'string';
            $nullable = 'nullable';
            $default = 'default';
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->{$stringType}('type')->{$nullable}();
            $table->{$stringType}('authorization_land')->{$nullable}();
            $table->{$stringType}('match_bpn')->{$nullable}();
            $table->{$stringType}('match_area')->{$nullable}();
            $table->{$stringType}('match_limit_in_area')->{$nullable}();
            $table->decimal('surface_area_by_letter')->{$nullable}()->{$default}(0);
            $table->{$stringType}('number')->{$nullable}()->{$default}(0);
            $table->date('date')->{$nullable}();
            $table->{$stringType}('on_behalf_of')->{$nullable}();
            $table->date('duration_land_authorization')->{$nullable}();
            $table->{$stringType}('bpn_name')->{$nullable}();
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
        Schema::dropIfExists('ots_according_letter_lands');
    }
}
