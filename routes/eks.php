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
Route::group([ 'prefix' => 'v1/eks', 'namespace' => 'API\v1\Eks' ], function() {

	/**
	 * Route for authentication
	 */
	Route::group([ 'prefix' => 'auth' , 'middleware' => 'throttle-custom:5,10' ], function () {

		/**
		 * Route for post request login
		 */
		Route::post( 'login', 'AuthController@store' );

		/**
		 * Route for post request register
		 */
		Route::post( 'register', 'AuthController@register' );
	});

	/**
	 * Route for activate new customer
	 */
	Route::post( 'activate', 'AuthController@activate' );

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
	 * Route for customer for resend email
	 */
	Route::group([ 'prefix' => 'auth' ], function () {
		/**
		 * Route for customer for resend email
		 */
		Route::post( 'resend-email', 'AuthController@resendEmail' );
	} );

	/**
	 * Route for customer or developer require authentication
	 */
	Route::group([ 'middleware' => [ 'api.auth' ] ], function () {
		Route::get( 'customer-data/{ref_number}/{ids}', 'CustomerEksController@getDataCustomer' );

		/**
			Route for dashboard developer [Top 5 User List and Chart]
		**/
		Route::post('get-data-dashboard-developer', 'DashboardController@dashboard');
		Route::get('get-list-property-agen-dev', 'PropertyController@getListPropertyAgenDev');
		/**
		 * Route for customer for register simple, complete and logout
		 */
		Route::group([ 'prefix' => 'auth' ], function () {

			/**
			 * Route for customer for register simple
			 */
			Route::post( 'register-simple', 'AuthController@update' );

			/**
			 * Route for customer for register complete
			 */
			Route::post( 'register-complete', 'AuthController@update' );

			/**
			 * Route for customer for logout
			 */
			Route::delete( 'logout', 'AuthController@destroy' );

		} );

		/**
		 * Route property
		 */
		Route::resource('property', 'PropertyController', [ 'except' => [ 'create', 'edit' ] ]);
		Route::get('propertyNotifCollateral/{prop_slug}', 'PropertyController@notifCollateral');


		/**
		 * Route property type get by property
		 */
		Route::prefix('property/{property}')->name('property.')->middleware('property.access')->group(function() {

			/**
			 * Route property type get by property
			 */
			Route::get('property-type', [
				'as'   => 'property-type.index',
				'uses' => 'PropertyTypeController@index',
			]);

			/**
			 * Route property type get by property
			 */
			Route::get('property-type/{property_type}', [
				'as'   => 'property-type.show',
				'uses' => 'PropertyTypeController@showProperty'
			]);

			/**
			 * Route property type get by property
			 */
			Route::match(['put', 'patch'], 'property-type/{property_type}', [
				'as'   => 'property-type.update',
				'uses' => 'PropertyTypeController@updateProperty'
			]);
		});

		/**
		 * Route property type
		 */
		Route::resource('property-type', 'PropertyTypeController', [ 'except' => [ 'create', 'edit' ] ]);

		/**
		 * Route property type
		 */
		Route::resource('property-item', 'PropertyItemController', ['except' => [ 'create', 'edit' ] ]);

		/**
		 * Route developer agent
		 */
		Route::resource( 'developer-agent', 'DeveloperAgentController', [
			'only' => [ 'index', 'store', 'update','show' ]
		] );

		/**
		*Route developer agent banned
		*/
		Route::get('developer-agent/banned/{id}', 'DeveloperAgentController@banned')->name('banned');

		/**
		 * Route property type
		 */
		Route::prefix('property-type/{property_type}')->name('property-type.')
			->middleware('property-type.access')->group(function() {

			/**
			 * Route property type
			 */
			Route::get('property-item', [
				'as'   => 'property-item.index',
				'uses' => 'PropertyItemController@index'
			]);

			/**
			 * Route property type
			 */
			Route::get('property-item/{property_item}', [
				'as'   => 'property-item.show',
				'uses' => 'PropertyItemController@showPropertyType'
			]);

			/**
			 * Route property type
			 */
			Route::match(['put', 'patch'], 'property-item/{property_item}', [
				'as'   => 'property-item.update',
				'uses' => 'PropertyItemController@updatePropertyType'
			]);
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
			Route::match(['put', 'patch'], 'update/{type}', 'ProfileController@update');
			Route::put('password' ,'ProfileController@change_password' );
		});

		/**
		 * Route for favourite
		 * @var [type]
		 */
		Route::resource('favourite', 'FavouriteController', [
			'only' => ['store', 'show']
		]);
	});
});

Route::group([ 'prefix' => 'v1/eks', 'namespace' => 'API\v1' ], function() {
	Route::get( 'eform/{token}/{status}', 'EFormController@verify' );

	Route::group(['middleware' => 'api.auth'], function($router) {
		/**
		* Route fot get schedule
		* @var [type]
		*/
		$router->resource('schedule', 'AppointmentController', [
			'only' => ['index', 'store', 'show']
		]);

		/**
	     * User Notification
	     */
		Route::get('users/notification', [
	        'as'    => 'api.user.notification.eks',
	        'uses'  => 'NotificationController@index'
	    ]);

	    Route::get('users/notification/unread', [
	        'as'    => 'api.user.unread_notification.eks',
	        'uses'  => 'NotificationController@unread'
	    ]);

	    Route::get('users/notification/summary', [
	        'as'    => 'api.user.summary_notification.eks',
	        'uses'  => 'NotificationController@summary'
	    ]);

	    Route::get('users/notification/read/{slug}/{type}', [
	        'as'    => 'api.user.read_notification.eks',
	        'uses'  => 'NotificationController@read'
	    ]);
	    Route::group(['prefix' => 'getall'], function() {
		    /**
			 * Route for namespace API\v1\Eks
			 */
			 Route::get('developer', [
		        'uses'  => 'Int\DeveloperController@GetAllDeveloper'
		    ]);
			/**
			 * Route for namespace API\v1\Eks
			 */
			 Route::get('property', [
		        'uses'  => 'Eks\PropertyController@GetAllProperty'
		    ]);
			 /**
			 * Route for namespace API\v1\Eks
			 */
			 Route::get('property-type', [
		        'uses'  => 'Eks\PropertyTypeController@GetAllType'
		    ]);
			 /**
			 * Route for namespace API\v1\Eks
			 */
			 Route::get('property-item', [
		        'uses'  => 'Eks\PropertyItemController@GetAllItem'
		    ]);
	    });

	});
});
