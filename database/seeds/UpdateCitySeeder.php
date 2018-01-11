<?php

use Illuminate\Database\Seeder;
use App\Models\City;

class UpdateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = __DIR__. '/../csv/cities.csv';
        $data = csv_to_array($file, ['name'], ';');
        foreach($data as $key => $chunk) {
            City::updateOrCreate(['id' => $key+1],$chunk);
        }
    }
}
