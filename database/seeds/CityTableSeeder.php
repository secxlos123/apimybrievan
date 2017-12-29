<?php

use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
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

        Schema::disableForeignKeyConstraints();
        DB::table('cities')->delete();
        foreach(collect($data)->chunk(50) as $chunk) {
            \DB::table('cities')->insert($chunk->toArray());
        }
        Schema::enableForeignKeyConstraints();
    }
}
