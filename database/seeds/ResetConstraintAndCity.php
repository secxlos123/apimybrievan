<?php

use Illuminate\Database\Seeder;

class ResetConstraintAndCity extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement("alter table kpr drop constraint if exists kpr_status_property_check;");

        $city = DB::table('cities')
        	->orderby('id')
        	->first();

        if ($city) {
        	$city = DB::table('cities')
        		->where('name', $city->name)
        		->where('id', '!=', $city->id)
        		->orderby('id')
        		->first();
        	if ($city) {
        		\DB::statement("delete from cities where id >= ". $city->id);
        	}
        }
    }
}
