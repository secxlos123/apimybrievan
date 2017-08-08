<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
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
Route::group(['prefix' => 'v1', 'namespace' => 'API\v1'], function () {

	/**
	 * Route group for type of user in : int and eks
	 */
	Route::group(['prefix' => '{type}', 'middleware' => 'api.access'], function () {
		
		/**
		 * Route group auth
		 */
		Route::group(['prefix' => 'auth'], function () {

			/**
			 * Route for login
			 */
			Route::post('login', 'AuthController@authenticate');

			/**
			 * Route for register
			 */
			Route::post('register', 'AuthController@register');

			/**
			 * Route for logout
			 */
			Route::delete('logout', 'AuthController@logout')->middleware(['api.auth']);
		} );
	});
});
