<?php

namespace App\Http\Controllers\API\v1\Int;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        dd($request->all());
        /*$validator = Validator::make($request->all(), [
	        'title' = > 'required',
            'body' = > 'required',
            'token' = > 'required',
            'type' = > 'required',
            'id' = > 'required',

	    ]);

	    if ($validator->fails()) {
	        $this->throwValidationException(
	            $request, $validator
	        );
	    }

	    $title = $request['title'];
	    $body = $request['body'];
	    $type = $request['type'];
	    $id = $request['id'];
	    $dataarray = array(
	        "id" = >$id,
	        "type" = >$type,
	        'title' = >$title,
	        'body' = >$body,
	        'image' = >'321451_v2.jpg',
	    );

	    $token = $request['token'];

    	$push = Push::sendpush($title, $body, $dataarray, $token);
        return response()->success( [
            'message' => 'Sukses',
            'contents' => ['data'=>$kodepost]
        ], 200 );*/

	}

	/*public static function sendpush($title, $body, $dataarray, $token)
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

	    return new JsonResponse(array('status' = >'1', 'sucess' = >$downstreamResponse->numberSuccess(), 'fail' = > $downstreamResponse->numberFailure(), 'msg' = >$downstreamResponse->tokensWithError()), 200);

	}*/


}
