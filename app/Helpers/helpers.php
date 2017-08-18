<?php

if (! function_exists('csv_to_array')) {
	
    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers 
     * @param  string $delimiter
     * 
     * @return array
     */
	function csv_to_array($file = '', array $headers, $delimiter = ',')
	{
		if(!file_exists($file) || !is_readable($file)) return FALSE;

		$data = [];
		if (($handle = fopen($file, 'r')) !== FALSE) {
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
				$data[] = array_combine($headers, $row);
			}
			fclose($handle);
		}

		return $data;
	}
}
