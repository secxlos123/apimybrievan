<?php

namespace App\Http\Controllers;

use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Events\Customer\CustomerRegistered;
use GuzzleHttp\Exception\RequestException;
use LaravelFCM\Message\PayloadDataBuilder;
use App\Events\Customer\CustomerRegister;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\Topics;
use App\Models\UserServices;
use Illuminate\Http\Request;
use App\Models\PropertyType;
use App\Models\PropertyItem;
use LaravelFCM\Facades\FCM;
use App\Models\Developer;
use App\Models\Property;
use GuzzleHttp\Client;
use App\Models\EForm;
use App\Models\User;
use Activation;
use Response;
use Session;
use Image;

class ImagesController extends Controller
{
    /**
     * This function for delete developer dummy
     * @author rangga darmajati (rangga.darmajati@wgs.co.id)
     * @param  $dev_name
     * @return \Illuminate\Http\Response
     */
    public function DeleteDeveloperDummy($dev_name)
    {
        $lowerValue = '%'.strtolower($dev_name).'%';
        $dev = Developer::where(\DB::raw('LOWER(company_name)'), 'like', $lowerValue)->first();
        $dev_user_id = $dev->user_id;
        $dev_id = $dev->id;
        $user = User::where('id', $dev_user_id)->first();
        $property = Property::where('developer_id', $dev_id)->get();
        foreach ($property as $key => $value) {
            $property_type = PropertyType::where('property_id', $value['id'])->get();
            foreach ($property_type as $key1 => $value1 ) {
                $property_item = PropertyItem::where('property_type_id', $value1['id'])->delete();
            }

            PropertyType::where('property_id', $value['id'])->delete();    
        }

        Property::where('developer_id', $dev_id)->delete();
        Developer::where('id', $dev_id)->delete();
        User::where('id', $dev_user_id)->delete();

         return response()->success([
                'message' => "Developer Dummy Berhasil dihapus!",
                'contents' => ["user" => $user]
           ], 200);
    }

    /**
     * This function for show developer dummy
     * @author rangga darmajati (rangga.darmajati@wgs.co.id)
     * @param  $dev_name
     * @return \Illuminate\Http\Response
     */
    public function ShowDeveloperDummy($dev_name)
    {
        $lowerValue = '%'.strtolower($dev_name).'%';
        $dev = Developer::where(\DB::raw('LOWER(company_name)'), 'like', $lowerValue)->first();
        $dev_user_id = $dev->user_id;
        $dev_id = $dev->id;
        $user = User::where('id', $dev_user_id)->first();
        $property = Property::where('developer_id', $dev_id)->get();

        return response()->success([
            'message' => "Developer Dummy Berhasil ditemukan!",
            'contents' => [
                 "user" => $user
                ,"developer" => $dev
                ,"Property" => $property
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $file)
    {
        if($request->has('password_access')){
            $date        = date('dmy');
            $secret_code = "mybri";
            $key         = $date.$secret_code;

            $getUserInt  = UserServices::where('pn', $request->input('pn_access'))
                           ->where('password', md5($request->input('password_access')))->first();
            // $getUser     = User::where('email', $request->input('pn_access'))
            //                ->where('password', bcrypt($request->input('password_access')))->first();
            if($getUserInt){
                return response()->download(public_path('uploads/'.$file), null, [], null);
            }else{
                $data = $file;
                $error= "Invalid Credentials";
                return view('security', compact('data', 'error'));
            }

            // if($request->input('password_access') == $key){
            //     return response()->download(public_path('uploads/'.$file), null, [], null);
            // }else{
            //     $data = $file;
            //     return view('security', compact('data'));    
            // }
        }

        $cekpdf = substr($file, -3);
        if($cekpdf == 'pdf'){
            
            $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
            if($secure != 1){
                return response()->download(public_path('uploads/'.$file), null, [], null);
            }else{

                $data  = $file;
                return view('security', compact('data'));
                
            }
        }else{
            $storagePath = public_path('uploads/'.$file);
            return Image::make($storagePath)->response();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show2($nik, $pdf, $key)
    {
        $date = date('dmy');
        \Log::info($date);
        if($key == "key=".$date){
            return response()->download(public_path('uploads/'.$nik.'/'.$pdf), null, [], null);
        }else{
            return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
           ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show3(Request $request, $folder, $file)
    {
        $cekpdf = substr($file, -3);
        if($cekpdf == 'pdf'){
            \Log::info("========MASUK KESINI GAK (PDF)================");
            $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
            \Log::info($secure);
            if($secure == 1 ){
                $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
                    // return response()->download(public_path('uploads/'.$folder.'/'.$file), null, [], null);
                    return Response::download(public_path('uploads/'.$folder.'/'.$file), null, [], null);
                }else{
                    return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
                ]);
            }
        }
        
        $header = $request->ip();
        $server = $request->server();
        $ip = env('ACCESS_CLAS_IP', '127.0.0.1');
        $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
        
        if($secure != 1 ){
            $storagePath = public_path('uploads/'.$folder.'/'.$file);
            return Image::make($storagePath)->response();
        }else{
            return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
           ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show4(Request $request, $folder, $id, $file)
    {
        $header = $request->ip();
        $server = $request->server();
        $ip = env('ACCESS_CLAS_IP', '127.0.0.1');
        $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;

        if($secure != 1 ){
            $storagePath = public_path('uploads/'.$folder.'/'.$id.'/'.$file);
            return Image::make($storagePath)->response();
        }else{
            return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
           ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show5(Request $request, $folder, $other, $id, $file)
    {
        $header = $request->ip();
        $server = $request->server();
        $ip = env('ACCESS_CLAS_IP', '127.0.0.1');
        $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
    
        if($secure != 1 ){
            $storagePath = public_path('uploads/'.$folder.'/'.$other.'/'.$id.'/'.$file);
            return Image::make($storagePath)->response();
        }else{
            return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
           ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $type_notif)
    {
        // $user_login = \RestwsHc::getUser();
        $EForm = EForm::with('customer')->findOrFail($id);
        $Eform = EForm::findOrFail($id);
        // $eform = Eform::with('customer')->where('id',$id)->first();
        // $msgData = [
        //     'customer_name' => $eform['customer']['first_name'].' '.$eform['customer']['last_name'],
        //     'user_login' => 'JHONY ISKANDARIA NEGARA',
        //     'ref_number' => $eform->ref_number
        // ];
        // dd($msgData);
        // dd($eform['customer']['first_name'].' '.$eform['customer']['last_name'].'  user_login :'.$user_login['name']);
        $usersModel = User::FindOrFail($EForm->user_id);
        $user_login = [
            'branch_id' => '012',
            'pn' => '00066777'
        ];
        $data = [
            'eform' => $EForm,
            'data'  => $Eform,
            'user' => $usersModel,
            'credentials' => $user_login
        ];
        //dd($data['data']->customer->first_name.' '.$data['data']->customer->last_name);
        // dd($data['data']->ref_number);
        // $test = $data['data'];
        // dd($test->pefindo_score_all->individual);
        // $result = $this->getMessage('eform_pencairan', $data);
        \Log::info('============'.$type_notif.'===============');
        pushNotification($data, $type_notif);
        $result = $this->TesData($data, 'disposition');
        // dd($result);
        return response()->success([
            'message' => 'Berhasil',
            'contents' => $result
            ], 200);
        // if($data){
        //     return response()->success([
        //     'message' => "Data Berhasil ditemukan!",
        //     'contents' => $data
        //     ], 200);    
        // }else{
        //     return response()->error([
        //         'message' => "Data tidak ditemukan!",
        //         'contents' => null
        //     ], 404);
        // }

        
    }

    public function TesData($credentials, $type){
       if($type == 'disposition'){
        //\Log::info("===MASUK PUSH_NOTIFICATION DISPOSISI===");
           $message = $this->disposition($credentials);
       }elseif($type == 'pencairan'){
           $message = $this->pencairanEForm($credentials);
       }
       return $message;
    }

    public function disposition($credentials){
        // dd($credentials);
       // \Log::info("===MASUK PROSES PUSH_NOTIFICATION DISPOSISI===");
        $message = $this->getMessage('eform_disposition', $credentials);
        return $message;   
    }

    public function getMessage($type, $credentials = NULL){
        switch ($type) {
            case 'eform_pencairan':
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Pengajuan : '.$credentials['eform']->ref_number.' telah dicairkan.',
                ];
                break;
            case 'eform_disposition':
         //   \Log::info("===MASUK DISPOSISI Message===");
            // \Log::info($credentials->product_type);
                // dd($credentials['eform']->customer->first_name);
                $name = $credentials['eform']->customer->first_name.' '.$credentials['eform']->customer->last_name;
                $message = [
                    'title'=> 'EForm Notification',
                    'body' => 'Disposisi Pengajuan '.$credentials['eform']->product_type.' a.n '.$name.' '.$credentials['eform']->ref_number.'. Segera TL!!',
                ]; 
                // dd($message);
                break;
            
            default:
                $message = [
                    'title'=> NULL,
                    'body' => NULL
                ]; 
                break;
        }
        return $message;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * resend email Activation
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function ResendActivations(Request $request, $email)
    {
        $user = User::whereEmail( $email )->first();
        if(count($user) <= 0){
            return response()->error( [
                'message' => 'Email Tersebut tidak ditemukan!',
                'contents' => null
            ], 404 );    
        }
        $activation = Activation::where('user_id', '=', $user->id)->first();
        Activation::complete($user, $activation->code);
        event( new CustomerRegister( $user, $activation->code ) );

        return response()->success( [
            'message' => 'Resend Activations Sukses',
            'contents' => $user
        ], 201 );
    }

    /**
     * resend email For Developer and Agen Developer
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function ResendPass(Request $request, $email)
    {
        $role_id = \Sentinel::findRoleBySlug('developer-sales')->id;
        $user = User::whereEmail( $email )->first();
        if(count($user) <= 0){
            return response()->error( [
                'message' => 'Email Tersebut tidak ditemukan!',
                'contents' => null
            ], 404 );    
        }
        // $password = $this->randomPassword(8,"lower_case,upper_case,numbers");
        $password = '123Mybri';
        \Log::info("EMAIL BERHASIL TERKIRIM");
        // \Log::info("P: ".$password);
        $user->update(['password' => bcrypt($password)]);
        event(new CustomerRegistered($user, $password,$role_id));
        return response()->success( [
            'message' => 'Resend Pass Sukses',
            'contents' => [
                'data' => $user,
                // 'password' => $password
            ]
        ], 201 );
    }

    /**
     * Generate Random Password
     * @param  [type] $length     [description]
     * @param  [type] $characters [description]
     * @return [type]             [description]
     */
    public function randomPassword($length,$characters) {
        // $length - the length of the generated password
        // $characters - types of characters to be used in the password
        // define variables used within the function
        $symbols = array();
        $passwords = array();
        $used_symbols = '';
        $pass = '';
        // an array of different character types
        $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
        $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols["numbers"] = '1234567890';
        $symbols["special_symbols"] = '!?~@#-_+<>[]{}';
        $characters = explode(",",$characters); // get characters types to be used for the passsword
        foreach ($characters as $key=>$value) {
            $used_symbols .= $symbols[$value]; // build a string with all characters
        }
        $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1
            for ($i = 0; $i < $length; $i++) {
                $n = rand(0, $symbols_length); // get a random character from the string with all characters
                $pass .= $used_symbols[$n]; // add the character to the password string
            }

        return $pass; // return the generated password
    }

    /**
     * Check Service Email Restwshc
     * @param email
     */
    public function testEmailService($email){
        try {
            $client = new Client();
            $host = config('restapi.restwshc');
            \Log::info($host.'/send_emailv2');    
            $res = $client->request('POST', $host.'/send_emailv2', [
                    'form_params' => [
                        "headers" => [
                            "Content-type" => "application/x-www-form-urlencoded"
                        ]
                        , "app_id"  => "mybriapi"
                        , "subject" => "Test Service Email Restwshc"
                        , "content" => "TEST SERVICE EMAIL RESTWSHC SUCCESS (".$host."/send_emailv2)"
                        , "to" => $email
                    ],
                ]);

            $data = json_decode($res->getBody()->getContents(), true);
            \Log::info("===SUCCESS SEND MAIL FROM SERVICE RESTWSHC===");
            return $data;
            
        } catch (RequestException $e) {
            \Log::info("===FAILED SEND MAIL FROM SERVICE RESTWSHC===");
            return response()->error( [
                'message' => 'Error Service Mail Restwshc',
                'contents' => [
                    'data' => $e->getMessage(),
                    ]
            ], 201 );
        }
    }

    /**
     * Check Service Push Notifications
     * @param $topic
     */
    public function TestPushNotif($topic){
        try {
            \Log::info(env('PUSH_NOTIFICATION', false));
            if(env('PUSH_NOTIFICATION') == false){
                return "PLEASE Turn On Notification Service";
            }
            $TP = (String) $topic;
            $notificationBuilder = new PayloadNotificationBuilder('MYBRI');
            $notificationBuilder->setBody('TEST NOTIFICATIONS - IGNORE THIS NOTIFICATIONS')
                                ->setSound('default');

            $notification = $notificationBuilder->build();

            $topic = new Topics();
            $topic->topic($TP);

            $topicResponse = FCM::sendToTopic($topic, null, $notification, null);

            $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();
            
            return response()->success( [
                'message' => 'Success Service FCM PUSH NOTIFICATIONS TOPIC ('.$TP.')',
                'contents' => [
                    'data' => 'TEST PUSH NOTIFICATIONS',
                    ]
            ], 200 );
            
        } catch (RequestException $e) {
            \Log::info("===FAILED SEND PUSH_NOTIFICATION===");
            return response()->error( [
                'message' => 'Error Service FCM PUSH NOTIFICATIONS TOPIC ('.$TP.')',
                'contents' => [
                    'data' => $e->getMessage(),
                    ]
            ], 500 );
        }
    }
}