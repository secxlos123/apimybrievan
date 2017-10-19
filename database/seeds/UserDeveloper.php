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

        		 	$user = APP\Models\User::create(array(
        		 	'email' => 'mybri@bri.co.id',
                    'password'=> bcrypt('12345678'),
                    'first_name'=>'INDEPENDENT',
                    'last_name' => 'BRI'
                    ));
                    $user->roles()->attach(4);
                    $activation = Activation::create($user);
                    Activation::complete($user, $activation->code);

                    $city = App\Models\City::all()->random();
                    $developer = App\Models\Developer::create(array(
                        'user_id' => $user->id,
                        'dev_id_bri' => 1,  
                        'company_name' => "INDEPENDENT", 
                        'created_by'=>  "", 
                        'pks_number'=> "", 
                        'plafond'=>"0", 
                        'address'=>"", 
                        'summary'=>"", 
                        'is_approved'=> "t" 

                    ));
                    $properties = App\Models\Property::create(array(
                    'developer_id' => $developer->id,           
                    'name' => "INDEPENDENT",
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