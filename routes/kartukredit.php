<?php

Route::get('/contoh',function(){
		return redirect('https://mybri.bri.co.id/');
});


Route::group(['prefix' => 'v1/int/kk','namespace'=> 'API\v1\Int'], function() {

	Route::get('contohemail','KartuKreditController@contohemail');
    

   	Route::get('get-dropdown-info','KartuKreditController@getAllInformation');

	Route::post('get-nik','KartuKreditController@checkNIK');

	

	Route::get('/nik-los/{nik}', 'KartuKreditController@checkDedup');

	Route::get('cek-data-nasabah/{apRegno}','KartuKreditController@cekDataNasabah');


	Route::post('/eform', 'KartuKreditController@eform');

	

	Route::post('/toemail','KartuKreditController@toEmail');
	Route::post('/tosms', 'KartuKreditController@sendSMS');
	Route::get('/verifyemail', 'KartuKreditController@checkEmailVerification');

	Route::get('/listreject','KartuKreditController@listReject');



    Route::group(['middleware' => 'api.auth'], function() {

    	Route::post('/putusan-pinca','KartuKreditController@putusanPinca');
    	Route::post('add-data-los','KartuKreditController@sendUserDataToLos');
		Route::post('/update-data-los', 'KartuKreditController@updateDataLos');
		Route::post('/analisa', 'KartuKreditController@analisaKK');
		Route::post('/finish-analisa','KartuKreditController@finishAnalisa');
    });
});


