<?php

use Illuminate\Database\Seeder;

class ViewAuditrailAdminDeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::unprepared("DROP VIEW IF EXISTS auditrail_admin_developer");
        \DB::unprepared("CREATE VIEW auditrail_admin_developer AS
         select a.id
         	   , a.created_at 
         	   , a.action as modul_name
         	   , a.event
         	   , a.user_id
         	   , lower(a.auditable_type) as auditable_type
         	   , a.url
         	   , case when b.first_name is not null then concat(b.first_name,' ',b.last_name) else h.name end as username
         	   , case when e.slug is not null then e.slug else h.role end as role
         	   , c.company_name
         	   , case when c.id = '1' then 'Non Kerja Sama' when c.id >1 then 'Kerja Sama' else '' end as developer
         	   , a.old_values
         	   , a.new_values
         	   , a.ip_address
         	   , a.extra_params as action_location
         	   from audits a
			   left join users b on b.id = a.user_id
			   left join developers c on c.user_id = a.user_id
			   left join role_users d on d.user_id = a.user_id
			   left join roles e on e.id = d.role_id
			   left join collaterals f on f.developer_id = c.id
			   left join eforms g on g.user_id = a.user_id
			   left join user_services h on h.pn = a.user_id");
    }
}
