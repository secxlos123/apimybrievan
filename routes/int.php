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
			'only' => ['index', 'store', 'update']
		]);

		/**
		 * Route group for controller when uses trait ManageUserTrait
		 */
		Route::group(['prefix' => 'user/{model}'], function () {

			/**
			 * Route for get detail a user
			 */
			Route::get('/', 'UserController@show')->name('user.show');

			/**
			 * Route for actived a user
			 */
			Route::match(['put', 'patch'], 'actived', 'UserController@actived')->name('user.actived');
		});

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
			'only' => ['index', 'store', 'update']
		] );

		/**
		 * Route group for controller when uses trait ManageUserTrait
		 */
		Route::group(['prefix' => 'developer/{model}'], function () {

			/**
			 * Route for get detail a developer
			 */
			Route::get('/', 'DeveloperController@show')->name('developer.show');

			/**
			 * Route for actived a developer
			 */
			Route::match(['put', 'patch'], 'actived', 'DeveloperController@actived')->name('developer.actived');
		});

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
	 * Manage e-form from internal BRI
	 */
	Route::group( [ 'prefix' => 'eforms/{eform_id}' ], function () {

		/**
		 * Route for assign to AO
		 */	
		Route::post( 'disposition', 'EFormController@disposition' )->name( 'eforms.disposition' );

		/**
		 * Route for approve e-form
		 */	
		Route::post( 'approve', 'EFormController@approve' );
	} );
});
