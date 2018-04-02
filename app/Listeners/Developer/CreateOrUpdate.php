<?php

namespace App\Listeners\Developer;

use App\Events\Developer\CreateOrUpdate as Event;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrUpdate
{
    /**
     * Handle the event.
     *
     * @param  CreateOrUpdate  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $developer = $event->developer->load( "user" );

        $current = [
            , "tipe_pihak_ketiga" => "DEVELOPER"
            , "nama_pihak_ketiga" => $developer->company_name
            , "alamat_pihak_ketiga" => $developer->address
            , "pic_pihak_ketiga" => $developer->user->fullname
            , "pks_pihak_ketiga" => $developer->pks_number
            , "deskripsi_pihak_ketiga" => $developer->summary
            , "telepon_pihak_ketiga" => $developer->user->phone
            , "hp_pihak_ketiga" => $developer->user->mobile_phone
            , "fax_pihak_ketiga" => ""
            , "deskripsi_pks_pihak_ketiga" => $developer->pks_description
            , "plafon_induk_pihak_ketiga" => $developer->plafond
            , "grup_sub_pihak_ketiga" => "null"
            , "pihak_ketiga_value" => $developer->dev_id_bri ?: ""
        ];

        $insert = \Asmx::setEndpoint( "InsertDataPihakKetiga" )
            ->setBody( [ "request" => json_encode( $current ) ] )
            ->post( "form_params" );

        if ( $insert["code"] == "200" ) $developer->update( [ "dev_id_bri" => $insert["contents"] ] );
    }
}
