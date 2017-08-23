<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewDevelopersViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::unprepared("DROP VIEW IF EXISTS developers_view_table");
        \DB::unprepared("CREATE VIEW developers_view_table AS
          SELECT 
              users.id AS dev_id, 
              developers.company_name AS company_name, 
              concat(users.first_name, ' ', users.last_name) AS name,
              users.email AS email,
              users.phone AS phone_number,
              cities.id AS city_id,
              cities.name AS city_name,
              ( 
                  SELECT count( * ) FROM properties
                      LEFT JOIN property_types ON properties.id = property_types.property_id
                      LEFT JOIN property_items ON property_types.id = property_items.property_type_id
                      where developers.id = properties.developer_id
              ) AS project 

          FROM developers
              inner JOIN users ON users.id = developers.user_id
              inner JOIN cities ON cities.id = developers.city_id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::unprepared("DROP VIEW developers_view_table");
    }
}
