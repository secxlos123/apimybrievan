<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Events\Customer\CustomerVerify;
use App\Http\Requests\API\v1\Int\VerificationRequest;
use App\Models\EForm;
use RestwsHc;

class VerificationController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\API\v1\Int\VerificationRequest  $request
     * @param  int  $eform_id
     * @return \Illuminate\Http\Response
     */
    public function show( VerificationRequest $request, $eform_id )
    {
        $eform = EForm::findOrFail( $eform_id );
        $customer = $eform->customer;
        return response()->success( [
            'message' => 'Sukses',
            'contents' => [
                'customer' => [
                    'id' => $customer->id,
                    'nik' => $eform->nik,
                    'is_completed' => $customer->is_completed,
                    'name' => $customer->full_name,
                    'gender' => $customer->gender,
                    'birth_place_id' => $customer->detail->birth_place_id,
                    'birth_place' => $customer->detail->birth_place_id ? $customer->detail->birth_place_city->name : '',
                    'birth_date' => $customer->detail->birth_date,
                    'phone' => !($customer->phone) ? '0' : $customer->phone,
                    'mobile_phone' => $customer->mobile_phone,
                    'address' => $customer->detail->address,
                    'current_address' => $customer->detail->current_address,
                    'citizenship_id' => $customer->detail->citizenship_id,
                    'citizenship_name' => $customer->detail->citizenship_name,
                    'status' => $customer->detail->status_id,
                    'status_name' => $customer->detail->status,
                    'address_status' => $customer->detail->address_status_id,
                    'address_status_name'=> $customer->detail->address_status,
                    'mother_name' => $customer->detail->mother_name,
                    'email' => $customer->email,
                    'city_id'=> $customer->detail->city_id,
                    'city' => $customer->detail->city_id ? $customer->detail->city->name : '',
                    'identity'=> $customer->detail->identity,
                    'couple_nik'=> $customer->detail->couple_nik,
                    'couple_name'=> $customer->detail->couple_name,
                    'couple_birth_place_id' => $customer->detail->couple_birth_place_id,
                    'couple_birth_place' => $customer->detail->couple_birth_place_id ? $customer->detail->couple_birth_place_city->name : '',
                    'couple_birth_date'=> $customer->detail->couple_birth_date,
                    'couple_identity'=> $customer->detail->couple_identity,
                    'job_field_id'=> $customer->detail->job_field_id,
                    'job_field_name'=> $customer->detail->job_field_name,
                    'job_type_id'=> $customer->detail->job_type_id,
                    'job_type_name'=> $customer->detail->job_type_name,
                    'job_id'=> $customer->detail->job_id,
                    'job_name'=> $customer->detail->job_name,
                    'company_name'=> $customer->detail->company_name,
                    'position'=> $customer->detail->position,
                    'position_name'=> $customer->detail->position_name,
                    'work_duration'=> $customer->detail->work_duration,
                    'work_duration_month' => $customer->detail->work_duration_month,
                    'office_address'=> $customer->detail->office_address,
                    'salary'=> $customer->detail->salary,
                    'other_salary'=> $customer->detail->other_salary,
                    'loan_installment'=> $customer->detail->loan_installment,
                    'dependent_amount'=> $customer->detail->dependent_amount,
                    'couple_salary'=> $customer->detail->couple_salary,
                    'couple_other_salary'=> $customer->detail->couple_other_salary,
                    'couple_loan_installment'=> $customer->detail->couple_loan_installment,
                    'emergency_name'=> $customer->detail->emergency_name,
                    'emergency_contact'=> $customer->detail->emergency_contact,
                    'emergency_relation'=> $customer->detail->emergency_relation,
                    'zip_code'=> $customer->detail->zip_code,
                    'kelurahan' => $customer->detail->kelurahan,
                    'kecamatan' => $customer->detail->kecamatan,
                    'kabupaten' => $customer->detail->kabupaten,
                    'zip_code_current'=> $customer->detail->zip_code_current,
                    'kelurahan_current' => $customer->detail->kelurahan_current,
                    'kecamatan_current' => $customer->detail->kecamatan_current,
                    'kabupaten_current' => $customer->detail->kabupaten_current,
                    'zip_code_office'=> $customer->detail->zip_code_office,
                    'kelurahan_office' => $customer->detail->kelurahan_office,
                    'kecamatan_office' => $customer->detail->kecamatan_office,
                    'kabupaten_office' => $customer->detail->kabupaten_office,
                    'source_income' => $customer->financial ? ($customer->financial['source_income'] ? $customer->financial['source_income'] : 'single') : 'single'
                ]
                , 'kpr' => $eform->kpr
                , 'kemendagri' => $this->getKemendagri( $request->header( 'Authorization' ), $eform->nik, $request->header( 'pn' ) )
                , 'cif' => $this->getCIF( $request->header( 'Authorization' ), $eform->nik, $request->header( 'pn' ) )
            ]
        ], 200 );
    }

    /**
     * Resend email verification
     *
     * @return void
     * @author
     **/
    public function resend( $eform_id )
    {
        $eform = EForm::findOrFail( $eform_id );

        if ( $eform->response_status == 'unverified' ) {
            $customer = $eform->customer;
            event( new CustomerVerify( $customer, $eform ) );

            return response()->success( [
                'message' => 'Email Verifikasi berhasil dikirim.'
            ], 200 );
        }

        return response()->success( [
            'message' => 'Email Verifikasi gagal dikirim.'
        ], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchNik( Request $request )
    {
        $nik = is_null($request->input('new_nik')) ? $request->input('nik') : $request->input('new_nik');
        return response()->success( [
            'message' => 'Sukses',
            'contents' => [
                'kemendagri' => $this->getKemendagri( $request->header( 'Authorization' ), $nik, $request->header( 'pn' ) ),
                'cif' => $this->getCIF( $request->header( 'Authorization' ), $nik, $request->header( 'pn' ) )
            ]
        ], 200 );
    }

    /**
     * Get Kemendagri Data.
     *
     * @param  string  $authorization
     * @param  string  $nik
     * @param  string  $pn
     * @return \Illuminate\Http\Response
     */
    public function getKemendagri( $authorization, $nik, $pn )
    {
        
        try {

            // $data = RestwsHc::setBody([
            //     'request' => json_encode([
            //         'requestMethod' => 'get_kemendagri_profile_nik',
            //         'requestData' => [
            //             'nik'     => $nik
            //             , 'id_user' => $pn
            //         ],
            //     ])
            // ])->post( 'form_params' );

            $client = new Client();
            $host = config('restapi.restwshc');
            
            \Log::info("=====HOST GET KEMENDAGRI :");
            \Log::info($host);
            
            $res = $client->request('POST', $host.'get_kemendagri_profile_nik', [
                       'form_params' => [
                            'requestData' => [
                                'nik'     => $nik
                               ,'id_user' => $pn
                                ],
                            ]
                    ]);

            $data = json_decode($res->getBody()->getContents(), true);

            $keys = [
            'name' => 'namaLengkap'
            , 'gender' => 'jenisKelamin'
            , 'birth_place' => 'tempatLahir'
            , 'birth_date' => 'tanggalLahir'
            , 'phone' => ''
            , 'mobile_phone' => ''
            , 'address' => 'alamat'
            , 'citizenship' => ''
            , 'status' => 'statusKawin'
            , 'address_status' => ''
            , 'mother_name' => 'namaIbu'
        ];

        $return = array();

        foreach ($keys as $key => $field) {
            $return[$key] = $this->mapData(
                $data['responseData']
                , $field
                , $data['responseCode']
            );
        }
        \log::info("====Success Get Data KEMENDAGRI====");
        return $return;
            
        } catch (RequestException $e) {

            \log::info("====ERROR EXCEPTION KEMENDAGRI====");
            
            $data = [
                'responseCode' => '408',
                'responseDesc' => 'Get customer profile by nik Failed',
                'responseData' => [
                    'info' => [
                        'cifno' => '',
                        'cfbrnn' => '',
                        'gelar_sebelum_nama' => '',
                        'nama_sesuai_id' => '',
                        'gelar_sesudah_nama' => '',
                        'nama_lengkap' => '',
                        'id_number' => '',
                        "id_type" => '',
                        "id_desc" => "Kartu Tanda Penduduk (KTP)",
                        "id_issue" => null,
                        "id_exp" => " ",
                        "tipe_nasabah" => " ",
                        "tipe_nasabah_desc" => " ",
                        "npwp" => null,
                        "jenis_kelamin" => " ",
                        "tanggal_lahir" => " ",
                        "tempat_lahir" => " ",
                        "nama_ibu_kandung" => " ",
                        "status_nikah" => " ",
                        "agama" => " ",
                        "alamat_id1" => " ",
                        "alamat_id2" => " ",
                        "alamat_id3" => " ",
                        "alamat_id4" => " ",
                        "rt_id" => " ",
                        "rw_id" => " ",
                        "kelurahan_id" => " ",
                        "kecamatan_id" => " ",
                        "kota_id" => " ",
                        "propinsi_id" => " ",
                        "kodepos_id" => " ",
                        "alamat_domisili1" => "xxxxxxxxxxxxxxxx ",
                        "alamat_domisili2" => "xxxxxxxxxxxxxxxx ",
                        "alamat_domisili3" => "xxxxxxxxxxxxxxxx ",
                        "alamat_domisili4" => "xxxxxxxxxxxxxxxx ",
                        "rt_domisili" => "xxxxxxxxxxxxxxxx   ",
                        "rw_domisili" => "xxxxxxxxxxxxxxxx    ",
                        "kelurahan_domisili" => "xxxxxxxxxxxxxxxx ",
                        "kecamatan_domisili" => "xxxxxxxxxxxxxxxx ",
                        "kota_domisili" => "xxxxxxxxxxxxxxxx ",
                        "propinsi_domisili" => "xxxxxxxxxxxxxxxx  ",
                        "kodepos_domisili" => "xxxxxxxxxxxxxxxx",
                        "alamat_kantor1" => "xxxxxxxxxxxxxxxx ",
                        "alamat_kantor2" => "xxxxxxxxxxxxxxxx ",
                        "alamat_kantor3" => "xxxxxxxxxxxxxxxx ",
                        "alamat_kantor4" => "xxxxxxxxxxxxxxxx ",
                        "rt_kantor" => "     ",
                        "rw_kantor" => "     ",
                        "kelurahan_kantor" => "xxxxxxxxxxxxxxxx ",
                        "kecamatan_kantor" => "xxxxxxxxxxxxxxxx ",
                        "kota_kantor" => "xxxxxxxxxxxxxxxx ",
                        "propinsi_kantor" => "xxxxxxxxxxxxxxxx ",
                        "kodepos_kantor" => "xxxxxxxxxxxxxxxx",
                        "alamat_surat" => "",
                        "kewarganegaraan" => "Indonesia ",
                        "negara" => "Indonesia           ",
                        "kode_pajak" => "xxxxxxxxxxxxxxxx",
                        "prioritas" => "xxxxxxxxxxxxxxxx",
                        "prioritas_pbo" => null,
                        "prioritas_pbo_contact" => null,
                        "pep" => "Tidak",
                        "pep_jabatan" => null,
                        "pep_keluarga" => "Tidak",
                        "pep_status_keluarga" => "Tidak",
                        "handphone" => null,
                        "telp_rumah" => null,
                        "telp_kantor" => null,
                        "fax" => null,
                        "email" => null,
                        "kode_pendidikan" => "xxxxxxxxxxxxxxxx",
                        "pendidikan" => "xxxxxxxxxxxxxxxx             ",
                        "kode_jenis_pekerjaan" => "xxxxxxxxxxxxxxxx ",
                        "jenis_pekerjaan" => "xxxxxxxxxxxxxxxx                            ",
                        "nama_kantor" => "xxxxxxxxxxxxxxxx                             ",
                        "kode_bidang_pekerjaan" => "xxxxxxxxxxxxxxxx",
                        "bidang_pekerjaan" => "xxxxxxxxxxxxxxxx                               ",
                        "kode_jabatan" => "xxxxxxxxxxxxxxxx   ",
                        "jabatan" => "xxxxxxxxxxxxxxxx                      ",
                        "lama_bekerja_tahun" => "  ",
                        "lama_bekerja_bulan" => "  ",
                        "kode_penghasilan_per_bulan" => "xxxxxxxxxxxxxxxx",
                        "penghasilan_per_bulan" => "s/d x Juta          ",
                        "kode_omset_per_bulan" => "  ",
                        "omset_per_bulan" => null,
                        "kode_trx_normal_harian" => "xxxxxxxxxxxxxxxx",
                        "trx_normal_harian" => "s/d x juta         ",
                        "kode_sumber_penghasilan" => "xxxxxxxxxxxxxxxx",
                        "sumber_penghasilan" => "Gaji                                    ",
                        "kode_tujuan_buka_rekening" => "xxxxxxxxxxxxxxxx",
                        "tujuan_buka_rekening" => "Investasi                               ",
                        "tanggal_buka_cif" => "2015-01-08 00:00:00.000",
                        "tanggal_maintenance_cif" => "2015-01-08 00:00:00.000",
                        "notes_beneficial_owner" => null,
                        "notes_ibu_kandung" => null,
                        "notes_tanggal_lahir" => null,
                        "kode_bidang_usaha" => null,
                        "bidang_usaha" => null,
                        "tempat_pendirian" => null,
                        "tipe_id_corp" => null,
                        "tipe_id_desc_corp" => null,
                        "no_id_corp" => null,
                        "tanggal_terbit_corp" => null,
                        "tanggal_kadaluarsa_corp" => null,
                        "no_akta_pendirian" => null,
                        "tanggal_akta_pendirian" => null,
                        "no_akta_perubahan" => null,
                        "tanggal_akta_perubahan" => null,
                        "nama_pengurus_1" => null,
                        "jabatan_pengurus_1" => null,
                        "telepon_pengurus_1" => null,
                        "email_pengurus_1" => null,
                        "nama_pengurus_2" => null,
                        "jabatan_pengurus_2" => null,
                        "telepon_pengurus_2" => null,
                        "email_pengurus_2" => null,
                        "nama_pengurus_3" => null,
                        "jabatan_pengurus_3" => null,
                        "telepon_pengurus_3" => null,
                        "email_pengurus_3" => null

                    ],

                    'portofolio' => [
                        "kelompok" => "Tabungan",
                        "tipe_produk" => null,
                        "rekening" => "xxxxx",
                        "mata_uang" => "IDR ",
                        "saldo" => "xxxxx",
                        "saldo_rupiah" => "xxxxx",
                        "tanggal_buka" => "2015-01-08 00:00:00.000",
                        "tipe_produk_rekening" => "xxxxxxxxxxxxxxxx"
                    ],

                    'investasi' => [
                        "kelompok" => "DPLK",
                        "tipe_produk" => null,
                        "rekening" => "xxxxx",
                        "saldo" => "xxxxx",
                        "tipe_produk_rekening" => "xxxxx"
                    ],

                    'card_info' => [
                        "jenis_produk" => "Kartu Kredit",
                        "nama_produk" => "-",
                        "nomor_produk" => "xxxxxxxxxxxxxxxx",
                        "mata_uang" => "-",
                        "kolektibilitas" => "1"
                    ],

                    'loan_detl' => [

                    ]
                ],
            ];

            $keys = [
            'name' => 'namaLengkap'
            , 'gender' => 'jenisKelamin'
            , 'birth_place' => 'tempatLahir'
            , 'birth_date' => 'tanggalLahir'
            , 'phone' => ''
            , 'mobile_phone' => ''
            , 'address' => 'alamat'
            , 'citizenship' => ''
            , 'status' => 'statusKawin'
            , 'address_status' => ''
            , 'mother_name' => 'namaIbu'
        ];

        $return = array();

        foreach ($keys as $key => $field) {
            $return[$key] = $this->mapData(
                $data['responseData']
                , $field
                , $data['responseCode']
            );
        }
            return $return;
        }

        

        
    }

    /**
     * Get CIF Data.
     *
     * @param  string  $authorization
     * @param  string  $nik
     * @param  string  $pn
     * @return \Illuminate\Http\Response
     */
    public function getCIF( $authorization, $nik, $pn )
    {
        $data = RestwsHc::setBody([
                'request' => json_encode([
                    'requestMethod' => 'get_customer_profile_nik',
                    'requestData' => [
                        'app_id' => 'mybriapi'
                        , 'nik'     => $nik
                    ],
                ])
            ])->post( 'form_params' );

        // try {
            
        //     $client = new Client();
            
        //     $host = config('restapi.restwshc');

        //     \Log::info("=====HOST GET CIF :");
        //     \Log::info($host);

        //         $res = $client->request('POST', $host.'get_customer_profile_nik', [
        //                'form_params' => [
        //                     'requestData' => [
        //                          'app_id' => 'mybriapi'
        //                         ,'nik'     => $nik
        //                         ],
        //                     ]
        //             ]);

        //     $data = json_decode($res->getBody()->getContents(), true);

            $keys = [
            'cif_number' => 'cifno'
            , 'name' => 'nama_sesuai_id'
            , 'gender' => 'jenis_kelamin'
            , 'birth_place' => 'tempat_lahir'
            , 'birth_date' => 'tanggal_lahir'
            , 'phone' => 'telp_rumah'
            , 'mobile_phone' => 'handphone'
            , 'address' => 'alamat_id1'
            , 'citizenship' => 'kewarganegaraan'
            , 'status' => 'status_nikah'
            , 'address_status' => ''
            , 'mother_name' => 'nama_ibu_kandung'
        ];

            $return = array();

            foreach ($keys as $key => $field) {
                $return[$key] = $this->mapData(
                    $data['responseData']
                    , $field
                    , $data['responseCode']
                );
            }
            \log::info("====Success Get Data CIF====");
            return $return;

        // } catch (RequestException $e) {

        //     \log::info("====ERROR EXCEPTION CIF====");
            
        //     $data = [
        //         'responseCode' => '408',
        //         'responseDesc' => 'Get customer profile by nik Failed',
        //         'responseData' => [
        //             'info' => [
        //                 'cifno' => '',
        //                 'cfbrnn' => '',
        //                 'gelar_sebelum_nama' => '',
        //                 'nama_sesuai_id' => '',
        //                 'gelar_sesudah_nama' => '',
        //                 'nama_lengkap' => '',
        //                 'id_number' => '',
        //                 "id_type" => '',
        //                 "id_desc" => "Kartu Tanda Penduduk (KTP)",
        //                 "id_issue" => null,
        //                 "id_exp" => " ",
        //                 "tipe_nasabah" => " ",
        //                 "tipe_nasabah_desc" => " ",
        //                 "npwp" => null,
        //                 "jenis_kelamin" => " ",
        //                 "tanggal_lahir" => " ",
        //                 "tempat_lahir" => " ",
        //                 "nama_ibu_kandung" => " ",
        //                 "status_nikah" => " ",
        //                 "agama" => " ",
        //                 "alamat_id1" => " ",
        //                 "alamat_id2" => " ",
        //                 "alamat_id3" => " ",
        //                 "alamat_id4" => " ",
        //                 "rt_id" => " ",
        //                 "rw_id" => " ",
        //                 "kelurahan_id" => " ",
        //                 "kecamatan_id" => " ",
        //                 "kota_id" => " ",
        //                 "propinsi_id" => " ",
        //                 "kodepos_id" => " ",
        //                 "alamat_domisili1" => "xxxxxxxxxxxxxxxx ",
        //                 "alamat_domisili2" => "xxxxxxxxxxxxxxxx ",
        //                 "alamat_domisili3" => "xxxxxxxxxxxxxxxx ",
        //                 "alamat_domisili4" => "xxxxxxxxxxxxxxxx ",
        //                 "rt_domisili" => "xxxxxxxxxxxxxxxx   ",
        //                 "rw_domisili" => "xxxxxxxxxxxxxxxx    ",
        //                 "kelurahan_domisili" => "xxxxxxxxxxxxxxxx ",
        //                 "kecamatan_domisili" => "xxxxxxxxxxxxxxxx ",
        //                 "kota_domisili" => "xxxxxxxxxxxxxxxx ",
        //                 "propinsi_domisili" => "xxxxxxxxxxxxxxxx  ",
        //                 "kodepos_domisili" => "xxxxxxxxxxxxxxxx",
        //                 "alamat_kantor1" => "xxxxxxxxxxxxxxxx ",
        //                 "alamat_kantor2" => "xxxxxxxxxxxxxxxx ",
        //                 "alamat_kantor3" => "xxxxxxxxxxxxxxxx ",
        //                 "alamat_kantor4" => "xxxxxxxxxxxxxxxx ",
        //                 "rt_kantor" => "     ",
        //                 "rw_kantor" => "     ",
        //                 "kelurahan_kantor" => "xxxxxxxxxxxxxxxx ",
        //                 "kecamatan_kantor" => "xxxxxxxxxxxxxxxx ",
        //                 "kota_kantor" => "xxxxxxxxxxxxxxxx ",
        //                 "propinsi_kantor" => "xxxxxxxxxxxxxxxx ",
        //                 "kodepos_kantor" => "xxxxxxxxxxxxxxxx",
        //                 "alamat_surat" => "",
        //                 "kewarganegaraan" => "Indonesia ",
        //                 "negara" => "Indonesia           ",
        //                 "kode_pajak" => "xxxxxxxxxxxxxxxx",
        //                 "prioritas" => "xxxxxxxxxxxxxxxx",
        //                 "prioritas_pbo" => null,
        //                 "prioritas_pbo_contact" => null,
        //                 "pep" => "Tidak",
        //                 "pep_jabatan" => null,
        //                 "pep_keluarga" => "Tidak",
        //                 "pep_status_keluarga" => "Tidak",
        //                 "handphone" => null,
        //                 "telp_rumah" => null,
        //                 "telp_kantor" => null,
        //                 "fax" => null,
        //                 "email" => null,
        //                 "kode_pendidikan" => "xxxxxxxxxxxxxxxx",
        //                 "pendidikan" => "xxxxxxxxxxxxxxxx             ",
        //                 "kode_jenis_pekerjaan" => "xxxxxxxxxxxxxxxx ",
        //                 "jenis_pekerjaan" => "xxxxxxxxxxxxxxxx                            ",
        //                 "nama_kantor" => "xxxxxxxxxxxxxxxx                             ",
        //                 "kode_bidang_pekerjaan" => "xxxxxxxxxxxxxxxx",
        //                 "bidang_pekerjaan" => "xxxxxxxxxxxxxxxx                               ",
        //                 "kode_jabatan" => "xxxxxxxxxxxxxxxx   ",
        //                 "jabatan" => "xxxxxxxxxxxxxxxx                      ",
        //                 "lama_bekerja_tahun" => "  ",
        //                 "lama_bekerja_bulan" => "  ",
        //                 "kode_penghasilan_per_bulan" => "xxxxxxxxxxxxxxxx",
        //                 "penghasilan_per_bulan" => "s/d x Juta          ",
        //                 "kode_omset_per_bulan" => "  ",
        //                 "omset_per_bulan" => null,
        //                 "kode_trx_normal_harian" => "xxxxxxxxxxxxxxxx",
        //                 "trx_normal_harian" => "s/d x juta         ",
        //                 "kode_sumber_penghasilan" => "xxxxxxxxxxxxxxxx",
        //                 "sumber_penghasilan" => "Gaji                                    ",
        //                 "kode_tujuan_buka_rekening" => "xxxxxxxxxxxxxxxx",
        //                 "tujuan_buka_rekening" => "Investasi                               ",
        //                 "tanggal_buka_cif" => "2015-01-08 00:00:00.000",
        //                 "tanggal_maintenance_cif" => "2015-01-08 00:00:00.000",
        //                 "notes_beneficial_owner" => null,
        //                 "notes_ibu_kandung" => null,
        //                 "notes_tanggal_lahir" => null,
        //                 "kode_bidang_usaha" => null,
        //                 "bidang_usaha" => null,
        //                 "tempat_pendirian" => null,
        //                 "tipe_id_corp" => null,
        //                 "tipe_id_desc_corp" => null,
        //                 "no_id_corp" => null,
        //                 "tanggal_terbit_corp" => null,
        //                 "tanggal_kadaluarsa_corp" => null,
        //                 "no_akta_pendirian" => null,
        //                 "tanggal_akta_pendirian" => null,
        //                 "no_akta_perubahan" => null,
        //                 "tanggal_akta_perubahan" => null,
        //                 "nama_pengurus_1" => null,
        //                 "jabatan_pengurus_1" => null,
        //                 "telepon_pengurus_1" => null,
        //                 "email_pengurus_1" => null,
        //                 "nama_pengurus_2" => null,
        //                 "jabatan_pengurus_2" => null,
        //                 "telepon_pengurus_2" => null,
        //                 "email_pengurus_2" => null,
        //                 "nama_pengurus_3" => null,
        //                 "jabatan_pengurus_3" => null,
        //                 "telepon_pengurus_3" => null,
        //                 "email_pengurus_3" => null

        //             ],

        //             'portofolio' => [
        //                 "kelompok" => "Tabungan",
        //                 "tipe_produk" => null,
        //                 "rekening" => "xxxxx",
        //                 "mata_uang" => "IDR ",
        //                 "saldo" => "xxxxx",
        //                 "saldo_rupiah" => "xxxxx",
        //                 "tanggal_buka" => "2015-01-08 00:00:00.000",
        //                 "tipe_produk_rekening" => "xxxxxxxxxxxxxxxx"
        //             ],

        //             'investasi' => [
        //                 "kelompok" => "DPLK",
        //                 "tipe_produk" => null,
        //                 "rekening" => "xxxxx",
        //                 "saldo" => "xxxxx",
        //                 "tipe_produk_rekening" => "xxxxx"
        //             ],

        //             'card_info' => [
        //                 "jenis_produk" => "Kartu Kredit",
        //                 "nama_produk" => "-",
        //                 "nomor_produk" => "xxxxxxxxxxxxxxxx",
        //                 "mata_uang" => "-",
        //                 "kolektibilitas" => "1"
        //             ],

        //             'loan_detl' => [

        //             ]
        //         ],
        //     ];

        //     $keys = [
        //     'cif_number' => 'cifno'
        //     , 'name' => 'nama_sesuai_id'
        //     , 'gender' => 'jenis_kelamin'
        //     , 'birth_place' => 'tempat_lahir'
        //     , 'birth_date' => 'tanggal_lahir'
        //     , 'phone' => 'telp_rumah'
        //     , 'mobile_phone' => 'handphone'
        //     , 'address' => 'alamat_id1'
        //     , 'citizenship' => 'kewarganegaraan'
        //     , 'status' => 'status_nikah'
        //     , 'address_status' => ''
        //     , 'mother_name' => 'nama_ibu_kandung'
        // ];

        //     $return = array();

        //     foreach ($keys as $key => $field) {
        //         $return[$key] = $this->mapData(
        //             $data['responseData']
        //             , $field
        //             , $data['responseCode']
        //         );
        //     }

        //     return $return;

        // }

    }

    /**
     * Mapping Data.
     *
     * @param  array  $data
     * @param  string  $field
     * @param  string  $responseCode
     * @return \Illuminate\Http\Response
     */
    public function mapData( $data, $field, $responseCode )
    {
        // \Log::info($responseCode);
        if ( $responseCode != '00' && $responseCode != '88' ){
            return '';

        } else {
            if ( isset($data['info']) ) {
                $data = $data['info'][0];

            } else {
                $data = $data[0];

            }
            return $responseCode == '00' ? ( isset($data[$field]) ? $data[$field] : '' ) : '';

        }
    }
}
