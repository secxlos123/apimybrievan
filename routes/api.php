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

	Route::group( [ 'prefix' => '{type}', 'middleware' => 'api.auth' ], function () {
		Route::get( 'job-list', 'JobController@index' );
		Route::get( 'job-field-list', 'JobFieldController@index' );
		Route::get( 'job-type-list', 'JobTypeController@index' );
		Route::get( 'citizenship-list', 'CitizenshipController@index' );

		Route::resource( 'eforms', 'EFormController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );

		Route::get( 'cities', 'CityController' );

		Route::get( 'offices', 'OfficeController@index' );
	} );

	/**
	 * Route group for type of user in : int and eks
	 * NB : BUAT APA MENGGUNAKAN API.ACCESS DAN API.AUTH? APA PERBEDAAN KEDUANYA?
	 */
	Route::group(['prefix' => '{type}', 'middleware' => 'api.access'], function () {

		/**
		 * Route for user activation
		 */
		Route::post( 'activate', 'AuthController@activate' );
		
		/**
		 * Route group auth
		 */
		Route::group(['prefix' => 'auth'], function () {
			
			/**
			 * Route for register
			 */
			Route::post( 'register', 'AuthController@register' );
			Route::post( 'register-simple', 'AuthController@registerComplete' )->middleware( [ 'api.auth' ] );
			Route::post( 'register-complete', 'AuthController@registerComplete' )->middleware( [ 'api.auth' ] );
		});
	});

	Route::group( [ 'namespace' => 'Int' ], function () {
		Route::get( 'list-developer', 'DeveloperController@index' );
	} );

	Route::put( 'eforms/submit-screening', 'EFormController@submitScreening' );
});
