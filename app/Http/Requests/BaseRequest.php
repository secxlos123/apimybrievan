<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class BaseRequest extends FormRequest
{
    /**
     * Format the errors from the given Validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return array
     */
    protected function formatErrors( Validator $validator )
    {
        return [
            'message' => 'Please insert valid data.',
            'data' => array_map( function( $value ) {
                return $value[ 0 ];
            } , $validator->messages()->toArray() )
        ];
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response( array $errors )
    {
        return response()->error( $errors, 200 );
    }
}
