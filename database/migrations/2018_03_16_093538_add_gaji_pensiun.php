<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGajiPensiun extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('briguna', function ( Blueprint $table ) {
            $table->double('gaji_bersih_pensiun')->nullable();
            $table->double('Pendapatan_profesi_pensiun')->nullable();
            $table->double('Potongan_per_bulan_pensiun')->nullable();
            $table->double('Maksimum_plafond_pensiun')->nullable();
            $table->double('Maksimum_angsuran_pensiun')->nullable();
            $table->double('Maksimum_plafond_diberikan')->nullable();
            $table->string('jenis_rekening')->nullable();
            $table->string('nama_bank_lain')->nullable();
            $table->string('nama_bank_lain_name')->nullable();
            $table->string('Sektor_ekonomi_sid_name')->nullable();
        });
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
                'gaji_bersih_pensiun', 'Pendapatan_profesi_pensiun',
                'Potongan_per_bulan_pensiun', 'Maksimum_plafond_pensiun',
                'Maksimum_angsuran_pensiun', 'Maksimum_plafond_diberikan',
                'jenis_rekening', 'nama_bank_lain','nama_bank_lain_name',
                'Sektor_ekonomi_sid_name'
            ]);
        });
    }
}
