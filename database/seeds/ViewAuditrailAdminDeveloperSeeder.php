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
               , f.staff_name
         	   , case when e.slug is not null then e.slug else h.role end as role
         	   , i.name as project_name
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
			   left join user_services h on h.pn = a.user_id
			   left join properties i on i.developer_id = c.id");

        //this for table view auditrail collateral
        \DB::unprepared("DROP VIEW IF EXISTS auditrail_collaterals");
        \DB::unprepared("CREATE VIEW auditrail_collaterals AS
         select a.id
            , a.created_at
            , a.action as modul_name
            , a.event
            , a.user_id
            , lower(a.auditable_type) as auditable_type
            , a.url
            , h.name as username
            , h.role as role
            ,c.staff_name as staff_penilai
            ,d.company_name as company_name
            ,case when c.developer_id= '1' then 'Non Kerja Sama' when c.developer_id >1 then 'Kerja Sama' else '' end as developer 
            , a.old_values
            , a.new_values
            , a.ip_address
            , a.extra_params as action_location
            from audits a
            left join user_services h on h.pn = a.user_id
            left join collaterals c on a.auditable_id = c.id
            left join users s on c.developer_id = s.id 
            left join developers d on d.user_id = s.id");
    }


}
