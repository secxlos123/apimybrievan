<?php
Route::group(['prefix'=>'v1/int/crm', 'middleware' => 'api.auth', 'namespace' => 'API\v1\Int\Crm'], function(){
  // route dashboard
  Route::get( '/', 'DashboardController@index', [

  ] )->name('crm.index');

  // route reporting Crm
  // Route::post( 'report_marketings', 'reportController@report_marketings')->name('crm.report_marketings');
  // Route::post( 'report_activities', 'reportController@report_activities')->name('crm.report_activities');

  //route $pemasar
  Route::post('pemasar', 'DashboardController@pemasar')->name('crm.pemasar');

  //route account
  Route::post( 'account/leads', 'AccountController@index')->name('crm.account');
  Route::post( 'account/leads_detail', 'AccountController@detail')->name('crm.account_detail');
  Route::post( 'account/existing_fo', 'AccountController@existingFo')->name('crm.existing_fo');
  Route::post( 'account/test', 'AccountController@getBranchKanwil');
  Route::get( 'account/get_referral', 'AccountController@get_referral');
  Route::get( 'account/get_referral_by_officer', 'AccountController@get_referral_by_officer');
  Route::post( 'account/get_referral_by_branch', 'AccountController@get_referral_by_branch');
  Route::post( 'account/store_referral', 'AccountController@store_referral');

  // route customer group
  Route::get( 'account/customer_group', 'customerGroupController@index');
  // route  store customer group
  Route::post( 'account/customer_group', 'customerGroupController@store');

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

  // Route Activity by officer
  Route::post('/activity/by_officer', 'marketingActivityController@activity_by_officer');

  // Route Activity by branch
  Route::post('/activity/by_branch', 'marketingActivityController@activity_branch');

  // Route Activity by marketing
  Route::post('/activity/by_marketing', 'marketingActivityController@activity_by_marketing');

  // Route Activity by customer
  Route::post('/activity/by_customer', 'marketingActivityController@activity_by_customer');

  // Route reschedule Activity
  Route::post('/activity/reschedule', 'marketingActivityController@reSchedule')->name('crm.reschedule');

  // Route storeFollowUp Activity
  Route::post('/activity/storeFollowUp', 'marketingActivityController@storeFollowUp')->name('crm.storeFollowUp');

  //Route Marketing
  Route::resource('marketing', 'MarketingController', [
    'only' => ['index', 'store']
  ]);

  // Marketing Notes Route
  Route::get('/marketing/note', 'MarketingController@getNote');
  Route::post('/marketing/srore_note', 'MarketingController@store_note');

  // Route marketing by branch
  Route::post('/marketing/by_branch', 'MarketingController@by_branch');

  Route::get('/activity/deleteAll', 'marketingActivityController@deleteAll');

  //marketing Map
  Route::get('/market_mapping', 'marketingMapController@index');
  //marketing store Map
  Route::post('/marketing_map', 'marketingMapController@store');
});
