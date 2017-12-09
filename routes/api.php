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
		Route::post('select', 'SelectController@select');
		Route::post('mitra_relation', 'EFormController@mitra_relation');
		Route::post('eforms_briguna', 'EFormController@show_briguna');
		Route::get('Download_Rekomendasi', 'Download_RekomendasiController@Download');
		Route::get('Surat_Kuasa_Potong_Upah', 'DownloadFileController@Download');
		Route::get	('Surat_Rekomendasi_Atasan', 'DownloadFileController@Download2');
		Route::post('SelectMitra', 'SelectMitraController@SelectMitra');
		Route::post('SelectKodePos', 'SelectKodePosController@SelectKodePos');
		/* ------------*/


	Route::group( [ 'prefix' => '{type}', 'middleware' => 'api.auth' ], function () {
		 /**
         * User Notification
         */
		Route::get('users/notification', [
            'as'    => 'api.user.notification',
            'uses'  => 'NotificationController@index'
        ]);

        Route::get('users/notification/unread', [
            'as'    => 'api.user.unread_notification',
            'uses'  => 'NotificationController@unread'
        ]);

        Route::get('users/notification/summary', [
            'as'    => 'api.user.summary_notification',
            'uses'  => 'NotificationController@summary'
        ]);

        Route::get('users/notification/read/{id}', [
            'as'    => 'api.user.read_notification',
            'uses'  => 'NotificationController@read'
        ]);

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
		Route::post('SelectCabang', 'SelectCabangController@index');

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
		Route::post( 'eforms/submit-screening', 'EFormController@submitScreening' );

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
		Route::get('collateral/nonindex', ['as' => 'collateral.indexNon', 'uses' => 'CollateralController@indexNon']);
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

	Route::group( [ 'prefix' => '{type}' ], function () {
		Route::get( 'cities', 'CityController' );
		Route::get('city', 'CityController@getAll');
	} );
} );
