<?php

use Illuminate\Database\Seeder;

class UserDeveloper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
        		$dev = DB::table('developers')->where('id', '=', 1)->get();
        		
        		 if (count($dev) > 1) {
        		 	DB::table('developers')->where('id', '=', 1)
					->update(array(
        		 	'id'=> 1,
        		 	'dev_id_bri' => 1, 
        		 	'company_name' => "TIDAK ADA",
        			'created_by'=>	"",
        			'pks_number'=> "",
        			'plafond'=>"0",
        			'address'=>"",
        			'summary'=>"",
        			'is_approved'=> "t"
        		));
					DB::table('properties')->where('id', '=', 1)
					->update(array(
        		  	'id'=> 1,
        		  	'developer_id' => 1,		 	 
        		  	'name' => "TIDAK MENGIKUTI PROJECT",
        		  	'pic_name'=>"",
        		  	'pic_phone'=>"",
        		  	'address'=>"",
        		  	'category'=>0,
        		  	'latitude'=>"",
        		  	'longitude'=>"",
        		  	'description'=>"",
        		  	'facilities'=>"",
        		  	'slug'=>0,
        		  	'is_approved'=> "t"
        		  ));
        		 }
        		 else
        		 {
        		 	DB::table('developers')->insert(array(
        		 	'id'=> 1,
        		 	'dev_id_bri' => 1, 
        		 	'company_name' => "TIDAK ADA",
        			'created_by'=>	"",
        			'pks_number'=> "",
        			'plafond'=>"0",
        			'address'=>"",
        			'summary'=>"",
        			'is_approved'=> "t"
        		));
					DB::table('properties')->insert(array(
        		  	'id'=> 1,
        		  	'developer_id' => 1,		 	 
        		  	'name' => "TIDAK MENGIKUTI PROJECT",
        		  	'pic_name'=>"",
        		  	'pic_phone'=>"",
        		  	'address'=>"",
        		  	'category'=>0,
        		  	'latitude'=>"",
        		  	'longitude'=>"",
        		  	'description'=>"",
        		  	'facilities'=>"",
        		  	'slug'=>0,
        		  	'is_approved'=> "t"
        		  ));
        		 }
        		 
        		
       
        	DB::commit();
        } catch (Exception $e) {
        	dd($e);
        	DB::rollback();
        	
        }
    }
}