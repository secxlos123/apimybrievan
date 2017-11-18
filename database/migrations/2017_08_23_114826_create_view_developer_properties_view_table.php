<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewDeveloperPropertiesViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
                ( SELECT max(property_types.price) FROM property_types WHERE properties.id = property_types.property_id ) AS prop_price,
                ( SELECT developers.user_id FROM developers WHERE properties.developer_id = developers.id ) AS prop_dev_id,
                ( SELECT count(property_types.id) FROM property_types WHERE properties.id = property_types.property_id ) AS prop_types,
                ( SELECT count(property_items.id) FROM property_items inner join property_types on property_types.id = property_items.property_type_id where properties.id = property_types.property_id ) AS prop_items
            FROM properties
                LEFT JOIN cities ON properties.city_id = cities.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::unprepared("DROP VIEW IF EXISTS developer_properties_view_table");
    }
}
