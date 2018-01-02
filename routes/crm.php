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
  Route::post( 'account/test', 'AccountController@getBranchKanwil');

  // route customer
  Route::get( 'account/customer', 'CustomerController@index')->name('crm.customer');

  //route portfolio
  Route::post('account/detail_by_cif', 'CustomerController@detailByCif');

  // route customer by nik
  Route::post( 'account/customer_nik', 'CustomerController@customer_nik')->name('crm.customer_nik');

  // route customer officer
  Route::post( 'account/customer_officer', 'CustomerController@customer_officer')->name('crm.customer_officer');

  //route activity
  Route::resource( 'activity', 'marketingActivityController', [
    'only' => ['index', 'store']
  ] );

  // Route create Activity by pinca
  Route::post('/activity_by_pinca', 'marketingActivityController@store_by_pinca');

  // Route Activity by branch
  Route::post('/activity/by_branch', 'marketingActivityController@activity_branch');

  // Route Activity by marketing
  Route::post('/activity/by_marketing', 'marketingActivityController@activity_by_marketing');

  // Route reschedule Activity
  Route::post('/activity/reschedule', 'marketingActivityController@reSchedule')->name('crm.reschedule');

  // Route storeFollowUp Activity
  Route::post('/activity/storeFollowUp', 'marketingActivityController@storeFollowUp')->name('crm.storeFollowUp');

  //Route Marketing
  Route::resource('marketing', 'MarketingController', [
    'only' => ['index', 'store']
  ]);
});
