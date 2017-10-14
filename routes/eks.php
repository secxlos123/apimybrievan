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
Route::group( [ 'prefix' => 'v1/eks', 'namespace' => 'API\v1\Eks' ], function() {

	/**
	 * Route for authentication
	 */
	Route::group( [ 'prefix' => 'auth' ], function () {
		Route::post( 'login', 'AuthController@store' );
		Route::post( 'register', 'AuthController@register' );
	} );

	/**
	 * Route for activate new customer
	 */
	Route::post( 'activate', 'AuthController@activate' );

	/**
	 * Route for customer or developer require authentication
	 */
	Route::group( [ 'middleware' => [ 'api.auth' ] ], function () {
		Route::group( [ 'prefix' => 'auth' ], function () {
			Route::post( 'register-simple', 'AuthController@update' );
			Route::post( 'register-complete', 'AuthController@update' );
			Route::delete( 'logout', 'AuthController@destroy' );
		} );

		/**
		 * Route property
		 */
		Route::resource('property', 'PropertyController', [
			'except' => [ 'create', 'edit' ],
		]);

		/**
		 * Route property type get by property
		 */
		Route::group([
			'as' => 'property.',
			'prefix' => 'property/{property}',
			'middleware' => [ 'property.access' ]
		], function() {

			Route::get('property-type', 'PropertyTypeController@index')
				->name('property-type.index');

			Route::get('property-type/{property_type}', 'PropertyTypeController@showProperty')
				->name('property-type.show');

			Route::match(['put', 'patch'], 'property-type/{property_type}', 'PropertyTypeController@updateProperty')
				->name('property-type.update');
		});

		/**
		 * Route property type
		 */
		Route::resource('property-type', 'PropertyTypeController', [
			'except' => [ 'create', 'edit' ],
		]);

		/**
		 * Route property type
		 */
		Route::resource('property-item', 'PropertyItemController', [
			'except' => [ 'create', 'edit' ],
		]);

		/**
		 * Route property type
		 */
		Route::group([
			'as' => 'property-type.',
			'prefix' => 'property-type/{property_type}',
			'middleware' => [ 'property-type.access' ]
		], function () {

			Route::get('property-item', 'PropertyItemController@index')
				->name('property-item.index');

			Route::get('property-item/{property_item}', 'PropertyItemController@showPropertyType')
				->name('property-item.show');

			Route::match(['put', 'patch'], 'property-item/{property_item}', 'PropertyItemController@updatePropertyType')
				->name('property-item.update');
		});

		/**
		 * Route group profile
		 */
		Route::group(['prefix' => 'profile'], function () {

			/**
			 * Route for get simple profile
			 */
			Route::get('/', 'ProfileController@index');

			/**
			 * Route for update profile
			 */
			Route::match(['put', 'patch'], 'update', 'ProfileController@update');
		});

	} );

	/**
	 * Route for homepage frontend
	 */
	Route::get( 'properties', 'PropertyController@nearby' );

	Route::get( 'developers', '\App\Http\Controllers\API\v1\Int\DeveloperController@index' );

	/**
	 * Route group password
	 */
	Route::group(['prefix' => 'password'], function () {

		/**
		 * Route for reset and send new password
		 */
		Route::post('reset', 'PasswordController@reset');
	});
});
