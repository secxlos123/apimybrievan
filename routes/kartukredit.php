<?php


Route::group(['prefix' => 'v1/int/kk','namespace'=> 'API\v1'], function() {
    Route::get('get-data','KartuKreditController@example');
     // Route::post('eform','KartuKreditController@cekEform');


   	Route::get('get-dropdown-info','KartuKreditController@getAllInformation');

	Route::post('get-nik','KartuKreditController@checkNIK');

	Route::post('los','KartuKreditController@sendUserDataToLos');

    Route::group(['middleware' => 'api.auth'], function() {
    	 //cari nik di mybri(nasabah), kalau ada balikin, kalau engga cari lagi di crm baru balikin
        // Route::post('get-nik','KartuKreditController@checkNIK');

    });
});


