<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Database\Schema\Blueprint;
use Schema;

class RemovableController extends Controller
{
    public function run( Request $request ) {
        if( $request->header( 'password' ) == 'yudi.y@smooets.com' ) {
            $update_message = [];
            if (Schema::hasTable('developer_properties_view_table') && ! Schema::hasColumn('developer_properties_view_table', 'prop_price')) {
                \DB::unprepared("DROP VIEW IF EXISTS developer_properties_view_table");
                \DB::unprepared("CREATE VIEW developer_properties_view_table AS
                    SELECT 
                        properties.id AS prop_id,
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
                        INNER JOIN cities ON properties.city_id = cities.id
                ");

                $update_message[] = 'Update developer_properties_view_table';
            }

            if( empty( $update_message ) ) {
                return response()->json( [
                    'message' => 'No update'
                ], 200 );
            } else {
                return response()->json( [
                    'message' => $update_message
                ], 200 );
            }
        } else {
            return response()->json( [
                'message' => 'Not authorized!'
            ], 400 );
        }
    }
}
