<?php

use Illuminate\Database\Seeder;

class CustomerDataView extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	\DB::unprepared("DROP VIEW IF EXISTS customer_view_table");
    	\DB::unprepared(" CREATE VIEW customer_view_table AS
	    		Select 
				eforms.id AS eforms_id,
				customer_details.user_id AS user_id,
				customer_details.nik,
				customer_details.identity,
				customer_details.couple_identity,
				visit_reports.photo_with_customer,
				visit_reports.npwp,
				visit_reports.legal_document,
				visit_reports.marrital_certificate,
				visit_reports.divorce_certificate,
				visit_reports.offering_letter,
				visit_reports.down_payment,
				visit_reports.building_tax,
				visit_reports.salary_slip,
				visit_reports.proprietary,
				visit_reports.building_permit,
				visit_reports.legal_bussiness_document,
				visit_reports.license_of_practice,
				visit_reports.work_letter,
				visit_reports.family_card
				from customer_details
				LEFT JOIN eforms ON eforms.user_id = customer_details.user_id
				LEFT JOIN visit_reports ON  visit_reports.eform_id = eforms.id
				ORDER BY eforms.id desc
				");

    }
}
