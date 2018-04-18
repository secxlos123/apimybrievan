<?php

/*
|--------------------------------------------------------------------------
| API Routes For common data
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Route for handling common data guest user
 */
Route::group([ 'prefix' => 'v1/common', 'namespace' => 'API\v1' ], function () {
	/**
     * User Notification
     */
	Route::get('users/notification', [
        'as'    => 'api.user.notification.eks',
        'uses'  => 'NotificationController@index'
    ]);

    Route::get('users/notification/unread', [
        'as'    => 'api.user.unread_notification.eks',
        'uses'  => 'NotificationController@unread'
    ]);

    Route::get('users/notification/summary', [
        'as'    => 'api.user.summary_notification.eks',
        'uses'  => 'NotificationController@summary'
    ]);

    Route::get('users/notification/read/{slug}/{type}', [
        'as'    => 'api.user.read_notification.eks',
        'uses'  => 'NotificationController@read'
    ]);

    Route::get('list_developer', 'DropdownController@list_developer');

    Route::get('list_proptype', 'DropdownController@list_proptype');

    Route::get('mobile/list-notification', 'NotificationController@unreadMobile');
	/**
	 * Route for namespace API\v1\Eks
	 */
	Route::group(['namespace' => 'Eks'], function () {

		/**
		 * Route for Calculator
		 **/
		Route::post('calculator', 'CalculatorController@calculate');
		
		/**
		 * Route for get nearby properties
		 */
		Route::get('nearby-properties', 'PropertyController@nearby');

		/**
		 * Route resource for get property and detail property
		 */
		Route::resource('property', 'PropertyController', [ 'only' => ['index', 'show'] ]);

		/**
		 * Route resource for get property type and detail property type
		 */
		Route::resource('property-type', 'PropertyTypeController', [ 'only' => ['index', 'show'] ]);

		/**
		 * Route resource for get property item and detail property item
		 */
		Route::resource('property-item', 'PropertyItemController', [ 'only' => ['index', 'show'] ]);
	});

	/**
	 * Route for namespace API\v1\Int
	 */
	Route::group(['namespace' => 'Int'], function () {

		/**
		 * Route for get list developers
		 */
		Route::get('developers/{id?}', 'DeveloperController@index');
	});

	/**
	 * Route for get list cms product
	 */
	Route::get('cmsproduct', 'ContentMYBRIController@index');
	Route::post('detailproduct', 'ContentMYBRIController@detail');

	Route::post('dbws_mybri', ['uses'=>'DbwsController@getimage','middleware'=>'ipcheck']);

});
