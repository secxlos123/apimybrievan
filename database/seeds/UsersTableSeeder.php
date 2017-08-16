<?php 

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Avaliable permissions.
     *
     * @var array
     */
    protected $permissions = [
        'home' => true,
        'nasabah' => true,
        'properti' => true,
        'e-form' => true,
        'developer' => true,
        'debitur' => true,
        'penjadwalan' => true,
        'kalkulator' => true,
        'tracking' => true,
        'pihak-ke-3' => true,
        'manajemen-user' => true,
        'manajemen-role' => true,
    ];

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
    protected function clearData()
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
    protected function setRoles()
    {
    	$roles = [
    		['name' => 'Account Officer', 'slug' => 'ao', 'is_default' => true, 'permissions' => $this->permissions],
    		['name' => 'Manager Pemasaran', 'slug' => 'mp', 'is_default' => true, 'permissions' => $this->permissions],
    		['name' => 'Pimpinan Cabang', 'slug' => 'pinca', 'is_default' => true, 'permissions' => $this->permissions],
    		['name' => 'Developer', 'slug' => 'developer', 'is_default' => true, 'permissions' => $this->permissions],
    		['name' => 'Nasabah', 'slug' => 'customer', 'is_default' => true, 'permissions' => $this->permissions],
    		['name' => 'Pihak Ke-3', 'slug' => 'others', 'is_default' => true, 'permissions' => $this->permissions],
    	];

    	$models = [];
    	foreach ($roles as $key => $role) {
    		$models[$key] = Sentinel::getRoleRepository()->createModel()->create($role);
    	}
    	return $models;
    }
}
