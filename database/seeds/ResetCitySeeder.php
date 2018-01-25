<?php

use Illuminate\Database\Seeder;
use App\Models\City;

class ResetCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // tadi ada masalah di production, jdi bkin handlingnya
        Schema::disableForeignKeyConstraints();
        $cities = City::orderBy('id', 'asc')->get();

        foreach ($cities as $key => $city) {
            $city->update( [ 'id' => $key +1 ] );
        }
        Schema::enableForeignKeyConstraints();
    }
}
