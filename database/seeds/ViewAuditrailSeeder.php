<?php

use Illuminate\Database\Seeder;

class ViewAuditrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::unprepared("DROP VIEW IF EXISTS auditrail_type_one");
        \DB::unprepared("CREATE VIEW auditrail_type_one AS
         select a.id
          , a.created_at
          , case
          when event = 'created' and lower(auditable_type) = 'app\models\appointment' and slug = 'customer' then 'Penjadwalan baru via Eorm Nasabah'
          when event = 'updated' and lower(auditable_type) = 'app\models\appointment' and slug = 'customer' then 'Ubah Penjadwalan via Nasabah'
          when event = 'created' and lower(auditable_type) = 'app\models\appointment' and slug = 'developer-sales' then 'Penjadwalan baru via Eform AgenDev'
          when event = 'created' and lower(auditable_type) = 'app\models\appointment' and role = 'ao' then 'Penjadwalan baru via Eform AO'
          when event = 'updated' and lower(auditable_type) = 'app\models\appointment' and role = 'ao' then 'Ubah Penjadwalan via AO'
          end as modul_name
          , a.action
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
          , a.extra_params as action_location
          from audits a
          left join users b on b.id = a.user_id
          left join developers c on c.user_id = a.user_id
          left join role_users d on d.user_id = a.user_id
          left join roles e on e.id = d.role_id
          left join collaterals f on f.developer_id = c.id
          left join eforms g on g.user_id = a.user_id
          left join user_services h on h.pn = a.user_id");

        \DB::unprepared("DROP VIEW IF EXISTS auditrail_pengajuankredit");
        \DB::unprepared("DROP VIEW IF EXISTS auditrail_type_two");
        \DB::unprepared("CREATE VIEW auditrail_type_two AS
        select a.id
          , a.created_at
          , a.action as modul_name
          , a.event
          , a.user_id
          , lower(a.auditable_type) as auditable_type
          , a.url
          , case when b.first_name is not null then concat(b.first_name,' ',b.last_name) else h.name end as username
          , case when e.slug is not null then e.slug else h.role end as role
          , i.ref_number as ref_number
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
          join eforms i on i.id = a.auditable_id");

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
              , b.user_id as id_debitur
              , concat(e.first_name,' ',e.last_name) as debitur
              , b.branch_id
              , d.region_name
              , d.region_id
        from auditrail_type_two a
        join eforms b on b.ref_number = a.ref_number
        join kpr c on c.eform_id = b.id
        join properties d on d.id = c.property_id
        join users e on e.id = b.user_id");

        $query = \DB::raw("CREATE VIEW pengajuan_kredit_briguna AS
          select eforms.ref_number,eforms.created_at,eforms.prescreening_status,
                eforms.ao_name as username,eforms.ao_position,eforms.pinca_name,
                eforms.is_screening,eforms.branch_id,eforms.branch,
                eforms.pinca_position,briguna.id,briguna.id_aplikasi,
                briguna.no_rekening,briguna.request_amount,".'briguna."Plafond_usulan"'.",
                briguna.is_send,briguna.eform_id,briguna.tp_produk,briguna.tgl_pencairan,
                briguna.tgl_analisa,briguna.tgl_putusan,briguna.cif,
                customer_details.nik,customer_details.is_verified,
                customer_details.address,customer_details.mother_name,
                customer_details.birth_date,
                concat(users.first_name, ' ' ,users.last_name) as nama_debitur,
                users.mobile_phone,users.gender,
                case when eforms.ao_id is not null or eforms.ao_id != '' 
                    then 'Disposisi Pengajuan'
                    else 'Pengajuan Kredit' end as status_pengajuan,
                case when (briguna.tp_produk = '1' and briguna.jenis_pinjaman_id = '1')
                    then 'Briguna Karya'
                    when (briguna.tp_produk = '1' and briguna.jenis_pinjaman_id = '2') 
                    then 'Briguna Umum'
                    when briguna.tp_produk = '2' then 'Briguna Purna'
                    when briguna.tp_produk = '10' then 'Briguna Micro'
                    when briguna.tp_produk = '22' then 'Briguna Talangan'
                    when briguna.tp_produk = '28' then 'Briguna Pekerja BRI'
                    else 'Lainnya' end as product,
                case when briguna.is_send = 0 then 'APPROVAL'
                  when briguna.is_send = 1 then 'APPROVED'
                  when briguna.is_send = 2 then 'UNAPPROVED'
                  when briguna.is_send = 3 then 'DITOLAK'
                  when briguna.is_send = 4 then 'VOID ADK'
                  when briguna.is_send = 5 then 'APPROVED PENCAIRAN'
                  when briguna.is_send = 6 then 'DISBURSED'
                  when briguna.is_send = 7 then 'SENT TO BRINETS'
                  when briguna.is_send = 8 then 'AGREE BY MP'
                  when briguna.is_send = 9 then 'DITOLAK'
                  when briguna.is_send = 10 then 'AGREE BY AMP'
                  when briguna.is_send = 11 then 'AGREE BY PINCAPEM'
                  when briguna.is_send = 12 then 'AGREE BY PINCA'
                  when briguna.is_send = 13 then 'AGREE BY WAPINWIL'
                  when briguna.is_send = 14 then 'AGREE BY WAPINCASUS'
                  when briguna.is_send = 15 then 'NAIK KETINGKAT LEBIH TINGGI BY AMP'
                  when briguna.is_send = 16 then 'NAIK KETINGKAT LEBIH TINGGI BY MP'
                  when briguna.is_send = 17 then 'NAIK KETINGKAT LEBIH TINGGI BY PINCAPEM'
                  when briguna.is_send = 18 then 'NAIK KETINGKAT LEBIH TINGGI BY PINCA'
                  when briguna.is_send = 19 then 'NAIK KETINGKAT LEBIH TINGGI BY WAPINWIL'
                  when briguna.is_send = 20 then 'NAIK KETINGKAT LEBIH TINGGI BY WAPINCASUS'
                  when briguna.is_send = 21 then 'MENGEMBALIKAN DATA KE AO'
                  else '-' end as status_putusan
            from eforms
          join briguna on briguna.eform_id = eforms.id
          join customer_details on customer_details.user_id = eforms.user_id
          join users on users.id = eforms.user_id
          order by eforms.created_at desc
        ");
        \DB::unprepared("DROP VIEW IF EXISTS pengajuan_kredit_briguna");
        \DB::unprepared($query);
    }
}