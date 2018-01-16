<?php

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Storage;
use App\Models\UserNotification;
use App\Notifications\ApproveEFormCustomer;
use App\Notifications\RejectEFormCustomer;
use App\Notifications\VerificationApproveFormNasabah;
use App\Notifications\VerificationRejectFormNasabah;
use App\Notifications\CreateScheduleNotification;
use App\Notifications\UpdateScheduleNotification;
use App\Notifications\LKNEFormCustomer;
use App\Notifications\VerificationDataNasabah;
use App\Models\UserServices;
use App\Models\User;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use LaravelFCM\Facades\FCM;
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
            \Log::info("=============================exception-==========================");
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
            $ArrRole = ['role' =>'staff','branch_id' => $branch_id ];
        }

        return $ArrRole;
    }
}

if (! function_exists('get_religion')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function get_religion($key)
    {
        $data = array(
            "BUD" => "BUDHA"
            , "HIN" => "HINDU"
            , "ISL" => "ISLAM"
            , "KRI" => "KRISTEN"
            , "ZZZ" => "LAINNYA"
        );

        if ( $key != 'all' ) {
            return isset($data[$key]) ? $data[$key] : '-';
        }

        return $data;
    }
}

if (! function_exists('get_title')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function get_title($key)
    {
        $data = array(
            "S1" => "Sarjana"
            , "S2" => "Master"
            , "S3" => "Doktor"
            // , "SE" => "Sekolah"
            , "SD" => "SD"
            , "SM" => "SMP"
            , "SU" => "SMU/SMK"
            , "ZZ" => "Diploma"
        );

        if ( $key != 'all' ) {
            return isset($data[$key]) ? $data[$key] : '-';
        }

        return $data;
    }
}

if (! function_exists('get_employment')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function get_employment($key)
    {
        $data = array(
            "1" => "Pegawai Tetap"
            , "2" => "Kontrak"
            , "3" => "Honorer"
            , "4" => "Lainnya"
        );

        if ( $key != 'all' ) {
            return isset($data[$key]) ? $data[$key] : '-';
        }

        return $data;
    }
}

if (! function_exists('get_loan_history')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function get_loan_history($key)
    {
        $data = array(
            "1" => "Pernah menunggak"
            , "2" => "Debitur baru"
            , "3" => "Tidak ada tunggakan"
        );

        if ( $key != 'all' ) {
            return isset($data[$key]) ? $data[$key] : '-';
        }

        return $data;
    }
}

if (! function_exists('pushNotification')) {

    /**
     * Convert csv file to array.
     *
     * @param  string $file path to file
     * @param  array $headers
     * @param  string $delimiter
     *
     * @return array
     */
    function pushNotification($credentials, $type)
    {
        if(env('PUSH_NOTIFICATION', false)){
            if($type == 'disposition'){
                disposition($credentials);
            }else if($type == 'createEForm'){
                createEForm($credentials);
            }else if($type == 'approveEForm'){
                approveEForm($credentials);
            }else if($type == 'rejectEForm'){
                rejectEForm($credentials);
            }else if($type == 'approveKPR'){
                approveKPR($credentials);
            }else if($type == 'rejectKPR'){
                rejectKPR($credentials);
            }else if($type == 'lknEForm'){
                lknEForm($credentials);
            }else if($type == 'createSchedule'){
                createSchedule($credentials);
            }else if($type == 'updateSchedule'){
                updateSchedule($credentials);
            }else if($type == 'verifyCustomer'){
                verifyCustomer($credentials);
            }else if ($type =='general'){
                collateralNotification($credentials);
            } 
        }
    }

    function verifyCustomer($credentials){
        $dataUser = $credentials['data'];

        $userModel  = User::FindOrFail($dataUser['user_id']);
        $userModel->notify(new VerificationDataNasabah($dataUser));

        $notificationBuilder = new PayloadNotificationBuilder('Verify Notification');
        $notificationBuilder->setBody('Silahkan Verifikasi Data Anda.')
                            ->setSound('default');

        $notificationData = UserNotification::where('eform_id', $dataUser['id'])
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $dataUser['id'],
            'type'     => 'schedule',
        ]);
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();

        $topic = new Topics();
        $topic->topic('testing')->andTopic('user_'.$dataUser['user_id']);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function updateSchedule($credentials){
        $dataUser = $credentials['data'];

        $userModel  = User::FindOrFail($dataUser['user_id']);
        $userModel->notify(new UpdateScheduleNotification($dataUser));

        $notificationBuilder = new PayloadNotificationBuilder('Schedule Notification');
        $notificationBuilder->setBody('Schedule anda telah di update.')
                            ->setSound('default');

        $notificationData = UserNotification::where('eform_id', $dataUser['eform_id'])
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $dataUser['eform_id'],
            'type'     => 'schedule',
        ]);
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();

        $topic = new Topics();
        $topic->topic('testing')->andTopic('user_'.$dataUser['user_id']);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function createSchedule($credentials){
        $dataUser = $credentials['data'];

        $userModel  = User::FindOrFail($dataUser['user_id']);
        $userModel->notify(new CreateScheduleNotification($dataUser));

        $notificationBuilder = new PayloadNotificationBuilder('Schedule Notification');
        $notificationBuilder->setBody('Anda memiliki schedule baru.')
                            ->setSound('default');


        $notificationData = UserNotification::where('eform_id', $dataUser['eform_id'])
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $dataUser['eform_id'],
            'type'     => 'schedule',
        ]);
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();

        $topic = new Topics();
        $topic->topic('testing');
        // topic('user_'.$save['user_id']);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function lknEForm($credentials){
        $data         = $credentials;
        $userLogin    = $data['credentials'];
        $branch_id    = $userLogin['branch_id'];
        $userModel    = $data['user'];
        $userNotif    = new UserNotification;

        $userModel->notify(new LKNEFormCustomer($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
        $notificationBuilder->setBody('Data LKN berhasil dikirim')
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('eform_id', $data['data']->id)
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $data['data']->id,
            'type'     => 'eform',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic = new Topics();
        $topic->topic('testing')->orTopic('branch_'.$branch_id)->orTopic('pinca');

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();

    }

    function disposition($credentials){
        $data      = $credentials['eform'];
        $aoId      = $credentials['ao_id'];
        $userNotif = new UserNotification;

        $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
        $notificationBuilder->setBody('E-Form berhasil di disposisi')
                            ->setSound('default');
        // Get data from notifications table
        $notificationData = $userNotif->where('eform_id', $data->id)
                                        ->orderBy('created_at', 'desc')->first();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $data->id,
            'type'     => 'eform',
        ]);
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic = new Topics();
        $topic->topic('testing')->orTopic('ao_'.$aoId);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function createEForm($credentials){
        $dataUser = $credentials;
        $user     = $dataUser['request']->user();
        if($user != null){
            $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
            $notificationBuilder->setBody('Pengajuan KPR Baru')
                                ->setSound('default');
            // Get data from notifications table
            $notificationData = UserNotification::where('eform_id', $dataUser['data']->id)
                                            ->orderBy('created_at', 'desc')->first();

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'id'   => $notificationData['id'],
                'slug' => $dataUser['data']->id,
                'type' => 'eform',
            ]);

            $notification = $notificationBuilder->build();
            $data         = $dataBuilder->build();
            $topic        = new Topics();

            $topic->topic('testing')->orTopic('branch_'.$dataUser['data']->branch_id)->orTopic('pinca');

            $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
            $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();
        }else{
            $user_login = \RestwsHc::getUser();
            if($user_login['role'] == 'staff'){
                $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
                $notificationBuilder->setBody('Pengajuan KPR Baru')
                                    ->setSound('default');
                // Get data from notifications table
                $notificationData = UserNotification::where('eform_id', $dataUser['data']->id)
                                                ->orderBy('created_at', 'desc')->first();

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData([
                    'id'   => $notificationData['id'],
                    'slug' => $dataUser['data']->id,
                    'type' => 'eform',
                ]);

                $notification = $notificationBuilder->build();
                $data         = $dataBuilder->build();
                $topic        = new Topics();

                $topic->topic('testing')->andTopic(function($condition) use ($dataUser) {
                    // send to user
                    $condition->topic('user_'.$dataUser['data']->user_id);
                })->andTopic(function($condition) use ($dataUser){
                    // send to pinca
                    $condition->topic('branch_'.$dataUser['data']->branch_id)->andTopic('pinca');
                });

                $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
                $topicResponse->isSuccess();
                $topicResponse->shouldRetry();
                $topicResponse->error();
            }else if($user_login['role'] == 'ao'){
                $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
                $notificationBuilder->setBody('Pengajuan KPR Baru')
                                    ->setSound('default');
                // Get data from notifications table
                $notificationData = UserNotification::where('eform_id', $dataUser['data']->id)
                                                ->orderBy('created_at', 'desc')->first();

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData([
                    'id'   => $notificationData['id'],
                    'slug' => $dataUser['data']->id,
                    'type' => 'eform',
                ]);

                $notification = $notificationBuilder->build();
                $data         = $dataBuilder->build();
                $topic        = new Topics();

                $topic->topic('testing')->orTopic('branch_'.$dataUser['data']->branch_id)->orTopic('pinca');

                $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
                $topicResponse->isSuccess();
                $topicResponse->shouldRetry();
                $topicResponse->error();
            }
        }
    }

    function approveEForm($credentials){
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        $userModel->notify(new ApproveEFormCustomer($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
        $notificationBuilder->setBody('Pengajuan anda telah di Setujui.')
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('eform_id', $data['data']->id)
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'eform',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        $topic->topic('testing')->andTopic('user_'.$userId);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function rejectEForm($credentials){
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        $userModel->notify(new RejectEFormCustomer($data['data']));
        
        $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
        $notificationBuilder->setBody('Pengajuan anda telah di Ditolak.')
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('eform_id', $data['data']->id)
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'   => $notificationData['id'],
            'slug' => $data['data']->id,
            'type' => 'eform',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        $topic->topic('testing')->andTopic('user_'.$userId);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function approveKPR($credentials){
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $credentials['user'];
        $userNotif = new UserNotification;

        $userModel->notify(new VerificationApproveFormNasabah($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
        $notificationBuilder->setBody('Pengajuan KPR telah di Setujui.')
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('eform_id', $data['data']->id)
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $data['data']->id,
            'type'     => 'eform',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        $topic->topic('testing')->andTopic('user_'.$userId);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function rejectKPR($credentials){
        $data      = $credentials;
        $userId    = $data['data']->user_id;
        $userModel = $data['user'];
        $userNotif = new UserNotification;

        $userModel->notify(new VerificationRejectFormNasabah($data['data']));

        $notificationBuilder = new PayloadNotificationBuilder('EForm Notification');
        $notificationBuilder->setBody('Pengajuan KPR di Ditolak.')
                            ->setSound('default');

        // Get data from notifications table
        $notificationData = $userNotif->where('eform_id', $data['data']->id)
                                        ->orderBy('created_at', 'desc')->first();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $notificationData['id'],
            'slug'     => $data['data']->id,
            'type'     => 'eform',
        ]);

        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic        = new Topics();

        $topic->topic('testing')->andTopic('user_'.$userId);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    function collateralNotification($credentials){
       $user_id= $credentials['user_id']; 
       $bodyNotif= $credentials['bodyNotif']; 
       $headerNotif= $credentials['headerNotif']; 
       $id = $credentials['id']; 
       $type= $credentials['type']; 
       $slug = $credentials['slug']; 
       $receiver = $credentials['receiver']; 

        $notificationBuilder = new PayloadNotificationBuilder($headerNotif);
        $notificationBuilder->setBody($bodyNotif)
                              ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'id'       => $id,
            'slug'     => $slug,
            'type'     => $type,
        ]);
                              
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();
        $topic = new Topics();
        if($receiver=='staf_collateral'){   
             $dataUser  = UserServices::where('pn',$user_id)->first();
             $branch_id = $dataUser['branch_id'];
             $topic->topic('testing')->andTopic('branch_'.$branch_id)->andTopic('staff_collateral_'.$user_id);
        }else if ($receiver=='external'){  //send to external mobile apps
             $topic->topic('testing')->andTopic('user_'.$user_id);
        }else if ($receiver=='manager_collateral'){
             $dataUser  = UserServices::where('pn',$user_id)->first();
             $branch_id = $dataUser['branch_id'];
             $topic->topic('testing')->andTopic('branch_'.$branch_id)->andTopic('manager_collateral_'.$user_id);
        }
        $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);
        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error(); 
    }

    


}