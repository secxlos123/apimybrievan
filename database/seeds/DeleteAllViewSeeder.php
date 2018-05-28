<?php

use Illuminate\Database\Seeder;

class DeleteAllViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::unprepared(" DROP VIEW IF EXISTS customer_view_table ");
        \DB::unprepared(" DROP VIEW IF EXISTS agen_developers_view_table ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_admin_developer ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_appointment ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_pengajuankredit ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_type_one ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_type_two ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_collaterals ");
        \DB::unprepared(" DROP VIEW IF EXISTS developers_view_table ");
        \DB::unprepared(" DROP VIEW IF EXISTS developer_properties_view_table ");
        \DB::unprepared(" DROP VIEW IF EXISTS collateral_view_table ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_property ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_profile_edit ");
        \DB::unprepared(" DROP VIEW IF EXISTS auditrail_new_admin_dev ");
        \DB::unprepared(" DROP VIEW IF EXISTS pengajuan_kredit_briguna ");
    }
}