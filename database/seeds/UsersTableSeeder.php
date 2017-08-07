<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$this->clearData();

    	foreach ($this->setRoles() as $role) {
    		$user = factory(App\Models\User::class)->create();
    		$activation = Activation::create($user);
    		Activation::complete($user, $activation->code);
    		$role->users()->attach($user);
    	}
    }

    /**
     * Clear data users.
     *
     * @return void
     */
    private function clearData()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::table('role_users')->truncate();
    }

    /**
     * Set roles.
     *
     * @return array
     */
    private function setRoles()
    {
    	$roles = [
    		['name' => 'Account Officer', 'slug' => 'ao'],
    		['name' => 'Manager Pemasaran', 'slug' => 'mp'],
    		['name' => 'Pimpinan Cabang', 'slug' => 'pinca'],
    		['name' => 'Developer', 'slug' => 'developer'],
    		['name' => 'Nasabah', 'slug' => 'customer'],
    		['name' => 'Pihak Ke-3', 'slug' => 'others'],
    	];

    	$models = [];
    	foreach ($roles as $key => $role) {
    		$models[$key] = Sentinel::getRoleRepository()->createModel()->create($role);
    	}
    	return $models;
    }
}
