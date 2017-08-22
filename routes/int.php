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
Route::group(['prefix' => 'v1/int', 'namespace' => 'API\v1\Int',
		'middleware' => ['api.access', 'api.auth']
	], function () {

	/**
	 * Route resource for RoleController
	 */
	Route::resource('role', 'RoleController', [
		'except' => ['edit', 'create']
	]);

	/**
	 * Route resource for UserController
	 */
	Route::resource('user', 'UserController', [
		'except' => ['edit', 'create', 'destroy']
	]);

	/**
	 * Route resource for UserController
	 */
	Route::get( 'account-officers', 'AccountOfficerController@index' );

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
});

Route::group( ['prefix' => 'v1/int', 'namespace' => 'API\v1', 'middleware' => [ 'api.access', 'api.auth' ] ], function () {
	Route::post( 'eforms/{id}/disposition', 'EFormController@disposition' );
} );
