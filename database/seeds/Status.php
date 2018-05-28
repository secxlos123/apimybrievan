<?php

use Illuminate\Database\Seeder;

class Status extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert(array(
	        array('id' => 0, 'status_name' => 'APPROVAL'),
	        array('id' => 1, 'status_name' => 'APPROVED'),
	        array('id' => 2, 'status_name' => 'UNAPPROVED'),
	        array('id' => 3, 'status_name' => 'DITOLAK'),
	        array('id' => 4, 'status_name' => 'VOID ADK'),
            array('id' => 5, 'status_name' => 'APPROVED PENCAIRAN'),
            array('id' => 6, 'status_name' => 'DISBURSED'),
            array('id' => 7, 'status_name' => 'SENT TO BRINETS'),
            array('id' => 8, 'status_name' => 'AGREE BY MP'),
            array('id' => 9, 'status_name' => 'DITOLAK'),
            array('id' => 10, 'status_name' => 'AGREE BY AMP'),
            array('id' => 11, 'status_name' => 'AGREE BY PINCAPEM'),
            array('id' => 12, 'status_name' => 'AGREE BY PINCA'),
            array('id' => 13, 'status_name' => 'AGREE BY WAPINWIL'),
            array('id' => 14, 'status_name' => 'AGREE BY WAPINCASUS'),
            array('id' => 15, 'status_name' => 'NAIK KETINGKAT LEBIH TINGGI BY AMP'),
            array('id' => 16, 'status_name' => 'NAIK KETINGKAT LEBIH TINGGI BY MP'),
            array('id' => 17, 'status_name' => 'NAIK KETINGKAT LEBIH TINGGI BY PINCAPEM'),
            array('id' => 18, 'status_name' => 'NAIK KETINGKAT LEBIH TINGGI BY PINCA'),
            array('id' => 19, 'status_name' => 'NAIK KETINGKAT LEBIH TINGGI BY WAPINWIL'),
            array('id' => 20, 'status_name' => 'NAIK KETINGKAT LEBIH TINGGI BY WAPINCASUS'),
            array('id' => 21, 'status_name' => 'MENGEMBALIKAN DATA KE AO')
	    ));
    }
}
