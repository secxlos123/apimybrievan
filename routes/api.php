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
		/* BRIGUNA */
	Route::group( [ 'prefix' => '{type}', 'middleware' => 'api.auth' ], function () {
		Route::post('select', 'SelectController@select');
		Route::post('mitra_relation', 'EFormController@mitra_relation');
		Route::post('Download_Rekomendasi', 'Download_RekomendasiController@Download');
		Route::get( 'positions', 'PositionController@index' );
		Route::get( 'job-list', 'JobController@index' );
		Route::get( 'job-field-list', 'JobFieldController@index' );
		Route::get( 'job-type-list', 'JobTypeController@index' );
		Route::get( 'citizenship-list', 'CitizenshipController@index' );
		Route::get( 'kpp-type-list', 'KPPController@index' );
		Route::get( 'list-jenis-dibiayai', 'CostTypeController@index' );
		Route::get( 'program-list', 'ProgramController@index' );
		Route::get( 'project-list', 'ProjectController@index' );
		Route::get( 'economy-sectors', 'EconomySectorController@index' );
		Route::get( 'use-reasons', 'UseReasonController@index' );
		Route::get( 'kanwil-list', 'KanwilController@index' );
		Route::get( 'kemendagri', 'CustomerController@kemendagri' );
		Route::get( 'customer-bri', 'CustomerController@customer' );

		Route::resource( 'eforms', 'EFormController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );

		Route::get( 'offices', 'OfficeController@index' );

		Route::group(['prefix' => 'dropdown'], function () {
			Route::get('properties', 'DropdownController@properties');
			Route::get('types', 'DropdownController@types');
			Route::get('units', 'DropdownController@items');
		});

		// Dropbox
		Route::group(['prefix' => 'dropbox'], function () {
			Route::post('index', 'DropboxController@index');
		});

		// API LAS
		Route::group(['prefix' => 'api_las'], function () {
			Route::post('index', 'ApiLasController@index');
		});

		Route::resource( 'customer', 'Int\CustomerController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );

		/**
		* Route schedule
		*/
		Route::resource('schedule', 'AppointmentController',[
			'only' => ['index','store', 'update']
		]);
		/**
		 * Route group for controller when uses trait ManageUserTrait
		 */
		Route::group(['prefix' => 'schedule/{id}'], function () {
			/**
			 * Route for status schedule
			 */
			Route::match(['put', 'patch'], 'status', 'AppointmentController@status')->name('schedule.status');
		});

		Route::post( 'eforms/prescreening', 'EFormController@postPrescreening' );

		Route::resource( 'prescreening', 'PrescreeningController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );

		/**
		 * Collateral routes
		 */
		Route::resource('collateral', 'CollateralController', [
			'only' => ['index', 'store', 'update']
		]);
		Route::get('collateral/{developerId}/{propertyId}', ['as' => 'collateral.detail', 'uses' => 'CollateralController@show'])
			->where(['developerId' => '[0-9]+', 'propertyId' => '[0-9]+']);
		Route::post('/collateral/disposition/{collateralId}', ['as' => 'collateral.disposition', 'uses' => 'CollateralController@disposition'])
			->where('collateralId', '[0-9]+');
		Route::post('/collateral/{action}/{collateralId}', ['as' => 'collateral.change-status', 'uses' => 'CollateralController@changeStatus'])
			->where(['collateralId' => '[0-9]+','action' => '^(approve|reject)$']);

		/**
		 * Collateral ots routes
		 */
		Route::group(['prefix' => 'collateral/ots'], function($router) {
			Route::post('/{collateralId}', ['as' => 'collateral.ots.store', 'uses' => 'CollateralController@storeOts'])
				->where('collateralId', '[0-9]+');
			Route::get('/{collateralId}', ['as' => 'collateral.ots.show', 'uses' => 'CollateralController@getOts'])
				->where('collateralId', '[0-9]+');
		});
	} );


	Route::put( 'eforms/submit-screening', 'EFormController@submitScreening' );
	Route::group( [ 'prefix' => '{type}' ], function () {
		Route::get( 'cities', 'CityController' );
		Route::get('city', 'CityController@getAll');
	} );
} );
