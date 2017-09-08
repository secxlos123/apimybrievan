<?php

/*
|--------------------------------------------------------------------------
| API Routes For eksternal BRI
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Route group for api v1
 */
Route::group( [ 'prefix' => 'v1/eks', 'namespace' => 'API\v1\Eks', 'middleware' => ['api.access'] ], function() {
	Route::post( 'auth/login', 'AuthController@store' );

	// route that required for login
	Route::group( [ 'middleware' => [ 'api.auth' ] ], function () {
		Route::group( [ 'prefix' => 'auth' ], function () {
			Route::delete( 'logout', 'AuthController@destroy' );
		} );
	} );

	/**
	 * Route group password
	 */
	Route::group(['prefix' => 'password'], function () {

		/**
		 * Route for reset and send new password
		 */
		Route::post('reset', 'PasswordController@reset');
	});

	/**
	 * Route group profile
	 */
	Route::group(['prefix' => 'profile', 'middleware' => ['api.auth'] ], function () {

		/**
		 * Route for get simple profile
		 */
		Route::get('/', 'ProfileController@index');

		/**
		 * Route for update profile
		 */
		Route::match(['put', 'patch'], 'update', 'ProfileController@update');
	});
});
