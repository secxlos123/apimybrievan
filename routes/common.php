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
	 * Route for namespace API\v1\Eks
	 */
	Route::group(['namespace' => 'Eks'], function () {

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

	Route::post('dbws_mybri', 'DbwsController@getimage');

});
