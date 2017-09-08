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
        return config('restapi.asmx').$this->endpoint;
    }

    /**
     * Post request to middleware.
     *
     * @return \Illuminate\Http\Response
     */
    public function post($type = 'json')
    {
        try {
            $request  = $this->http->request('POST', $this->uri(), [
                'headers'  => $this->headers,
                'query'    => $this->query,
                $type      => $this->body
            ]);
            $xml = simplexml_load_string( $request->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA );
            $string_xml = str_replace( ' ', '', $xml );
            $string_xml = str_replace( '"{', '{', $string_xml );
            $string_xml = str_replace( '}"', '}', $string_xml );
            $string_xml = json_decode( $string_xml, true );
            $response = $string_xml;
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody();
            $response = json_decode($body->getContents(), true);
        } catch (ServerException $e) {
            \Log::info($e->getRequest()->getBody());
            \Log::info($e->getMessage());
            abort(500);
        }

        return $response;
    }
}