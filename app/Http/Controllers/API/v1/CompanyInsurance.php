<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class CompanyInsurance extends Controller
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
                    "data": [
                        {
                            "id": 20,
                            "name": "PT. ASURANSI BINA DANA ARTA D\/H DHARMALA",
                            "code": "ABD"
                        },
                        {
                            "id": 21,
                            "name": "PT. ASURANSI ANDIKA RAHARJA PUTRA",
                            "code": "ANDIKA R P"
                        },
                        {
                            "id": 22,
                            "name": "PT. ASURANSI ARTARINDO",
                            "code": "ARTARINDO"
                        },
                        {
                            "id": 23,
                            "name": "PT. ASURANSI ASIA PRATAMA",
                            "code": "ASIA P"
                        },
                        {
                            "id": 38,
                            "name": "PT. ASKRINDO",
                            "code": "ASKRINDO"
                        },
                        {
                            "id": 41,
                            "name": "PT. ASURANSI PURNA ARTHANUGRAHA",
                            "code": "ASPAN"
                        },
                        {
                            "id": 24,
                            "name": "PT. ASURANSI BDNI",
                            "code": "BDNI"
                        },
                        {
                            "id": 25,
                            "name": "PT. ASURANSI BERDIKARI",
                            "code": "BERDIKARI"
                        },
                        {
                            "id": 26,
                            "name": "PT. ASURANSI BINAGRIYA UPAKARA",
                            "code": "BINAGRIYA"
                        },
                        {
                            "id": 27,
                            "name": "PT. ASURANSI BINTANG",
                            "code": "BINTANG"
                        },
                        {
                            "id": 28,
                            "name": "PT. BRINGIN JIWA SEJAHTERA",
                            "code": "BJS"
                        },
                        {
                            "id": 29,
                            "name": "PT. ASURANSI BSAM",
                            "code": "BSAM"
                        },
                        {
                            "id": 30,
                            "name": "PT. B S M INSURANCE BROKER",
                            "code": "BSM INS BR"
                        },
                        {
                            "id": 31,
                            "name": "PT. B S M REINSURANCE BROKER",
                            "code": "BSM REINS"
                        },
                        {
                            "id": 32,
                            "name": "PT. ASURANSI CENTRAL ASIA",
                            "code": "CENTRAL AS"
                        },
                        {
                            "id": 33,
                            "name": "PT. CHINA INS. INDONESIA",
                            "code": "CHINA INS"
                        },
                        {
                            "id": 44,
                            "name": "PT ASURANSI DAYIN MITRA",
                            "code": "DAYIN"
                        },
                        {
                            "id": 1,
                            "name": "PT. ASURANSI DHARMA BANGSA",
                            "code": "DHARMA B."
                        },
                        {
                            "id": 2,
                            "name": "PT. ASURANSI FADENT MAHKOTA SAHI",
                            "code": "FADENT M S"
                        },
                        {
                            "id": 3,
                            "name": "PT.ASURANSI GRASIA UNI SARANA",
                            "code": "GRASIA U S"
                        },
                        {
                            "id": 4,
                            "name": "IURAN JASA KESEJAHTERAAN INKPPABRI",
                            "code": "IJK"
                        },
                        {
                            "id": 34,
                            "name": "PT. ASURANSI IKRAR LLOYD",
                            "code": "IKRAR L"
                        },
                        {
                            "id": 39,
                            "name": "PT. JAMKRINDO",
                            "code": "JAMKRINDO"
                        },
                        {
                            "id": 35,
                            "name": "PT. ASURANSI JASA RAHARJA PUTRA",
                            "code": "JASA R P"
                        },
                        {
                            "id": 36,
                            "name": "PT. ASURANSI JASA TANIA",
                            "code": "JASA TANIA"
                        },
                        {
                            "id": 40,
                            "name": "PT. ASURANSI JASA INDONESIA (PERSERO)",
                            "code": "JASINDO"
                        },
                        {
                            "id": 43,
                            "name": "PT. Jaya Proteksi",
                            "code": "jayaprotek"
                        },
                        {
                            "id": 37,
                            "name": "PT. MASKAPAI ASURANSI INDONESIA",
                            "code": "MASKAPAI A"
                        },
                        {
                            "id": 5,
                            "name": "PT. ASURANSI MULTI ARTA GUNA",
                            "code": "MULTI A G"
                        },
                        {
                            "id": 6,
                            "name": "PT. MASKAPAI ASURANSI PAROLAMAS",
                            "code": "PAROLAMAS"
                        },
                        {
                            "id": 42,
                            "name": "PT. ASURANSI PAROLAMAS",
                            "code": "PAROLAMAS"
                        },
                        {
                            "id": 7,
                            "name": "PT. ASURANSI PRIMA PERKASA",
                            "code": "PRIMA P"
                        },
                        {
                            "id": 8,
                            "name": "PT HEKSA EKA LIFE INSURANCE",
                            "code": "PT HELI"
                        },
                        {
                            "id": 9,
                            "name": "PT. ASURANSI PURI ASIH",
                            "code": "PURI ASIH"
                        },
                        {
                            "id": 10,
                            "name": "PT. ASURANSI RAKSA PRATIKARA",
                            "code": "RAKSA P."
                        },
                        {
                            "id": 11,
                            "name": "PT. ASURANSI RAMA SATRIA WIBAWA",
                            "code": "RAMA S W"
                        },
                        {
                            "id": 12,
                            "name": "PT. ASURANSI RAMAYANA",
                            "code": "RAMAYANA"
                        },
                        {
                            "id": 13,
                            "name": "PT. ASURANSI SAMSUNG TUGU",
                            "code": "SAMSUNG T"
                        },
                        {
                            "id": 14,
                            "name": "PT. ASURANSI SARI SUMBER AGUNG \/ JAYA PR",
                            "code": "SARI S A"
                        },
                        {
                            "id": 15,
                            "name": "PT. ASURANSI SINAR MAS",
                            "code": "SINAR MAS"
                        },
                        {
                            "id": 16,
                            "name": "PT. ASURANSI STACO JASAPRATAMA",
                            "code": "STACO J"
                        },
                        {
                            "id": 45,
                            "name": "PT. Asuransi Tri Pakarta",
                            "code": "TRIPAKARTA"
                        },
                        {
                            "id": 17,
                            "name": "PT. ASURANSI TUGU INDO D\/H TUGU BUNAS",
                            "code": "TUGU INDO"
                        },
                        {
                            "id": 18,
                            "name": "PT. ASURANSI WAHANA TATA",
                            "code": "WAHANA T"
                        },
                        {
                            "id": 19,
                            "name": "PT. ASURANSI WANAMEKAR HANDAYANI",
                            "code": "WANAMEKAR"
                        }
                    ],
                    "from": "1",
                    "last_page": "1",
                    "per_page": "45",
                    "to": "45",
                    "total": "45"
                }
            ], 200 );

        } else {
            $insurance_service = Asmx::setEndpoint( 'GetPerusahaanAsuransi' )->setQuery( [
                'search' => $request->search,
                'limit' => $request->limit,
                'page' => $request->page,
                'sort' => $request->sort,
            ] )->setBody(['request'=>''])->post('form_params');
            \Log::info($insurance_service);
            $insurance = $insurance_service[ 'contents' ];
            $insurance[ 'data' ] = array_map( function( $content ) {
                return [
                    'id' => $content[ 'desc3' ],
                    'name' => $content[ 'desc2' ],
                    'code' => $content[ 'id_perusahaan_asuransi' ]
                ];
            }, $insurance[ 'data' ] );
            return response()->success( [
                'message' => 'Sukses',
                'contents' => $insurance
            ], 200 );
        }
    }
}
