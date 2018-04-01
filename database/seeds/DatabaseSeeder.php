<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call( UsersTableSeeder::class );
        // $this->call( MitraTableSeeder::class );
        // $this->call( KodeposTableSeeder::class );
        // $this->call( RolesTableSeeder::class );
        // $this->call( CityTableSeeder::class );
        // $this->call( UserDeveloper::class );
        // $this->call( DeveloperTableSeeder::class );
        // $this->call( UserServicesTableSeeder::class );
        $this->call( DeleteAllViewSeeder::class );
        $this->call( ViewSeeder::class );
        $this->call( CustomerDataView::class );
        $this->call( ViewAuditrailAdminDeveloperSeeder::class );
        $this->call( ViewAuditrailAppointmentSeeder::class );
        $this->call( ViewAuditrailAppointmentSeeder::class );
        $this->call( ViewAuditrailSeeder::class );
        $this->call( ViewUserAgenDeveloperSeeder::class );
        $this->call( FixAutoId::class );

    }
}
