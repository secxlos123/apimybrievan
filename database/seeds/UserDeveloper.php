<?php

use Illuminate\Database\Seeder;

class UserDeveloper extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        DB::beginTransaction();
        try {
            $action = 'update';
            if ( ! $user = App\Models\User::findEmail('mybri@bri.co.id') ) {
                $user = App\Models\User::create([
                    'email'     => 'mybri@bri.co.id',
                    'password'  => bcrypt('12345678'),
                    'first_name'=> 'INDEPENDENT',
                    'last_name' => 'BRI',
                ]);

                $action = 'create';
            }

            $developer = $user->developer()->updateOrCreate(['dev_id_bri' => 1], [
                'dev_id_bri'    => 1,  
                'company_name'  => 'INDEPENDENT',
                'created_by'    => 'BRI',
                'pks_number'    => '-',
                'address'       => '-',
                'summary'       => '-',
                'plafond'       => '-'
            ]);

            $developer->properties()->updateOrCreate(['prop_id_bri' => 1], [
                'name' => 'INDEPENDENT',
                'prop_id_bri' => 1,
                'description' => '-',
                'facilities'  => '-',
                'pic_phone' => '-',
                'address'   => '-',
                'category'  => 3,
                'latitude'  => '0',
                'longitude' => '0',
                'pic_name'  => 'BRI'
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
    }
}