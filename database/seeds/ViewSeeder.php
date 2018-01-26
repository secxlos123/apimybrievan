<?php

use Illuminate\Database\Seeder;

class ViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::unprepared("DROP VIEW IF EXISTS developers_view_table");
        \DB::unprepared("CREATE VIEW developers_view_table AS
          SELECT 
              users.id AS dev_id,
              developers.dev_id_bri AS bri, 
              developers.company_name AS company_name, 
              concat(users.first_name, ' ', users.last_name) AS name,
              users.email AS email,
              users.phone AS phone_number,
              users.is_actived AS is_actived,
              cities.id AS city_id,
              cities.name AS city_name,
              ( 
                  SELECT count( * ) FROM properties
                      LEFT JOIN property_types ON properties.id = property_types.property_id
                      LEFT JOIN property_items ON property_types.id = property_items.property_type_id
                      where developers.id = properties.developer_id
              ) AS project 

          FROM developers
              LEFT JOIN users ON users.id = developers.user_id
              LEFT JOIN cities ON cities.id = developers.city_id
        ");

        \DB::unprepared("DROP VIEW IF EXISTS developer_properties_view_table");
        \DB::unprepared("CREATE VIEW developer_properties_view_table AS
            SELECT 
                properties.id AS prop_id,
                properties.prop_id_bri AS bri,
                properties.name AS prop_name,
                properties.pic_name AS prop_pic_name,
                properties.pic_phone AS prop_pic_phone,
                properties.slug AS prop_slug,
                properties.city_id AS prop_city_id,
                properties.category AS prop_category,
                cities.name AS prop_city_name,
                properties.staff_id AS staff_id,
                properties.staff_name AS staff_name,
                properties.status AS status,
                properties.is_approved,
                ( SELECT max(property_types.price) FROM property_types WHERE properties.id = property_types.property_id ) AS prop_price,
                ( SELECT developers.user_id FROM developers WHERE properties.developer_id = developers.id ) AS prop_dev_id,
                ( SELECT developers.dev_id_bri FROM developers WHERE properties.developer_id = developers.id ) AS dev_id_bri,
                ( SELECT count(property_types.id) FROM property_types WHERE properties.id = property_types.property_id ) AS prop_types,
                ( SELECT count(property_items.id) FROM property_items inner join property_types on property_types.id = property_items.property_type_id where properties.id = property_types.property_id ) AS prop_items
            FROM properties
                LEFT JOIN cities ON properties.city_id = cities.id
        ");

        \DB::unprepared("DROP VIEW IF EXISTS collateral_view_table");
        \DB::unprepared("CREATE VIEW collateral_view_table AS
              SELECT
              users.first_name,
              users.last_name,
              users.phone,
              users.mobile_phone,
              users.gender,
              users.email,
              customer_details.mother_name,
              customer_details.address,
              customer_details.citizenship_name,
              customer_details.dependent_amount,
              eforms.id AS eform_id,
              eforms.ref_number,
              eforms.ao_name,
              eforms.branch,
              eforms.appointment_date,
              eforms.product_type,
              eforms.is_approved,
              eforms.nik,
              kpr.status_property,
              kpr.developer_id,
              kpr.property_id,
              kpr.price,
              kpr.building_area,
              kpr.home_location,
              kpr.year,
              kpr.active_kpr,
              kpr.dp,
              kpr.request_amount,
              kpr.developer_name,
              kpr.property_name,
              kpr.kpr_type_property,
              kpr.property_type,
              kpr.property_type_name,
              kpr.is_sent,
              collaterals.id AS collaterals_id,
              collaterals.staff_id,
              collaterals.staff_name,
              collaterals.status,
              collaterals.is_staff,
              collaterals.approved_by,
              (select name from cities where cities.id = customer_details.birth_place_id) AS birthplace,
              CASE WHEN customer_details.address_status::int = 0 THEN 'Milik Sendiri'
                   WHEN customer_details.address_status::int = 1 THEN 'Milik Orang Tua/Mertua atau Rumah Dinas'
                   WHEN customer_details.address_status::int = 3 THEN 'Tinggal di Rumah Kontrakan'
                   ELSE 'Tidak Ada' END AS address_status,
              CASE WHEN customer_details.status::int = 1 THEN 'Belum Menikah'
                   WHEN customer_details.status::int = 2 THEN 'Menikah'
                   WHEN customer_details.status::int = 3 THEN 'Janda/Duda'
                   ELSE 'Tidak Ada' END AS status_user,
              CASE WHEN kpr.status_property::int = 1 THEN 'Baru'
                   WHEN kpr.status_property::int = 2 THEN 'Secondary'
                   WHEN kpr.status_property::int = 3 THEN 'Refinancing'
                   WHEN kpr.status_property::int = 4 THEN 'Renovasi'
                   WHEN kpr.status_property::int = 5 THEN 'Top Up'
                   WHEN kpr.status_property::int = 6 THEN 'Take Over'
                   WHEN kpr.status_property::int = 7 THEN 'Take Over Top Up'
                   ELSE 'Tidak Ada' END AS status_property_name,
              CASE WHEN kpr.kpr_type_property::int = 1 THEN 'Rumah Tapak'
                   WHEN kpr.kpr_type_property::int = 2 THEN 'Rumah Susun/Apartment'
                   WHEN kpr.kpr_type_property::int = 3 THEN 'Rumah Toko'
                   ELSE 'Tidak Ada' END AS kpr_type_property_name

              from users
              LEFT JOIN customer_details ON customer_details.user_id = users.id
              LEFT JOIN eforms ON eforms.user_id = users.id
              LEFT JOIN kpr ON kpr.eform_id = eforms.id
              LEFT JOIN visit_reports ON visit_reports.eform_id = eforms.id
              LEFT JOIN collaterals ON collaterals.property_id = kpr.property_id
              WHERE eforms.id is not null AND collaterals.id is not null AND visit_reports.id is not null
          ");
    }
}
