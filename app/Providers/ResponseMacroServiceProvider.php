<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function (array $data, $code = 200) {
            $response = [
                'status' => [
                    'message' => isset($data['message']) ? $data['message'] : 'OK',
                    'succeded' => true,
                    'code' => $code,
                ],
            ] + array_except($data, 'message');

            return Response::json($response, $code);
        });

        Response::macro('error', function (array $data, $code = 400) {
            $response = [
                'status' => [
                    'message' => isset($data['message']) ? $data['message'] : 'Failed',
                    'succeded' => false,
                    'code' => $code,
                ],
            ] + array_except($data, 'message');

            return Response::json($response, $code);
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
