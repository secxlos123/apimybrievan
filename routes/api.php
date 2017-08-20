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
Route::post( 'urgent-function', 'RemovableController@run' );

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
			Route::post( 'register', 'AuthController@register' );
			Route::post( 'register-complete', 'AuthController@registerComplete' )->middleware( [ 'api.auth' ] );

			/**
			 * Route for logout
			 */
			Route::delete('logout', 'AuthController@logout')->middleware(['api.auth']);
		});

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
		 * Route group for logged in users
		 */
		Route::group( [ 'middleware' => 'api.auth' ], function() {

			/**
			 * Route resource for e-form
			 */
			Route::resource( 'eforms', 'EFormController', [
				'except' => [ 'edit', 'create', 'destroy' ]
			] );

			/**
			 * Route for get list of cities
			 */
			Route::get('cities', 'CityController');
		} );
	});
});
