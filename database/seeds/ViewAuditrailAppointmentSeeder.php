<?php

use Illuminate\Database\Seeder;

class ViewAuditrailAppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::unprepared("DROP VIEW IF EXISTS auditrail_appointment");
        \DB::unprepared("CREATE VIEW auditrail_appointment AS
select a.id
, a.created_at
, case
when event = 'created' and lower(auditable_type) = 'app\models\appointment' and slug = 'customer' then 'Penjadwalan baru via Eorm Nasabah'
when event = 'updated' and lower(auditable_type) = 'app\models\appointment' and slug = 'customer' then 'Ubah Penjadwalan via Nasabah'
when event = 'created' and lower(auditable_type) = 'app\models\appointment' and slug = 'developer-sales' then 'Penjadwalan baru via Eform AgenDev'
when event = 'created' and lower(auditable_type) = 'app\models\appointment' and role = 'ao' then 'Penjadwalan baru via Eform AO'
when event = 'updated' and lower(auditable_type) = 'app\models\appointment' and role = 'ao' then 'Ubah Penjadwalan via AO'
when event = 'created' and lower(auditable_type) = 'app\models\eform' and slug = 'customer' then 'Tambah Eform via Nasabah' 
when event = 'created' and lower(auditable_type) = 'app\models\eform' and slug = 'developer-sales' then 'Tambah Eform via AgenDev'
when event = 'created' and lower(auditable_type) = 'app\models\eform' and role = 'ao' then 'Tambah Eform via AO'
when event = 'created' and lower(auditable_type) = 'app\models\user' and role = 'ao' then 'Tambah Leads via AO'
when event = 'created' and lower(auditable_type) = 'app\models\user' and slug = 'developer-sales' then 'Tambah Leads via AgenDev'
when event = 'created' and lower(auditable_type) = 'app\models\user' and slug = 'developer' then 'Tambah Agen Developer'
when event = 'created' and lower(auditable_type) = 'app\models\user' and slug is null then 'Register Nasabah'
when event = 'updated' and lower(auditable_type) = 'app\models\customerdetail' and slug = 'customer' then 'Update Data Pribadi Nasabah'
when event = 'updated' and lower(auditable_type) = 'app\models\eform' and role = 'staff' then 'Disposisi'
end as modul_name
, a.action
, a.extra_params
, a.event
, a.user_id
, lower(a.auditable_type) as auditable_type
, a.url
, case when b.first_name is not null then concat(b.first_name,' ',b.last_name) else h.name end as username
, case when e.slug is not null then e.slug else h.role end as role
, g.ref_number
, c.company_name
, case when c.id = '1' then 'Non Kerja Sama' when c.id >1 then 'Kerja Sama' else '' end as developer
, a.old_values
, a.new_values
, a.ip_address
, case
when event ='updated' and lower(auditable_type) = 'app\models\appointment' and right(a.url, 20) like '%eks/schedule%' and slug = 'customer' then (select extra_params from audits where id = a.id-2) 
when event ='created' and right(a.url, 6) = 'eforms' and lower(auditable_type) = 'app\models\appointment' and slug = 'customer' then (select extra_params from audits where id = a.id-6)
when right(a.url, 6) = 'eforms' and lower(auditable_type) = 'app\models\appointment' and slug = 'customer' then (select extra_params from audits where id = a.id-3)
when right(a.url, 6) = 'eforms' and lower(auditable_type) = 'app\models\appointment' then (select extra_params from audits where id = a.id-5)
when event = 'created' and lower(auditable_type) = 'app\models\appointment' then (select extra_params from audits where id = a.id-1)
when event ='updated' and lower(auditable_type) = 'app\models\appointment' and old_values is not null then (select extra_params from audits where id = a.id-1)
when event ='created' and right(a.url, 6) = 'eforms' and lower(auditable_type) = 'app\models\appointment' and slug = 'customer' then (select extra_params from audits where id = a.id-6)
else 'Null' end as action_location
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
