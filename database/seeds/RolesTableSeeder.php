<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends UsersTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->truncate();
        $this->setRoles();
    }
}
