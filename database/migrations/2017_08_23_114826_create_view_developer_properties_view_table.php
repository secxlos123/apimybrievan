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
                property_types.property_id AS prop_id,
                properties.name AS prop_name,
                property_types.id AS prop_type_id,
                property_types.name AS prop_type_name,
                properties.city_id AS prop_city_id,
                cities.name AS prop_city_name,
                developers.user_id AS prop_dev_id,
                (
                    SELECT count(property_items.id) FROM property_items
                        WHERE property_types.id = property_items.property_type_id
                ) AS prop_items
            FROM property_types
            INNER JOIN properties ON properties.id = property_types.property_id
            INNER JOIN cities ON properties.city_id = cities.id
            INNER JOIN developers ON properties.developer_id = developers.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::unprepared("DROP VIEW developer_properties_view_table");
    }
}
