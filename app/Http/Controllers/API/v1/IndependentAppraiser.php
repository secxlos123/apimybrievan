<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class IndependentAppraiser extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        if ( ENV('APP_ENV') == 'local' ) {
            return response()->success( [
                'message' => 'Sukses',
                'contents' => {
                    "data" => [
                        {
                            "id" => 1,
                            "name" => " PT.Kawira Pratama Penilai"
                        },
                        {
                            "id" => 2,
                            "name" => " UJP.IR. Dudung Hamidi"
                        },
                        {
                            "id" => 3,
                            "name" => " PT.Putra Indonesia Puruhita"
                        },
                        {
                            "id" => 4,
                            "name" => " PT.Ujatek Baru"
                        },
                        {
                            "id" => 7,
                            "name" => " PT.Indoprofita Konsultama"
                        },
                        {
                            "id" => 8,
                            "name" => " PT.Pronilai Konsulis Indonesia"
                        },
                        {
                            "id" => 9,
                            "name" => " PT.Mediaindo Citra Kirana"
                        },
                        {
                            "id" => 10,
                            "name" => " PT.Indusma Kreasi Consult"
                        },
                        {
                            "id" => 11,
                            "name" => " PT.Daksana Intra Swadaya"
                        },
                        {
                            "id" => 12,
                            "name" => " PT.Penilai Arta Sedaya"
                        },
                        {
                            "id" => 13,
                            "name" => " PT.Saptasentra Jasa Pradana "
                        },
                        {
                            "id" => 14,
                            "name" => " PT.Arga Nilai Mandiri"
                        },
                        {
                            "id" => 15,
                            "name" => " PT.Soeparjono Arta Penilai"
                        },
                        {
                            "id" => 16,
                            "name" => " PT.Provalindo Nusa"
                        },
                        {
                            "id" => 17,
                            "name" => " PT.Raxindo Wardana"
                        },
                        {
                            "id" => 18,
                            "name" => " PT.Tetrindo Agrifor Penilai"
                        },
                        {
                            "id" => 19,
                            "name" => " Truscel Capital"
                        },
                        {
                            "id" => 21,
                            "name" => " PT.Amandamai Arthamitra  Jasa"
                        },
                        {
                            "id" => 22,
                            "name" => " PT.Reka Arta"
                        },
                        {
                            "id" => 23,
                            "name" => " PT.Mavira Aprisindo Utama"
                        },
                        {
                            "id" => 24,
                            "name" => " PT.Sierlando International Appraisal"
                        },
                        {
                            "id" => 25,
                            "name" => " PT.Inti Utama Cahaya Perkasa "
                        },
                        {
                            "id" => 26,
                            "name" => " PT.Shantika Valuindo Lestari "
                        },
                        {
                            "id" => 27,
                            "name" => " PT.Seruling Bambu Kuning"
                        },
                        {
                            "id" => 28,
                            "name" => " PT.Equalindo Estima"
                        },
                        {
                            "id" => 29,
                            "name" => " PT.Dwi Valuina"
                        },
                        {
                            "id" => 30,
                            "name" => " PT.Binamitra Consulindotama"
                        },
                        {
                            "id" => 31,
                            "name" => " PT.Tunas Apresindo Utama"
                        },
                        {
                            "id" => 32,
                            "name" => " PT.Aroma Citra Gading"
                        },
                        {
                            "id" => 33,
                            "name" => " PT.Gandamega Serasi"
                        },
                        {
                            "id" => 34,
                            "name" => " PT.Estimasindo Sejahtera"
                        },
                        {
                            "id" => 35,
                            "name" => " PT.Independensia Consultindo"
                        },
                        {
                            "id" => 37,
                            "name" => " PT.Dian Andilta Utama"
                        },
                        {
                            "id" => 38,
                            "name" => " PT.Heburinas Nusantara "
                        },
                        {
                            "id" => 40,
                            "name" => " PT.Bahana Kareza Appraisal"
                        },
                        {
                            "id" => 41,
                            "name" => " PT.VPC Hagai Sejahtera"
                        },
                        {
                            "id" => 42,
                            "name" => " PT.Graha Karya Reksatama"
                        },
                        {
                            "id" => 44,
                            "name" => " PT.Mega Appraisindo"
                        },
                        {
                            "id" => 45,
                            "name" => " PT.Asian Appraisal Indonesia"
                        },
                        {
                            "id" => 47,
                            "name" => " PT.Anima Krida Usaha"
                        },
                        {
                            "id" => 48,
                            "name" => " PT.Eka Karya Asa Mandiri"
                        },
                        {
                            "id" => 49,
                            "name" => " PT.Damasindo Nilai Utama"
                        },
                        {
                            "id" => 50,
                            "name" => " PT.Valuindo Perdana "
                        },
                        {
                            "id" => 51,
                            "name" => " PT.Satyatama Graha Tara"
                        },
                        {
                            "id" => 52,
                            "name" => " PT.Bahana Appresindo"
                        },
                        {
                            "id" => 53,
                            "name" => " PT.Actual Kencana Appraisal "
                        },
                        {
                            "id" => 55,
                            "name" => " PT.Wadantra Nilaitama"
                        },
                        {
                            "id" => 56,
                            "name" => " PT.Kartika Agung Caraka Appraisal"
                        },
                        {
                            "id" => 57,
                            "name" => " PT.Citra Bahana Penilai"
                        },
                        {
                            "id" => 58,
                            "name" => " PT.Laksa Laksana"
                        },
                        {
                            "id" => 59,
                            "name" => " PT.Investindo Konsultama Appraisal"
                        },
                        {
                            "id" => 60,
                            "name" => " PT.Perintis Inovasindo Utama"
                        },
                        {
                            "id" => 61,
                            "name" => " PT.Piesta Penilai"
                        },
                        {
                            "id" => 62,
                            "name" => " PT.Kreasi Laksana Konsultan"
                        },
                        {
                            "id" => 63,
                            "name" => " UJP.Drs.Hari Purwanto"
                        },
                        {
                            "id" => 64,
                            "name" => " PT.Berkat Mitra Handal"
                        },
                        {
                            "id" => 65,
                            "name" => " PT.Kusuma Real Sakti"
                        },
                        {
                            "id" => 66,
                            "name" => " PT.Hutama Penilai"
                        },
                        {
                            "id" => 67,
                            "name" => " PT.Duta Wirya Pratama Centralestima"
                        },
                        {
                            "id" => 68,
                            "name" => " PT.Pronitama Karya Nilaindo"
                        },
                        {
                            "id" => 69,
                            "name" => "PT. Doddi Purgana"
                        },
                        {
                            "id" => 70,
                            "name" => "Kjpp Anas Karim Rivai dan Rekan"
                        },
                        {
                            "id" => 71,
                            "name" => "Kjpp Muttaqin Bambang Purwanto Rozak Uswatun dan Rekan"
                        }
                    ],
                    "from" => "1",
                    "last_page" => "1",
                    "per_page" => "63",
                    "to" => "63",
                    "total" => "63"
                }
            ], 200 );

        } else {
            $appraiser_service = Asmx::setEndpoint( 'GetPenilaiIndependen' )->setQuery( [
                'search' => $request->search,
                'limit' => $request->limit,
                'page' => $request->page,
                'sort' => $request->sort,
            ] )->setBody(['request'=>''])->post('form_params');
            \Log::info($appraiser_service);
            $appraiser = $appraiser_service[ 'contents' ];
            $appraiser[ 'data' ] = array_map( function( $content ) {
                return [
                    'id' => $content[ 'id_penilai_independen' ],
                    'name' => $content[ 'desc' ],
                ];
            }, $appraiser[ 'data' ] );
            return response()->success( [
                'message' => 'Sukses',
                'contents' => $appraiser
            ], 200 );

        }
    }
}
