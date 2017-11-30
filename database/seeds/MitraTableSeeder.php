<?php

use Illuminate\Database\Seeder;

class MitraTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
                $file = __DIR__. '/../csv/mitrakerjasama.csv';
        $data = csv_to_array($file, ['BRANCH_CODE']);
        $mitrakerjasama = DB::table('mitra')->where('BRANCH_CODE', $data[0]['BRANCH_CODE'])->first();
        if ( !$mitrakerjasama ) {
            foreach(collect($data)->chunk(50) as $chunk) {
                \DB::table('mitra')->insert($chunk->toArray());
            }
        }
		$data = csv_to_array($file, ['NAMA_INSTANSI']);
        $mitrakerjasama = DB::table('mitra')->where('NAMA_INSTANSI', $data[1]['NAMA_INSTANSI'])->first();
        if ( !$mitrakerjasama ) {
            foreach(collect($data)->chunk(50) as $chunk) {
                \DB::table('mitra')->insert($chunk->toArray());
            }
        }
		$data = csv_to_array($file, ['idMitrakerja']);
        $mitrakerjasama = DB::table('mitra')->where('idMitrakerja', $data[2]['idMitrakerja'])->first();
        if ( !$mitrakerjasama ) {
            foreach(collect($data)->chunk(50) as $chunk) {
                \DB::table('mitra')->insert($chunk->toArray());
            }
        }
		$data = csv_to_array($file, ['segmen']);
        $mitrakerjasama = DB::table('mitra')->where('segmen', $data[3]['segmen'])->first();
        if ( !$mitrakerjasama ) {
            foreach(collect($data)->chunk(50) as $chunk) {
                \DB::table('segmen')->insert($chunk->toArray());
            }
        }
		
		
		$data = csv_to_array($file, ['UNIT_KERJA']);
        $mitrakerjasama = DB::table('mitra')->where('UNIT_KERJA', $data[4]['UNIT_KERJA'])->first();
        if ( !$mitrakerjasama ) {
            foreach(collect($data)->chunk(50) as $chunk) {
                \DB::table('UNIT_KERJA')->insert($chunk->toArray());
            }
        }
    }
}
