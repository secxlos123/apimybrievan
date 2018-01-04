<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AswinTambahLagi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'briguna', function ( Blueprint $table ) {
            $table->text( 'npl_instansi')->nullable();
            $table->text( 'npl_unitkerja')->nullable();			
            $table->text( 'gimmick')->nullable();			
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('briguna', function (Blueprint $table) {
            $table->dropColumn([
                'npl_instansi','npl_unitkerja','gimmick'
            ]);
        });
    }
}
