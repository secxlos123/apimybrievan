<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewUsersViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::unprepared("DROP VIEW IF EXISTS users_view_table");
        \DB::unprepared("CREATE VIEW users_view_table AS
          SELECT
            users.id AS id,
            users.email             AS email,
            CONCAT(users.first_name, ' ', users.last_name) AS fullname,
            users.first_name        AS first_name,
            users.last_name         AS last_name,
            users.phone             AS phone,
            users.mobile_phone      AS mobile_phone,
            users.gender            AS gender,
            users.is_actived        AS is_actived,
            users.image             AS image,
            offices.name            AS office_name,
            user_details.office_id  AS office_id,
            user_details.nip        AS nip,
            user_details.position   AS position,
            roles.id                AS role_id,
            roles.name              AS role_name,
            roles.slug              AS role_slug
          FROM users
          JOIN role_users ON role_users.user_id = users.id
          JOIN roles ON roles.id = role_users.role_id
          LEFT JOIN user_details ON users.id = user_details.user_id
          LEFT JOIN offices ON user_details.office_id = offices.id
          WHERE NOT roles.slug IN ('developer', 'customer', 'others')
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::unprepared("DROP VIEW users_view_table");
    }
}
