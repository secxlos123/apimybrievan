<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDataTypePopertyType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE property_types ALTER surface_area TYPE BIGINT USING surface_area::bigint ");
        \DB::statement("ALTER TABLE property_types ALTER building_area TYPE BIGINT USING building_area::bigint ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_types', function (Blueprint $table) {
            $table->string('surface_area')->nullable()->change();
            $table->string('building_area')->nullable()->change();
        });
    }
}
