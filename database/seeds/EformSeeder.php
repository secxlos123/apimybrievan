<?php

use Illuminate\Database\Seeder;

class EformSeeder extends Seeder
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
    		$users = factory(App\Models\User::class, 5)->create();
	    	$users->each(function ($user) {
	    		$user->roles()->attach(5);
	    		$activation = Activation::create($user);
	    		Activation::complete($user, $activation->code);

	    		$eform = factory(App\Models\EForm::class, 5)->create([
	    			'user_id' => $user->id
	    		]);
	    		
	    		

	    });
   		DB::commit();
    	} catch (Exception $e) {
    		dd($e->getMessage());
            DB::rollback();
    	}
        
    }
}
