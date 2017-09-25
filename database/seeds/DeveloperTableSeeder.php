<?php

use Illuminate\Database\Seeder;

class DeveloperTableSeeder extends Seeder
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
	    		$user->roles()->attach(4);
	    		$city = App\Models\City::all()->random();
	    		$developer = factory(App\Models\Developer::class)->create([
                    'created_by' => '00168857',
	    			'user_id' => $user->id,
	    			'city_id' => $city->id
	    		]);

	    		$properties = factory(App\Models\Property::class, mt_rand(1, 10))->make([
	    			'city_id' => $city->id
	    		])->toArray();
	    		
	    		$developer->properties()->createMany($properties);

	    		$properties = $developer->properties->each(function ($property) {
	    			$propertyTypes = factory(App\Models\PropertyType::class, mt_rand(1, 10))->make()->toArray();
	    			$property->propertyTypes()->createMany($propertyTypes);

	    			$propertyTypes = $property->propertyTypes->each(function ($propertyType) {
	    				$propertyItems = factory(App\Models\PropertyItem::class, mt_rand(1, 10))->make()->toArray();
	    				$propertyType->propertyItems()->createMany($propertyItems);
	    			});
	    		});
	    	});
            DB::commit();
        } catch (\Exception $e) {
        	dd($e->getMessage());
            DB::rollback();
        }
    }
}
