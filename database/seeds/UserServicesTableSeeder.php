<?php

use Illuminate\Database\Seeder;
use App\Models\UserServices;

class UserServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userServices = array(
        	[	'pn' => 16181,	
        		'branch_id' => '012',	
        		'hilfm' => 14,	
        		'role' => 'pinca',	
        		'name' => 'Dani Alfianto',	
        		'tipe_uker' => 'KC',	
        		'htext' => 'Pemimpin Cabang',	
        		'posisi' => 'PEMIMPIN CABANG',	
        		'last_activity' => '2017-12-21 12:27:12',	
        		'created_at' => '2017-12-21 05:23:19',	
        		'updated_at' => '2017-12-21 05:23:19',	
        		'password' => md5('123')
        	],
        	[	'pn' => 66777,	
        		'branch_id' => '012',	
        		'hilfm' => 42,	
        		'role' => 'ao',	
        		'name' => 'Jain Saparudin',	
        		'tipe_uker' => 'KC',	
        		'htext' => 'AO Menengah',	
        		'posisi' => 'PJ.ASSOCIATE ACCOUNT OFFICER 2 MENENGAH',	
        		'last_activity' => '2017-12-21 12:27:12',	
        		'created_at' => '2017-12-21 05:23:19',	
        		'updated_at' => '2017-12-21 05:23:19',	
        		'password' => md5('123')
        	],
        	[	'pn' => 68881,	
        		'branch_id' => '012',	
        		'hilfm' => 26,	
        		'role' => 'staff',	
        		'name' => 'Isti Yuli Ismawati',	
        		'tipe_uker' => 'KW',	
        		'htext' => 'Staf & Jabatan setingkat',	
        		'posisi' => 'STAFF 2',	
        		'last_activity' => '2017-12-21 12:27:12',	
        		'created_at' => '2017-12-21 05:23:19',	
        		'updated_at' => '2017-12-21 05:23:19',	
        		'password' => md5('123')
        	],
        );

        foreach ($userServices as $data) {
        	UserServices::firstOrCreate($data);
        }
    }
}
