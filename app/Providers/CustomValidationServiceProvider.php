<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Helpers\CustomValidation;

class CustomValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::resolver( function( $translator, $data, $rules, $messages ) {
            return new CustomValidation( $translator, $data, $rules, $messages );
        });
    }
}
