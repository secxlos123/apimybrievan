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
              eforms.id AS eform_id,
              eforms.ref_number,
              eforms.product_type,
              eforms.is_approved,
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
              collaterals.approved_by

              from users
              LEFT JOIN eforms ON eforms.user_id = users.id
              LEFT JOIN kpr ON kpr.eform_id = eforms.id
              LEFT JOIN collaterals ON collaterals.property_id = kpr.property_id
              WHERE eforms.id is not null AND collaterals.id is not null
          ");
    }
}
