<?php

use Illuminate\Database\Seeder;
use App\Models\City;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $cities = City::orderBy('id', 'asc')->get();

        foreach ($cities as $key => $city) {
            $city->update( [ 'id' => $key +1 ] );
        }
        Schema::enableForeignKeyConstraints();

        // $file = __DIR__. '/../csv/cities.csv';
        // $data = csv_to_array($file, ['name'], ';');

        // Schema::disableForeignKeyConstraints();
        // DB::table('cities')->delete();
        // foreach(collect($data)->chunk(50) as $chunk) {
        //     \DB::table('cities')->insert($chunk->toArray());
        // }
        // Schema::enableForeignKeyConstraints();
    }
}
