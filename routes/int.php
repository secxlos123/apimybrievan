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

	Route::post('getBranch', 'SelectUkerController@getBranch');
	Route::post('getMitra', 'SelectListMitraController@getMitra');
	Route::get('getFasilitas', 'SelectListController@getFasilitas');
	Route::get('getBank', 'SelectListController@getBank');
	Route::get('dir_rpc_list', 'dirrpcController@getdir_rpc');
	Route::post('mitraall', 'mitra\ScoringProsesController@getallmitra');
	Route::post('hapus_dir', 'dirrpcController@hapus_dir');
	Route::post('hapus_detail_dir', 'dirrpcController@hapus_detail_dir');
	Route::post('update_detail', 'dirrpcController@update_detail');
	Route::post('add_detail', 'dirrpcController@add_detail');
	Route::post('get_dir', 'dirrpcController@get_dir');
	Route::post('get_dir_detail', 'dirrpcController@get_dir_detail');
	Route::post('SendPushNotification', 'SendNotificationController@SendNotification');

	// if request attemp 5 it will wait 10 minutes
	// Route::group(['middleware' => 'throttle-custom:7,12'], function() {
		Route::post( 'auth/login', 'AuthController@store' );
	// });

	// route that require login session
	Route::group( [ 'middleware' => [ 'api.auth', 'file' ] ], function () {
		Route::get( 'check-token', 'TokenController@index' );
		Route::get('debitur-list', 'CustomerController@listDebitur');
		Route::post('dashboard-internal', 'DashboardController@getDataDashboardInternal');
		Route::get('debitur-detail', 'CustomerController@detailDebitur');

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

		Route::resource( 'customer', 'CustomerController', [
			'only' => [ 'destroy' ]
		] );

		Route::put( 'customers/{id}/verify', 'CustomerController@verify' );
		Route::post( 'eforms/{eform_id}/resend-vip', 'VisitReportController@resendVIP' );

		Route::group( [ 'prefix' => 'eforms/{eform_id}' ], function () {
			Route::resource( 'visit-reports', 'VisitReportController', [
				'only' => [ 'store' ]
			] );
			Route::resource( 'recontest', 'RecontestController', [
				'only' => [ 'store' ]
			] );
			Route::group( [ 'prefix' => 'verification' ], function () {
				Route::post( 'show', 'VerificationController@show' );
				Route::get( 'resend', 'VerificationController@resend' );
			} );
		} );

		Route::group( [ 'prefix' => 'verification' ], function () {
			Route::post( 'search-nik', 'VerificationController@searchNik' );
		} );

		Route::get( 'staff-list', 'StaffController@index' );

				Route::resource( 'scorings', 'ScoringController', [
			'except' => [ 'edit', 'create' ]
		] );
		 Route::resource( 'gimmick', 'GimmickController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'register_mitra', 'mitra\RegisterMitraController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'registrasi_perjanjian', 'mitra\RegisterPerjanjianController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'fasilitas_mitra', 'mitra\FasilitasMitraController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'scoring_mitra', 'mitra\ScoringProsesController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'penilaian_kelayakan', 'mitra\PenilaianKelayakanController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'approval_mitra', 'mitra\ApprovalMitraController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'input_kolektif', 'mitra\eksternal\InputKolektifController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'hasil_scoring', 'mitra\HasilScoringController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'dirrpc', 'dirrpcController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'mitra_list', 'mitra\MitraListController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		Route::resource( 'list_pekerja', 'mitra\MitraListController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		Route::resource( 'scorings', 'ScoringController', [
			'except' => [ 'edit', 'create' ]
		] );
		 Route::resource( 'gimmick', 'GimmickController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		 Route::resource( 'dirrpc', 'dirrpcController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );
		} );

	// route that require login session
	Route::group( [ 'middleware' => [ 'api.auth' ] ], function () {

		/**
		 * This Route For Auditrail
		 */
		Route::get('auditrail/pengajuan_kredit', 'AuditrailController@index');
		Route::get('auditrail/appointment', 'AuditrailController@auditAppointment');
		Route::get('auditrail/admindev', 'AuditrailController@auditAdmindev');
		Route::get('auditrail/agendev', 'AuditrailController@auditAgendev');
		Route::get('auditrail/login-logout', 'AuditrailController@auditLogin');
		Route::get('auditrail/edit', 'AuditrailController@auditEdit');
		Route::get('auditrail/collateral', 'AuditrailController@auditCollateral');
		Route::get('auditrail/property', 'AuditrailController@auditProperty');
		Route::get('auditrail/useractivity', 'AuditrailController@auditUserActivity');
		Route::get('auditrail/activity_detail/{user_id}', 'AuditrailController@auditUserActitiyDetail');
		Route::get('auditrail/customers/{nik}', 'AuditrailController@show');
		Route::get('auditrail/getnik', 'AuditrailController@getNik');
		Route::get('auditrail/list-mnpengajuan', 'AuditrailController@modulNamePengajuanKredit')->name('list-mnpengajuan');
		Route::get('auditrail/list-mnadmindev', 'AuditrailController@modulNameAdminDev')->name('list-mnadmindev');
		Route::get('auditrail/list-mnappointment','AuditrailController@modulNameAppointment')->name('list-mnappointment');
		Route::get('auditrail/list-mncollateral','AuditrailController@modulNameCollateral')->name('list-mncollateral');
		Route::get('auditrail/list-mnagendev', 'AuditrailController@modulNameAgenDev')->name('list-mnagendev');
		Route::get('auditrail/list-mnproperty', 'AuditrailController@modulNameProperty')->name('list-mnproperty');
		Route::get('auditrail/list-mndetailactivity/{id}', 'AuditrailController@modulNameDetailUserActivity')->name('list-mndetailactivity');
		Route::get('auditrail/list-collateral-dev', 'AuditrailController@collaterlDeveloper');
        Route::get('auditrail/list-collateral-non', 'AuditrailController@collateralNon');
        Route::get('auditrail/getEformCustomer/{nik}', 'AuditrailController@getEformCustomer');
        Route::get('auditrail/getBranch', 'AuditrailController@getBranch');
        Route::get('auditrail/getImage/{nik}', 'AuditrailController@getImageUpload');
	} );

	} );

Route::group(['prefix' => 'v1/int', 'namespace' => 'API\v1',
		'middleware' => ['api.auth']
	], function () {

		Route::resource( 'GetView', 'ViewController', [
			'except' => [ 'edit', 'create', 'destroy' ]
		] );

	Route::post( 'eforms/submit-screening', 'EFormController@submitScreening' );

	/**
	 * Route group for namespace controller Int
	 */
	Route::group(['namespace' => 'Int'], function () {

		/**
		 * Route For Prescreening
		 */
		Route::resource( 'prescreening', 'PrescreeningController', [
			'only' => [ 'index', 'store', 'update' ]
		] );
		Route::post( 'eforms/prescreening', 'PrescreeningController@show' );
		Route::get( 'eforms/auto_prescreening', 'PrescreeningController@getIsAutoPrescreening' );

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
		Route::post( 'delete', 'EFormController@delete' )->name( 'eforms.delete' );
		Route::post( 'disposition', 'EFormController@disposition' )->name( 'eforms.disposition' );
		Route::post( 'approve', 'EFormController@approve' );
		Route::post( 'step-{step_id}', 'EFormController@insertCoreBRI' ); // step id must between 1 - 7
	} );

	Route::get( 'eforms/{ids}/{ref_number}', 'EFormController@showIdsAndRefNumber' );

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

    Route::get('users/notification/read/{slug}/{type}', [
        'as'    => 'api.user.read_notification',
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
		 /**
		 * Route for namespace API\v1\Int
		 */
		 Route::get('collateral/{type}', [
	        'uses'  => 'CollateralController@GetAll'
	    ]);
    });

});
Route::group( [ 'prefix' => 'v1/int', 'namespace' => 'API\v1' ], function () {

	//this route for generatepdf
	Route::get('genaratePDF', 'EFormController@eformGenerate');

});