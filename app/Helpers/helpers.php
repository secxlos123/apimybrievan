<?php

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Storage;

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

if (! function_exists('name_separator')) {

    /**
     * Return an array of first name and last name from given full name.
     *
     * @param  string  $fullname
     * @return array
     */
    function name_separator($fullname)
    {
        $fullname = explode(' ', $fullname);

        return [$fullname[0], implode(' ', array_except($fullname, 0))];
    }
}

if (! function_exists('generate_paths')) {

    /**
     * Listen for generate path of photos and save to storage.
     *
     * @param  array    $photos
     * @param  string   $driver
     * @return array
     */
    function generate_paths($photos, $driver = 'uploads', $folder = '')
    {
        $paths = [];
        foreach ($photos as $key => $photo) {
            if ( is_file($photo) ) $paths[$key]['path'] = $photo->store($folder, $driver);
        }
        return $paths;
    }
}

if (! function_exists('removed_photos')) {

    /**
     * Logic for deleted photos
     *
     * @param  mixed    $model
     * @return void
     */
    function removed_photos($model, $driver = 'uploads')
    {
        if (request()->has('removed_photos') && $model->photos) {

            /**
             * Filtering object if exists with request removed_photos
             */
            $photos = $model->photos->filter(function ($value, $key) {
                return in_array($key, request('removed_photos'));
            });

            foreach ($photos as $photo) {
                Storage::disk($driver)->delete($photo->path);
                $photo->delete();
            }
        }
    }
}

if (! function_exists('saving_photos')) {

    /**
     * Logic for saving photos
     *
     * @param  mixed    $model
     * @return void
     */
    function saving_photos($model, $driver = 'uploads')
    {
        /**
         * This logic for remove image for property type
         */
        removed_photos($model, $driver);

        /**
         * Call function generate_paths on helpers file
         * request photos is array type, properties is a driver for saving to storage, last variable is folder
         */
        if (request()->hasFile('photos')) {
            $paths = generate_paths(request('photos'), $driver, $model->id);
            $model->photos()->createMany($paths);
        }
    }
}

if (! function_exists('curl_post')) {

    function curl_post($url, array $post = NULL, array $options = array())
    {
        $defaults = [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POSTFIELDS => http_build_query($post)
        ];

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        if( ! $result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }
}

if (! function_exists('user_info')) {
    /**
     * Get logged user info.
     *
     * @param  string $column
     * @return mixed
     */
    function user_info($column = null)
    {
        if ($user = Sentinel::check()) {
            if (is_null($column)) {
                return $user;
            }

            if ('full_name' == $column) {
                return user_info('first_name').' '.user_info('last_name');
            }

            if ('role' == $column) {
                return user_info()->roles[0];
            }

            return $user->{$column};
        }

        return null;
    }
}

if (! function_exists('generate_pdf')) {

    /**
     * Generate pdf file.
     *
     * @param  string $folder
     * @param  string $filename
     * @param  string $html
     *
     * @return array
     */
    function generate_pdf($folder, $filename, $html)
    {
        // return generate_pdf("uploads/327702020394", "myfile.pdf", '<h1>init data</h1>');
        try {
            $path = public_path().'/'.$folder;
            File::makeDirectory($path, $mode = 0777, true, true);

            PDF::loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false)
                ->save(public_path($folder.'/'.$filename));

        } catch (Exception $e) {
            \Log::info($e);
            return $e;

        }

        return $filename;
    }
}

if (! function_exists('checkRolesInternal')) {

    /**
     * Generate pdf file.
     *
     * @param  string $folder
     * @param  string $filename
     * @param  string $html
     *
     * @return array
     */
    function checkRolesInternal($branch_id)
    {
        if( in_array( intval($branch_id), [ 37, 38, 39, 41, 42, 43 ] ) ) {
            $ArrRole = ['role' =>'ao','branch_id' => $branch_id ];
        } else if( in_array( intval($branch_id), [ 21, 49, 50, 51 ] ) ) {
            $ArrRole = ['role' =>'mp','branch_id' => $branch_id ];
        } else if( in_array( intval($branch_id), [ 5, 11, 12, 14, 19 ] ) ) {
            $ArrRole = ['role' =>'pinca','branch_id' => $branch_id ];
        } else if( in_array( intval($branch_id), [ 59 ] ) ) {
            $ArrRole = ['role' =>'prescreening','branch_id' => $branch_id ];
            if( in_array( strtolower($data[ 'posisi' ]), [ 'collateral appraisal', 'collateral manager' ] ) ){
                $role = str_replace(' ', '-', strtolower($data[ 'posisi' ]));
            }
        } else if( in_array( intval($branch_id), [26] ) ) {
            $ArrRole = ['role' =>'staff','branch_id' => $branch_id ];
        } else if( in_array( intval($branch_id), [18] ) ) {
            $ArrRole = ['role' =>'collateral','branch_id' => $branch_id ];
        } else {
            $ArrRole = ['role' =>'null','branch_id' => $branch_id ];
        }

        return $ArrRole;
    }
}