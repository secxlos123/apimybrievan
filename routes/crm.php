<?php
Route::group(['prefix'=>'v1/int/crm', 'middleware' => 'api.auth', 'namespace' => 'API\v1\Int\Crm'], function(){
  // route dashboard
  Route::get( '/', 'DashboardController@index', [

  ] )->name('crm.index');

  //route $pemasar
  Route::post('pemasar', 'DashboardController@pemasar')->name('crm.pemasar');

  //route account
  Route::post( 'account/leads', 'AccountController@index')->name('crm.account');
  Route::post( 'account/leads_detail', 'AccountController@detail')->name('crm.account_detail');
  Route::post( 'account/existing_fo', 'AccountController@existingFo')->name('crm.existing_fo');

  // route customer
  Route::get( 'account/customer', 'CustomerController@index')->name('crm.customer');

  // route customer by nik
  Route::post( 'account/customer_nik', 'CustomerController@customer_nik')->name('crm.customer_nik');

  // route customer officer
  Route::post( 'account/customer_officer', 'CustomerController@customer_officer')->name('crm.customer_officer');

  //route activity
  Route::resource( 'activity', 'marketingActivityController', [
    'only' => ['index', 'store']
  ] );

  // Route reschedule Activity
  Route::post('/activity/reschedule', 'marketingActivityController@reSchedule')->name('crm.reschedule');

  // Route storeFollowUp Activity
  Route::post('/activity/storeFollowUp', 'marketingActivityController@storeFollowUp')->name('crm.storeFollowUp');

  //Route Marketing
  Route::resource('marketing', 'MarketingController', [
    'only' => ['index', 'store']
  ]);
});
