<?php

/*
|--------------------------------------------------------------------------
| API Routes For internal BRI
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
Route::group(['prefix' => 'v1/int', 'namespace' => 'API\v1',
		'middleware' => ['api.access', 'api.auth']
	], function () {

	/**
	 * Route group for namespace controller Int
	 */
	Route::group(['namespace' => 'Int'], function () {

		/**
		 * Route resource for RoleController
		 */
		Route::resource('role', 'RoleController', [
			'except' => ['edit', 'create']
		]);

		/**
		 * Route resource for Account Officers
		 */
		Route::get( 'account-officers', 'AccountOfficerController@index' );

		/**
		 * Route resource for UserController
		 */
		Route::resource('user', 'UserController', [
			'except' => ['edit', 'create', 'destroy']
		]);

		/**
		 * Route for actived a user
		 */
		Route::match(['put', 'patch'], 'user/{user}/actived', 'UserController@actived')->name('user.actived');

		/**
		 * Route resource for CustomerController
		 */
		Route::resource( 'customer', 'CustomerController', [
			'except' => [ 'edit', 'create' ]
		] );

		/**
		 * Route resource for DeveloperController
		 */
		Route::resource( 'developer', 'DeveloperController', [
			'except' => [ 'edit', 'create' ]
		] );

		/**
		 * Manage e-form from internal BRI
		 */
		Route::group( [ 'prefix' => 'eforms/{eform_id}' ], function () {
			/**
			 * Route resource for visit report management
			 */
			Route::resource( 'visit-reports', 'VisitReportController', [
				'except' => [ 'edit', 'create', 'destroy', 'index' ]
			] );
		} );
	});

	/**
	 * Route for assign to AO
	 */	
	Route::post('eforms/{eforms}/disposition', 'EFormController@disposition')
		->name('eforms.disposition');
});
