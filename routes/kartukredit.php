<?php

Route::group(['prefix' => 'v1/int/kk','namespace'=> 'API\v1\Int'], function() {
    Route::get('get-data','KartuKreditController@example');

    



   	Route::get('get-dropdown-info','KartuKreditController@getAllInformation');

	Route::post('get-nik','KartuKreditController@checkNIK');

	Route::post('add-data-los','KartuKreditController@sendUserDataToLos');

	Route::get('/nik-los/{nik}', 'KartuKreditController@checkDedup');

	Route::get('cek-data-nasabah/{apRegno}','KartuKreditController@cekDataNasabah');

	Route::post('/update-data-los', 'KartuKreditController@updateDataLos');

	Route::get('/pefindo', 'KartuKreditController@pefindo');

	Route::post('/eform', 'KartuKreditController@eform');

	Route::post('/analisa', 'KartuKreditController@analisaKK');

	Route::post('/toemail','KartuKreditController@toEmail');

	Route::post('/finish-analisa','KartuKreditController@finishAnalisa');

	// Route::post('s')


    Route::group(['middleware' => 'api.auth'], function() {

    	Route::post('ajukankredit', 'KartuKreditController@ajukanKredit');
    	
    	Route::post('/putusan-pinca','KartuKreditController@putusanPinca');
    });
});


