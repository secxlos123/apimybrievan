<?php

use Illuminate\Database\Seeder;

class ViewUserAgenDeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::unprepared("DROP VIEW IF EXISTS agen_developers_view_table");
        \DB::unprepared("CREATE VIEW agen_developers_view_table AS
          SELECT b.id,
                 a.id AS user_id,
                 a.first_name,
                 a.last_name,
           		 a.email, concat(a.first_name, ' ', a.last_name) AS full_name,
            	 a.phone, a.mobile_phone,
            	 a.is_banned,
            	 a.last_login,
            	 b.birth_date,
            	 b.join_date,
                 b.bound_project,
            	 b.admin_developer_id  
            	 FROM users a 
				 LEFT JOIN  user_developers b on a.id = b.user_id");
    }
}
