<?php

use Illuminate\Database\Seeder;

class CrmTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('crm_statuses')->insert(array(
        array('id' => 1, 'status_name' => 'Prospek'),
        array('id' => 2, 'status_name' => 'On Progress'),
        array('id' => 3, 'status_name' => 'Done'),
        array('id' => 4, 'status_name' => 'Batal'),
      ));

      DB::table('crm_product_types')->insert(array(
        array('id' => 1, 'product_name' => 'Giro'),
        array('id' => 2, 'product_name' => 'Tabungan'),
        array('id' => 3, 'product_name' => 'Deposito'),
        array('id' => 4, 'product_name' => 'CMS'),
        array('id' => 5, 'product_name' => 'E-Banking'),
        array('id' => 6, 'product_name' => 'Prioritas'),
      ));

      DB::table('crm_activity_types')->insert(array(
        array('id' => 1, 'activity_name' => 'Pick Up Service'),
        array('id' => 2, 'activity_name' => 'Top Up'),
        array('id' => 3, 'activity_name' => 'Akuisisi'),
        array('id' => 4, 'activity_name' => 'Suplesi'),
        array('id' => 5, 'activity_name' => 'Maintenance'),
        array('id' => 6, 'activity_name' => 'Perpanjangan'),
        array('id' => 7, 'activity_name' => 'Retensi'),
        array('id' => 8, 'activity_name' => 'Penagihan'),
      ));

      DB::table('crm_object_activities')->insert(array(
        array('id' => 1, 'name' => 'prospek', 'display_name' => 'Prospek Penawaran'),
        array('id' => 2, 'name' => 'follow_up', 'display_name' => 'Follow Up / Negosiasi'),
        array('id' => 3, 'name' => 'closing', 'display_name' => 'Closing (Akuisisi/ Aktivasi / Upgrade)'),
        array('id' => 4, 'name' => 'pick_up', 'display_name' => 'Pick Up Dana Baru / Top Up'),
        array('id' => 5, 'name' => 'collection_document', 'display_name' => 'Collection Dokumen'),
        array('id' => 6, 'name' => 'maintenance', 'display_name' => 'Maintenance'),
        array('id' => 7, 'name' => 'sosialisasi', 'display_name' => 'Sosialisasi'),
        array('id' => 8, 'name' => 'implementasi', 'display_name' => 'Implementasi'),
        array('id' => 9, 'name' => 'complain_handling', 'display_name' => 'Complain Handling'),
      ));

      DB::table('crm_action_activities')->insert(array(
        array('id' => 1, 'name' => 'kunjungan', 'display_name' => 'Kunjungan'),
        array('id' => 2, 'name' => 'telepon', 'display_name' => 'Telepon'),
        array('id' => 3, 'name' => 'event', 'display_name' => 'Event'),
        array('id' => 4, 'name' => 'rapat', 'display_name' => 'Rapat'),
        array('id' => 5, 'name' => 'email', 'display_name' => 'Email'),
      ));
    }
}
