<?php

use Illuminate\Database\Seeder;
use App\Models\Offices;

class MoveCities extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$city = [152 => 433,153 => 434 ,154 => 435, 519 => 152,520 => 153 ,521 => 154 ];
    	foreach ($city as $key => $value) {
        	\DB::table('customer_details')->where('city_id',$key)->update(['city_id' => $value]);
        	\DB::table('customer_details')->where('birth_place_id',$key)->update(['birth_place_id' => $value]);
        	\DB::table('customer_details')->where('couple_birth_place_id',$key)->update(['couple_birth_place_id' => $value]);
        	\DB::table('developers')->where('city_id', $key)->update(['city_id' => $value]);
        	\DB::table('properties')->where('city_id', $key)->update(['city_id' => $value]);
        	\DB::table('temp_users')->where('city_id', $key)->update(['city_id' => $value]);
        	\DB::table('third_parties')->where('city_id', $key)->update(['city_id' => $value]);
        	\DB::table('ots_in_areas')->where('city_id', $key)->update(['city_id' => $value]);
        	\DB::table('approval_data_changes')->where('city_id', $key)->update(['city_id' => $value]);
    	}
    	

    }
}
