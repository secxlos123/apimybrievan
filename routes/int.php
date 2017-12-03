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

Route::group( [ 'prefix' => 'v1/int', 'namespace' => 'API\v1\Int' ], function () {
	Route::post( 'auth/login', 'AuthController@store' );

	// route that require login session
	Route::group( [ 'middleware' => [ 'api.auth' ] ], function () {
		Route::get( 'check-token', 'TokenController@index' );

		Route::group( [ 'prefix' => 'auth' ], function () {
			Route::delete( 'logout', 'AuthController@destroy' );
		} );

		Route::resource( 'developer', 'DeveloperController', [
			'only' => [ 'index', 'store', 'update' ]
		] );

		/**
		* Route ThirdParty (pihak ke-3)
		*/
		Route::resource('thirdparty', 'ThirdpartyController',[
			'only' => ['index','store', 'update']
		]);

		/**
		* Route collateral (Collateral)
		*/
		// Route::resource('collateral', 'CollateralController',[
		// 	'only' => ['index','show']
		// ]);

		Route::resource( 'customer', 'CustomerController', [
			'only' => [ 'destroy' ]
		] );

		Route::put( 'customers/{id}/verify', 'CustomerController@verify' );

		Route::group( [ 'prefix' => 'eforms/{eform_id}' ], function () {
			Route::resource( 'visit-reports', 'VisitReportController', [
				'only' => [ 'store' ]
			] );
			Route::group( [ 'prefix' => 'verification' ], function () {
				Route::post( 'show', 'VerificationController@show' );
			} );
		} );

		Route::group( [ 'prefix' => 'verification' ], function () {
			Route::post( 'search-nik', 'VerificationController@searchNik' );
		} );
		Route::get( 'staff-list', 'StaffController@index' );



		Route::resource( 'scorings', 'ScoringController', [
			'except' => [ 'edit', 'create' ]
		] );
		
		Route::post('select', 'SelectController@select');
	} );
} );

Route::group(['prefix' => 'v1/int', 'namespace' => 'API\v1',
		'middleware' => ['api.auth']
	], function () {

		/**
		 * Route For Prescreening
		 */
		Route::resource( 'prescreening', 'PrescreeningController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );

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
		 * Route group for controller when uses trait ManageUserTrait
		 */
		Route::group(['prefix' => 'developer/{model}'], function () {

			/**
			 * Route for get detail a developer
			 */
			Route::get('/', 'DeveloperController@show')->name('developer.show');

			/**
			 * Route for get detail a developer
			 */
			Route::get('properties', 'DeveloperController@properties')->name('developer.properties');

			/**
			 * Route for actived a developer
			 */
			Route::match(['put', 'patch'], 'actived', 'DeveloperController@actived')->name('developer.actived');
		});

		/**
		 * Route group for controller when uses trait ManageUserTrait
		 */
		Route::group(['prefix' => 'thirdparty/{model}'], function () {

			/**
			 * Route for get detail a pihak ke-3
			 */
			Route::get('/', 'ThirdpartyController@show')->name('thirdparty.show');

			/**
			 * Route for actived a pihak ke-3
			 */
			Route::match(['put', 'patch'], 'actived', 'ThirdpartyController@actived')->name('thirdparty.actived');
		});


	});

	/**
	 * Manage e-form from internal BRI
	 */
	Route::group( [ 'prefix' => 'eforms/{eform_id}' ], function () {
		Route::post( 'disposition', 'EFormController@disposition' )->name( 'eforms.disposition' );
		Route::post( 'approve', 'EFormController@approve' );
		Route::post( 'step-{step_id}', 'EFormController@insertCoreBRI' ); // step id must between 1 - 7
	} );

});
