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

		Route::post('eks/SelectCabang', 'EFormController@eksternalmitra');
		Route::post('hapusbriguna', 'EFormController@hapuseform');
		Route::post('get_token', 'EFormController@get_token');
		Route::get('int/monitoring', 'EFormController@monitoring');
		Route::post('getBranch', 'SelectUkerController@getBranch');
		Route::post('smsnotif', 'SentSMSNotifController@sentsms');
		Route::post('select', 'SelectController@select');
		Route::get('phpini', 'EFormController@php_ini');
		Route::post('mitra_relation', 'EFormController@mitra_relation');
		//Route::post('GimmickUnduh', 'GimmickController@gimmick_pdf');
		Route::post('eforms_briguna', 'EFormController@show_briguna');
		Route::get('Download_Rekomendasi', 'Download_RekomendasiController@Download');
		Route::get('Surat_Kuasa_Potong_Upah', 'DownloadFileController@Download');
		Route::get	('Surat_Rekomendasi_Atasan', 'DownloadFileController@Download2');
		Route::post('SelectMitra', 'SelectMitraController@SelectMitra');
		Route::post('SelectKodePos', 'SelectKodePosController@SelectKodePos');
		Route::post('SelectCabang', 'SelectCabangController@getCabangMitra');
		Route::get('SelectCabangInternal', 'SelectCabangController@getCabangMitraOpi');
		Route::get('scheduler_mitra', 'SchedulerMitraController@scheduler');
		Route::get('scheduler_rekening', 'SchedulerRekeningController@rekening_brinets');
		Route::post('gimmick_list', 'Int\GimmickController@list_gimmick');
		Route::post('GetView', 'ViewController@index');
		Route::post('testertoken', 'EFormController@TestingBranch');
		Route::post('uploadtambahan', 'UploadtambahController@upload');

		/* ------------*/

	Route::group( [ 'prefix' => '{type}', 'middleware' => 'api.auth' ], function () {
		Route::get('mobile/count-notification', 'NotificationController@countNotification');
		Route::get('mobile/list-notification', 'NotificationController@unreadMobile');
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
		Route::get( 'insurance-list', 'CompanyInsurance@index' );
		Route::get( 'appraiser-list', 'IndependentAppraiser@index' );
		Route::get( 'zipcode-list', 'ZipCodeController@index' );

		Route::resource( 'eforms', 'EFormController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );

		Route::resource( 'dirrpc', 'Dir_rpcController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		Route::get( 'offices', 'OfficeController@index' );

		Route::group(['prefix' => 'dropdown'], function () {
			Route::get('properties', 'DropdownController@properties');
			Route::get('types', 'DropdownController@types');
			Route::get('units', 'DropdownController@items');
		});

		// Dropbox
		Route::post('dropbox/index', 'DropboxController@index');

		// API LAS
		Route::post('api_las/index', 'ApiLasController@index');
		Route::post('api_las/briguna', 'ApiLasController@show_briguna');
		Route::post('api_las/update', 'ApiLasController@update_briguna');
		Route::post('api_las/update_foto', 'ApiLasController@update_foto_briguna');
		Route::post('api_las/download_image', 'ApiLasController@download');
		Route::post('api_las/foto_lainnya', 'ApiLasController@update_foto_lainnya');
		// Dropdown LAS
        Route::post('dropdown_las', 'DropdownLasController@list_dropdown');

		Route::resource( 'customer', 'Int\CustomerController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );

		/**
		* Route schedule
		*/
		Route::resource('schedule', 'AppointmentController',[
			'only' => ['index','store', 'update']
		]);
		Route::get('schedule/{id}', ['as' => 'schedule.detail', 'uses' => 'AppointmentController@detail']);

		/**
		 * Route group for controller when uses trait ManageUserTrait
		 */
		Route::group(['prefix' => 'schedule/{id}'], function () {
			/**
			 * Route for status schedule
			 */
			Route::match(['put', 'patch'], 'status', 'AppointmentController@status')->name('schedule.status');
		});

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
		Route::post('/collateral/{action}/{collateralId}', ['as' => 'collateral.change-status', 'uses' => 'CollateralController@changeStatus'])->where(['collateralId' => '[0-9]+','action' => '^(approve|reject)$']);
		Route::get('collateral/nonindex', ['as' => 'collateral.indexNon', 'uses' => 'CollateralController@indexNon']);
		Route::get('collateral/nonindex/{developerId}/{propertyId}', ['as' => 'collateral.showNon', 'uses' => 'CollateralController@showNon']);
		Route::get('collateral/notifotsnonindex/{developerId}/{propertyId}', ['as' => 'collateral.notifotsnonindex', 'uses' => 'CollateralController@NotifOtsNonindex']);
		Route::get('collateral/notifots/{developerId}/{propertyId}', ['as' => 'collateral.notifots', 'uses' => 'CollateralController@NotifOts']);
        Route::get('collateral/collateralnotif/{collateralId}', ['as' => 'collateral.shownotif', 'uses' => 'CollateralController@notifCollateral']);
        Route::get('collateral/getIdCollateral/{property_id}', ['as' => 'collateral.getIdCollateral', 'uses' => 'CollateralController@getIdCollateral']);

		/**
		 * Collateral ots routes
		 */
		Route::group(['prefix' => 'collateral/ots'], function($router) {
			Route::post('/{collateralId}', ['as' => 'collateral.ots.store', 'uses' => 'CollateralController@storeOts'])
				->where('collateralId', '[0-9]+');
			Route::get('/{collateralId}', ['as' => 'collateral.ots.show', 'uses' => 'CollateralController@getOts'])
				->where('collateralId', '[0-9]+');
		});

		/**
		 * Collateral ots Doc routes
		 */
		Route::group(['prefix' => 'collateral/otsdoc'], function($router) {
			Route::post('/{collateralId}', ['as' => 'collateral.otsdoc.store', 'uses' => 'CollateralController@storeOtsDoc'])
				->where('collateralId', '[0-9]+');
			Route::get('/{collateralId}', ['as' => 'collateral.otsdoc.show', 'uses' => 'CollateralController@showOtsDoc'])
				->where('collateralId', '[0-9]+');
		});

		/**
		 * Route approval data change
		 * @var [type]
		 */
		Route::group(['prefix' => 'approval-data-change/{approvalType}', 'as' => 'approval-data-change.'], function($router) {
			Route::get( '/show-id/{id}', 'ApprovalDataChangeController@showByIds' );
			Route::resource('', 'ApprovalDataChangeController', [
				'only' => ['index', 'store', 'show', 'update'],
				'parameters' => [
				   '' => 'approvalDataChangeId'
				]
			]);
			Route::post('{status}/{id}', ['as' => 'change-status', 'uses' => 'ApprovalDataChangeController@changeStatus'])
			->where(['status' => '^(approve|reject)$', 'id' => '[0-9]+']);
		});

		Route::resource('tracking', 'TrackingController',[
			'only' => ['index', 'show']]);
	} );

	Route::group( [ 'prefix' => '{type}' ], function () {
		Route::get( 'cities', 'CityController' );
		Route::get('city', 'CityController@getAll');
	} );

	/**
	 * Force Update from CLAS
	 */
	Route::post( 'eforms/update-clas', 'EFormController@updateCLAS' );
} );
