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
                , case when k.company_name is not null then k.company_name when m.company_name is not null then m.company_name when n.company_name is not null then n.company_name else c.company_name end as company_name
               , case when c.id = '1' then 'Non Kerja Sama' when c.id >1 then 'Kerja Sama' else '' end as developer
               , a.old_values
               , a.new_values
               , a.ip_address
               , a.extra_params as action_location
               from audits a
               left join users b on b.id = a.user_id
               left join developers c on c.user_id = a.user_id
               left join user_developers j on j.user_id = b.id
               left join developers k on k.user_id = j.admin_developer_id
               left join role_users d on d.user_id = a.user_id
               left join roles e on e.id = d.role_id
               left join collaterals f on f.developer_id = c.id
               left join eforms g on g.user_id = a.user_id
               left join user_services h on h.pn = a.user_id
               left join properties i on i.developer_id = c.id
               left join developers m on m.user_id = a.auditable_id
                left join developers n on n.id = a.auditable_id ");

        //this for table view auditrail collateral
        \DB::unprepared("DROP VIEW IF EXISTS auditrail_collaterals");
        \DB::unprepared("CREATE VIEW auditrail_collaterals AS
         select a.id
            ,c.id as collateral_id
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
            ,c.manager_id
            ,c.manager_name
            ,e.region_id
            ,e.region_name
            from audits a
            left join user_services h on h.pn = a.user_id
            left join collaterals c on a.auditable_id = c.id
            left join users s on c.developer_id = s.id 
            left join developers d on d.user_id = s.id
            left join properties e on e.id = c.property_id");
        //view audit trail property
        \DB::unprepared("DROP VIEW IF EXISTS auditrail_property");
        \DB::unprepared("CREATE VIEW auditrail_property AS
            select * from (SELECT a.id
               , a.created_at 
               , a.action as modul_name
               , a.event
               , a.user_id
                    , lower(a.auditable_type) as auditable_type
               , a.url
               ,concat(b.first_name,' ',b.last_name)   as username
               ,i.name as project_name
               , case when i.id = '1' then 'Non Kerja Sama' when i.id >1 then 'Kerja Sama' else '' end as developer
                , a.old_values
               , a.new_values
               , a.ip_address
               , a.extra_params as action_location
               , e.slug  as role
               ,dd.company_name
              FROM audits a
              join users b  on b.id = a.user_id
              join properties i on i.id = a.auditable_id
              left join role_users d on d.user_id = a.user_id
              join developers dd on dd.user_id = b.id 
              left join roles e on e.id = d.role_id
              WHERE  a.user_id != '0'
              AND a.auditable_type != 'App\Models\User' and a.auditable_type ='App\Models\Property') 
                as audit_proyek
          union all
              select * from (SELECT a.id
                             , a.created_at 
                             , a.action as modul_name
                             , a.event
                             , a.user_id
                                  , lower(a.auditable_type) as auditable_type
                             , a.url
                             ,concat(b.first_name,' ',b.last_name)   as username
                             ,pp.name as project_name
                             , case when b.id = '1' then 'Non Kerja Sama' when i.id >1 then 'Kerja Sama' else '' end as developer
                              , a.old_values
                             , a.new_values
                             , a.ip_address
                             , a.extra_params as action_location
                             , e.slug  as role
                            ,dd.company_name
                            FROM audits a
                            join users b  on b.id = a.user_id
                            join property_types  i on i.id = a.auditable_id
                            join properties pp on pp.id = i.property_id
                            left join role_users d on d.user_id = a.user_id
                            join developers dd on dd.user_id = b.id 
                            left join roles e on e.id = d.role_id
                            WHERE  a.user_id != '0'
                            AND a.auditable_type != 'App\Models\User' and a.auditable_type ='App\Models\PropertyType')
                 as audit_tipe_proyek
          union all
              select * from ( SELECT  a.id
                               , a.created_at 
                               , a.action as modul_name
                               , a.event
                               , a.user_id
                               , lower(a.auditable_type) as auditable_type
                               , a.url
                               ,concat(b.first_name,' ',b.last_name)   as username
                               ,pp.name as project_name
                               , case when b.id = '1' then 'Non Kerja Sama' when i.id >1 then 'Kerja Sama' else '' end as developer
                                , a.old_values
                               , a.new_values
                               , a.ip_address
                               , a.extra_params as action_location
                               , e.slug  as role
                               ,dd.company_name
                              FROM audits a
                              join users b  on b.id = a.user_id
                              join property_items pi on pi.id = a.auditable_id
                              join property_types  i on  i.id = pi.property_type_id
                              join properties pp on pp.id = i.property_id
                              left join role_users d on d.user_id = a.user_id
                              join developers dd on dd.user_id = b.id 
                              left join roles e on e.id = d.role_id
                              WHERE  a.user_id != '0'
                              AND a.auditable_type != 'App\Models\User' and a.auditable_type ='App\Models\PropertyItem') 
                as audit_unit_proyek
          ");
        \DB::unprepared("DROP VIEW IF EXISTS auditrail_profile_edit");
        \DB::unprepared("CREATE VIEW auditrail_profile_edit AS
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
               , case when c.id = '1' then 'Non Kerja Sama' when c.id >1 then 'Kerja Sama' else '' end as developer
               , a.old_values
               , a.new_values
               , a.ip_address
               , a.extra_params as action_location
               from audits a
               left join users b on b.id = a.user_id
               left join developers c on c.user_id = a.user_id
               left join user_developers j on j.user_id = b.id
               left join role_users d on d.user_id = a.user_id
               left join roles e on e.id = d.role_id
               left join collaterals f on f.developer_id = c.id
               left join eforms g on g.user_id = a.user_id
               left join user_services h on h.pn = a.user_id");

        \DB::unprepared("DROP VIEW IF EXISTS auditrail_new_admin_dev");
        \DB::unprepared("CREATE VIEW auditrail_new_admin_dev AS
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
               /*, i.name as project_name */
                , case when k.company_name is not null then k.company_name when m.company_name is not null then m.company_name when n.company_name is not null then n.company_name else c.company_name end as company_name
               , case when c.id = '1' then 'Non Kerja Sama' when c.id >1 then 'Kerja Sama' else '' end as developer
               , a.old_values
               , a.new_values
               , a.ip_address
               , a.extra_params as action_location
               from audits a
                join users b on b.id = a.user_id
               left join developers c on c.user_id = a.user_id
               left join user_developers j on j.user_id = b.id
               left join developers k on k.user_id = j.admin_developer_id
               left join role_users d on d.user_id = a.user_id
               left join roles e on e.id = d.role_id
               left join collaterals f on f.developer_id = c.id
               left join eforms g on g.user_id = a.user_id
               left join user_services h on h.pn = a.user_id
             /*   join properties i on i.developer_id = c.id */
               left join developers m on m.user_id = a.auditable_id
               left join developers n on n.id = a.auditable_id");

        \DB::unprepared("DROP VIEW IF EXISTS auditrail_pengajuankredit");
        \DB::unprepared("CREATE VIEW auditrail_pengajuankredit AS
         select a.id
              , a.created_at
              , a.modul_name
              , a.event
              , a.user_id
              , a.auditable_type
              , a.url
              , a.username
              , a.role
              , a.ref_number
              , a.company_name
              , a.developer
              , a.old_values
              , a.new_values
              , a.ip_address
              , a.action_location
              , b.id as eform_id
              , b.branch_id
              , d.region_name
              , d.region_id 
        from auditrail_type_two a 
        join eforms b on b.ref_number = a.ref_number
        join kpr c on c.eform_id = b.id
        join properties d on d.id = c.property_id");

    }


}
