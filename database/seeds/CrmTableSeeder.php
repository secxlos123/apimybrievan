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
    }
}
