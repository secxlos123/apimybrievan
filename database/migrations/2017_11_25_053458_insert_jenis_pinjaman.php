<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertJenisPinjaman extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::table('jenis_pinjaman')->insert(
		  array(
            'kode' => '0',
            'keterangan' => 'Pilih',
        )
		);
         DB::table('jenis_pinjaman')->insert(
		 array(
            'kode' => '1',
            'keterangan' => 'Pendidikan',
        )
		);
         DB::table('jenis_pinjaman')->insert(
		array(
            'kode' => '2',
            'keterangan' => 'Renovasi',
        )
		);
         DB::table('jenis_pinjaman')->insert(
		array(
            'kode' => '3',
            'keterangan' => 'Usaha',
        )
		);
         DB::table('jenis_pinjaman')->insert(
		array(
            'kode' => '4',
            'keterangan' => 'Lainnya',
        )
		);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
