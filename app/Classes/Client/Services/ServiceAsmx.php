<?php

namespace App\Classes\Client\Services;

use App\Classes\Client\Client;

class ServiceAsmx extends Client
{
	/**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public function uri()
    {
        return config( "restapi.asmx" ) . $this->endpoint;
    }

    /**
     * Post request to middleware.
     *
     * @return \Illuminate\Http\Response
     */
    public function post( $type = "json" )
    {
        try {
            $request  = $this->http->request(
                "POST"
                , $this->uri()
                , [
                    "headers" => $this->headers
                    , "query" => $this->query
                    , $type => $this->body
                ]
            );

            $xml = simplexml_load_string(
                $request->getBody()
                , "SimpleXMLElement"
                , LIBXML_NOCDATA
            );

            if ( $this->endpoint != "GetPdfReport" ) {
                //parsing jika json tidak valid
                if ( !$this->isJSON( $xml ) ) {
                    $xml = str_replace( "=\"" , "" , str_replace( "\">" , "" , $xml ) );
                }
            }

            $response = json_decode( $xml, true );

        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody();
            $response = json_decode( $body->getContents(), true );

        } catch (ServerException $e) {
            \Log::info( "=== Response ASMX Service ===" );
            \Log::info( $e->getRequest()->getBody() );
            \Log::info( $e->getMessage() );
            \Log::info( "=== End ASMX Service ===" );
            abort(500);

        }

        return $response;
    }

    private function isJSON($string)
    {
        return is_string( $string ) && is_array( json_decode( $string, true ) ) && ( json_last_error() == JSON_ERROR_NONE ) ? true : false;
    }
}