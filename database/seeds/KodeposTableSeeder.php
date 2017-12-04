<?php

use Illuminate\Database\Seeder;
use Crockett\CsvSeeder\CsvSeeder;

class KodeposTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
	 function csv_to_array($filename = '', $delimiter = ',', $asHash = true)
{
  if(!file_exists($filename) || !is_readable($filename)) return false;
  if (!(is_readable($filename) || (($status = get_headers($filename)) && strpos($status[0], '200')))) {
        return FALSE;
    }
 
    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        if ($asHash) {
            while ($row = fgetcsv($handle, 0, $delimiter)) {
                if (!$header) {					
                    $header = $row;
                } else {
                    $data[] = $this->array_combine2($header, $row);
                }
            }
        } else {
            while ($row = fgetcsv($handle, 0, $delimiter)) {
                $data[] = $row;
            }
        }
 
        fclose($handle);
    } 

    return $data;

}
function array_combine2($arr1, $arr2) {
    $count = min(count($arr1), count($arr2));
    return array_combine(array_slice($arr1, 0, $count),
                         array_slice($arr2, 0, $count));
}
    public function run()
    {
			
            $file = __DIR__. '\..\csv\kode_pos6.csv';	
			$data = $this->csv_to_array($file);
print_r($data);die();
        $city = DB::table('tbl_kodepos')->where('postal_code', $data[0]['postal_code'])->first();
		        if ( !$city ) {

            foreach(collect($data)->chunk(50) as $chunk) {
                \DB::table('tbl_kodepos')->insert($chunk->toArray());
            }
				}


		
    }
}
