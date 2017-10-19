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

        		 	$id = DB::table('developers')->insertGetId(array(
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
        		  	'developer_id' => $id,		 	 
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
        		 
        		 
        		
       
        	DB::commit();
        } catch (Exception $e) {
        	dd($e);
        	DB::rollback();
        	
        }
    }
}