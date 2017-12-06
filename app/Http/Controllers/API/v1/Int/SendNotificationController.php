<?php

namespace App\Http\Controllers\API\v1\Int;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;

use FCM;
use Sentinel;
use DB;

class SendNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function SendNotification( Request $request )
	{
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
	        'title' => 'required',
            'body' => 'required',
            'token' => 'required',
            'type' => 'required',

	    ]);

	    if ($validator->fails()) {
	        $this->throwValidationException(
	            $request, $validator
	        );
	    }
		
		$data = $request->all();

	    $notificationBuilder = new PayloadNotificationBuilder($data['title']);
		$notificationBuilder->setBody($data['body'])
						    ->setSound('default');

		$notification = $notificationBuilder->build();

		$topic = new Topics();
		$topic->topic('news');

		$topicResponse = FCM::sendToTopic($topic, null, $notification, null);
        dd($topicResponse);

    	//$push = $this->sendpush($data['title'], $data['body'], $data, $data['token']);
        /*


        return response()->success( [
            'message' => 'Sukses',
            'contents' => ['data'=>$kodepost]
        ], 200 );*/

	}

	public static function sendpush($title, $body, $dataarray, $token)
	{

	    $optionBuiler = new OptionsBuilder();
	    $optionBuiler->setTimeToLive(60 * 20);

	    $notificationBuilder = new PayloadNotificationBuilder($title);
	    $notificationBuilder->setBody($body)
	        ->setSound('');
	    $dataBuilder = new PayloadDataBuilder();
	    $dataBuilder->addData($dataarray);

	    $option = $optionBuiler->build();
	    $notification = $notificationBuilder->build();
	    $data = $dataBuilder->build();

	    $token = $token;

	    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
		return response()->success( [
				            'status' =>'1',
				            'sucess' =>$downstreamResponse->numberSuccess(),
						  	'fail' => $downstreamResponse->numberFailure(), 
						  	'msg' => $downstreamResponse->tokensWithError(),
				        ], 200 );
	    
	}


}
