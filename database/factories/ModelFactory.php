<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
	    'email' => $faker->unique()->safeEmail,
	    'password' => bcrypt('12345678'),
	    'first_name' => $faker->firstName,
	    'last_name' => $faker->lastName,
	    'phone' 	=> $faker->phoneNumber,
	    'mobile_phone' => $faker->phoneNumber,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Office::class, function (Faker\Generator $faker) {
    return [
	    'id' 	=> $faker->unique()->randomNumber(3),
	    'name'  => $faker->unique()->name,
	    'address' => $faker->address,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Developer::class, function (Faker\Generator $faker) {
    return [
    	'company_name' => $faker->name,
    	'plafond' => $faker->randomNumber(9),
    	'pks_number' => $faker->unique()->randomNumber(5),
    	'address' => $faker->address,
    	'summary' => $faker->text,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Property::class, function (Faker\Generator $faker) {
    return [
        'name'		 => $faker->name,
        'address'	 => $faker->address,
        'category'	 => $faker->randomElement([0, 1, 2]),
	    'pic_name'	 => $faker->name,
	    'pic_phone'	 => $faker->randomNumber(9),
	    'latitude'	 => $faker->latitude,
	    'longitude'	 => $faker->longitude,
        'facilities' => $faker->text,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\PropertyType::class, function (Faker\Generator $faker) {
    return [
		'name'				=> $faker->name,
		'surface_area'		=> $faker->randomNumber(3),
		'building_area'		=> $faker->randomNumber(3),
		'price'				=> $faker->randomNumber(9),
		'electrical_power'	=> $faker->randomNumber(3),
		'bathroom'			=> $faker->randomNumber(1),
		'bedroom'			=> $faker->randomNumber(1),
		'floors'			=> $faker->randomNumber(1),
		'carport'			=> $faker->randomNumber(1),
	];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\PropertyItem::class, function (Faker\Generator $faker) {
    return [
        'address'	   => $faker->address,
        'price'		   => $faker->randomNumber(9),
        'status'	   => $faker->randomElement(['new', 'second']),
    ];
});
