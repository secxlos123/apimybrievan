<?php

namespace App\Http\Controllers;

use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Events\Customer\CustomerRegistered;
use GuzzleHttp\Exception\RequestException;
use LaravelFCM\Message\PayloadDataBuilder;
use App\Events\Customer\CustomerRegister;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\Topics;
use Illuminate\Http\Request;
use LaravelFCM\Facades\FCM;
use GuzzleHttp\Client;
use App\Models\User;
use Activation;
use Response;
use Image;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function show($file)
    {
        $cekpdf = substr($file, -3);
        if($cekpdf == 'pdf'){
            return response()->error([
                'message' => "you can't access this site !",
            ]);
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
    public function edit($id)
    {
        //
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