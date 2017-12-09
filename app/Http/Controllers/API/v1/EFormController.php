<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\EFormRequest;
use App\Events\EForm\Approved;
use App\Events\EForm\RejectedEform;
use App\Events\EForm\VerifyEForm;
use App\Models\EForm;
use App\Models\Customer;
use App\Models\KPR;
use App\Models\BRIGUNA;
use App\Models\EformBriguna;
use App\Models\Mitra;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Collateral;
use App\Models\User;
use DB;

class EFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        \Log::info($request->all());
        $limit = $request->input( 'limit' ) ?: 10;
        $newForm = EForm::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newForm
        ], 200 );
    }

    public function show_briguna( Request $request )
    {
        \Log::info($request->all());
          $eform = EformBriguna::filter( $request )->get();
		  $eform = $eform->toArray();
		  $eform[0]['Url'] = 'http://api.dev.net/uploads/'.$eform['user_id'];
        return response()->success( [
            'contents' => $eform
        ],200 );
    }
	
	
    public function mitra_relation( Request $request )
    {
        \Log::info($request->all());
        $mitra = Mitra::filter( $request )->get();
        // $eform = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );
        return response()->success( [
            'contents' => [
                'data' => $mitra
            ]
        ], 200 );
    }
    /**
     * Display the specified resource.
     *
     * @param  string $type
     * @param  integer $eform_id
     * @return \Illuminate\Http\Response
     */
    public function show( $type, $eform_id )
    {
        $eform = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );

        return response()->success( [
            'contents' => $eform
        ] );
    }

    public function uploadimage($image,$id,$atribute) {
        //$eform = EForm::findOrFail($id);
        $path = public_path( 'uploads/' . $id . '/' );

        if ( ! empty( $this->attributes[ $atribute ] ) ) {
            File::delete( $path . $this->attributes[ $atribute ] );
        }
        $filename = null;
        if ($image) {
            if (!$image->getClientOriginalExtension()) {
                if ($image->getMimeType() == '.pdf') {
                    $extension = '.pdf';
                }else{
                    $extension = 'png';
                }
            }else{
                $extension = $image->getClientOriginalExtension();
            }
            // log::info('image = '.$image->getMimeType());
            $filename = $id . '-'.$atribute.'.' . $extension;
            $image->move( $path, $filename );
        }
        return $filename;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( EFormRequest $request )
    {
        DB::beginTransaction();
        $branchs = \RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_near_branch_v2',
                'requestData'   => [
                    'app_id' => 'mybriapi',
                    'kode_branch' => $request->input('branch_id'),
                    'distance'    => 0,

                    // if request latitude and longitude not present default latitude and longitude cimahi
                    'latitude'  => 0,
                    'longitude' => 0
                ]
            ])
        ])
        ->post('form_params');

        $baseRequest = $request->all();

        // Get User Login
        $user_login = \RestwsHc::getUser();
        $baseRequest['ao_name'] = $user_login['name'];
        $baseRequest['ao_position'] = $user_login['position'];

        if ( $branchs['responseCode'] == '00' ) {
            foreach ($branchs['responseData'] as $branch) {
                if ( $branch['kode_uker'] == $request->input('branch_id') ) {
                    $baseRequest['branch'] = $branch['unit_kerja'];

                }
            }
        }

        if ( $request->product_type == 'kpr' ) {
            if ($baseRequest['status_property'] != ENV('DEVELOPER_KEY', 1)) {
                $baseRequest['developer'] = ENV('DEVELOPER_KEY', 1);
                $baseRequest['developer_name'] = ENV('DEVELOPER_NAME', "Non Kerja Sama");
            }
        }

        $baseArray = array (
            'job_type_id' => 'work_type', 'job_type_name' => 'work_type_name'
            , 'job_id' => 'work', 'job_name' => 'work_name'
            , 'job_field_id' => 'work_field', 'job_field_name' => 'work_field_name'
            , 'citizenship_name' => 'citizenship'
        );

        foreach ($baseArray as $target => $base) {
            if ( isset($baseRequest[$base]) ) {
                $baseRequest[$target] = $baseRequest[$base];
                unset($baseRequest[$base]);
            }
        }
        \Log::info("=======================================================");
        \Log::info($baseRequest);

        if ( $request->product_type == 'briguna' ) {

			        \Log::info("=======================================================");
            /* BRIGUNA */
            $NPWP_nasabah = $request->NPWP_nasabah;
            $KK = $request->KK;
            $SLIP_GAJI = $request->SLIP_GAJI;
            $SK_AWAL = $request->SK_AWAL;
            $SK_AKHIR = $request->SK_AKHIR;
            $REKOMENDASI = $request->REKOMENDASI;

            $id = $request->id;
            $NPWP_nasabah = $this->uploadimage($NPWP_nasabah,$id,'NPWP_nasabah');
            $KK = $this->uploadimage($KK,$id,'KK');
            $SLIP_GAJI = $this->uploadimage($SLIP_GAJI,$id,'SLIP_GAJI');
            $SK_AWAL = $this->uploadimage($SK_AWAL,$id,'SK_AWAL');
            $SK_AKHIR = $this->uploadimage($SK_AKHIR,$id,'SK_AKHIR');
            $REKOMENDASI = $this->uploadimage($REKOMENDASI,$id,'REKOMENDASI');

            $baseRequest['NPWP_nasabah'] = $NPWP_nasabah;
            $baseRequest['KK'] = $KK;
            $baseRequest['SLIP_GAJI'] = $SLIP_GAJI;
            $baseRequest['SK_AWAL'] = $SK_AWAL;
            $baseRequest['SK_AKHIR'] = $SK_AKHIR;
            $baseRequest['REKOMENDASI'] = $REKOMENDASI;
			$SKPG = '';
			if(!empty($request->SKPG)){
				$SKPG = $request->SKPG;
				$SKPG = $this->uploadimage($SKPG,$id,'SKPG');
				$baseRequest['SKPG'] = $SKPG;
				/*----------------------------------*/
			}
				$kpr = BRIGUNA::create( $baseRequest );
			        \Log::info($kpr);
		} else {

            $developer_id = env('DEVELOPER_KEY',1);
            $developer_name = env('DEVELOPER_NAME','Non Kerja Sama');

            if ($baseRequest['developer'] == $developer_id && $baseRequest['developer_name'] == $developer_name)  {
                $property =  Property::create([
                    'developer_id'=>$baseRequest['developer'],
                    'prop_id_bri'=>'1',
                    'name'=>$developer_name,
                    'pic_name'=>'BRI',
                    'pic_phone'=>'-',
                    'address'=>$baseRequest['home_location'],
                    'category'=>'3',
                    'latitude'=>'0',
                    'longitude'=>'0',
                    'description'=>'-',
                    'facilities'=>'-'
                ]);
                $baseRequest['property'] = $property->id;
                $baseRequest['property_name'] = $developer_name;
                \Log::info('=================== Insert Property===========');
                \Log::info($property);
                if ($property) {
                    $propertyType = PropertyType::create([
                        'property_id'=>$property->id,
                        'name'=>$developer_name,
                        'building_area'=>$baseRequest['building_area'],
                        'price'=>$baseRequest['price'],
                        'surface_area'=>$baseRequest['building_area'],
                        'electrical_power'=>'-',
                        'bathroom'=>0,
                        'bedroom'=>0,
                        'floors'=>0,
                        'carport'=>0
                    ]);
                    \Log::info('=================== Insert Property type===========');
                    \Log::info($propertyType);
                    $baseRequest['property_type']= $propertyType->id;
                    $baseRequest['property_type_name']= $developer_name;
                    if ($propertyType) {
                        $data = [
                        'developer_id' => $developer_id,
                        'property_id' => $property->id,
                        'status' => Collateral::STATUS[0]
                    ];
                    $collateral = Collateral::updateOrCreate(['property_id' => $property->id],$data);
                    \Log::info('=================== Insert Collateral===========');
                    \Log::info($collateral);
                    }
                }
            }
            $kpr = KPR::create( $baseRequest );

        }

        DB::commit();
        return response()->success( [
            'message' => 'Data e-form berhasil ditambahkan.',
            'contents' => $kpr
        ], 201 );
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function submitScreening( Request $request )
    {
        DB::beginTransaction();

        if ( $request->has('selected_sicd') ) {
            $eform = EForm::find( $request->input('eform_id') );

            $calculate = array(
                $request->input('pefindo', 'Hijau')
                , $request->input('dhn', 'Hijau')
                , $request->input('sicd', 'Hijau')
            );

            if ( in_array('Merah', $calculate) ) {
                $result = '3';

            } else if ( in_array('Kuning', $calculate) ) {
                $result = '2';

            } else {
                $result = '1';

            }

            $eform->update( [
                'selected_sicd' => $request->input('selected_sicd')
                , 'prescreening_status' => $result
            ] );

            $eform = array();

        } else {
            $eform = EForm::findOrFail( $request->id );
            $eform->update( [ 'prescreening_status' => $request->prescreening_status ] );

        }

        DB::commit();
        return response()->success( [
            'message' => 'Screening e-form berhasil disimpan.',
            'contents' => $eform
        ], 201 );
    }

    /**
     * Get data for prescreening.
     *
     * @return \Illuminate\Http\Response
     */
    public function postPrescreening( Request $request )
    {
        $data = EForm::findOrFail($request->eform);
        $personal = $data->customer->personal;

        $dhn = json_decode((string) $data->dhn_detail);
        if ( !isset($dhn->responseData) ) {
            $dhn = json_decode((string) '{"responseCode":"01","responseDesc":"","responseData":[{"kategori":null,"keterangan":"","warna":"Hijau","result":""}]}');
        }

        $sicd = json_decode((string) $data->sicd_detail);
        if ( !isset($sicd->responseData) ) {
            $sicd = json_decode((string) '{"responseCode":"01","responseDesc":"","responseData":[{"status":null,"acctno":null,"cbal":null,"bikole":null,"result":null,"cif":null,"nama_debitur":null,"tgl_lahir":null,"alamat":null,"no_identitas":null}]}');
        }

        $html = '';

        foreach (explode(',', $data->uploadscore) as $value) {
            if ($value != '') {
                $html .= asset('uploads/'.$data->nik.'/'.$value) . ',';
            }
        }

        $data['uploadscore'] = $html;

        return response()->success( [
            'message' => 'Data Screening e-form',
            'contents' => [
                'eform' => $data
                , 'dhn' => $dhn->responseData
                , 'sicd' => $sicd->responseData
            ]
        ], 200 );
    }

    /**
     * Set E-Form AO disposition.
     *
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function disposition( EFormRequest $request, $id )
    {
        DB::beginTransaction();
        $eform = EForm::findOrFail( $id );
        $ao_id = substr( '00000000' . $request->ao_id, -8 );

        $baseRequest = [ 'ao_id' => $ao_id ];
        // Get User Login
        $user_login = \RestwsHc::getUser($ao_id);
        $baseRequest['ao_name'] = $user_login['name'];
        $baseRequest['ao_position'] = $user_login['position'];

        $eform->update( $baseRequest );

        DB::commit();
        return response()->success( [
            'message' => 'E-Form berhasil di disposisi',
            'contents' => $eform
        ], 201 );
    }

    /**
     * Set E-Form AO disposition.
     *
     * @param integer $eform_id
     * @param  \App\Http\Requests\API\v1\EFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function approve( EFormRequest $request, $eform_id )
    {
        DB::beginTransaction();

        $baseRequest = $request;

        // Get User Login
        $user_login = \RestwsHc::getUser();
        $baseRequest['pinca_name'] = $user_login['name'];
        $baseRequest['pinca_position'] = $user_login['position'];

        $eform = EForm::approve( $eform_id, $baseRequest );
        if( $eform['status'] ) {

            $data =  EForm::findOrFail($eform_id);
            if ($request->is_approved) {
                event( new Approved( $data ) );
            } else {
                event( new RejectedEform( $data ) );
            }

            $detail = EForm::with( 'visit_report.mutation.bankstatement' )->findOrFail( $eform_id );
            generate_pdf('uploads/'. $detail->nik, 'lkn.pdf', view('pdf.approval', compact('detail')));

            DB::commit();
            return response()->success( [
                'message' => 'E-form berhasil di' . ( $request->is_approved ? 'approve.' : 'reject.' ),
                'contents' => $eform
            ], 201 );

        } else {
            DB::rollback();
            return response()->success( [
                'message' => isset($eform['message']) ? $eform['message'] : 'Approval E-Form Gagal',
                'contents' => $eform
            ], 400 );
        }
    }

    /**
     * Insert data to core BRI.
     *
     * @param integer $step_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function insertCoreBRI( Request $request, $eform_id, $step_id )
    {
        DB::beginTransaction();
        $eform = EForm::findOrFail( $eform_id );
        $result = $eform->insertCoreBRI( $step_id );

        DB::commit();
        dd( $result );
    }

    /**
     * Approve / Reject verification specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $token
     * @param  string $status
     * @return \Illuminate\Http\Response
     */
    public function verify( Request $request, $token, $status )
    {
        DB::beginTransaction();
        $verify = EForm::verify( $token, $status );

        if( $verify['message'] ) {
            if ($verify['contents']) {
                if ($status == 'approve') {
                    $detail = EForm::with( 'customer', 'kpr' )->where('id', $verify['contents']->id)->first();
                    generate_pdf('uploads/'. $detail->nik, 'permohonan.pdf', view('pdf.permohonan', compact('detail')));
                }

                event( new VerifyEForm( $verify['contents'] ) );
            }
            DB::commit();
            $code = 201;

        } else {
            DB::rollback();
            $code = 404;

        }


        return response()->success( $verify, $code );
    }

   /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @author erwan.akse@wgs.co.id
     */
    public function delete(Request $request)
    {
        DB::beginTransaction();
        $eform = EForm::findOrFail($request->eform_id);
        if ($eform->kpr->is_sent == false ) {
          User::destroy($eform->user_id);
          DB::commit();
        return response()->success( [
            'message' => 'Hapus User Berhasil',
        ], 200 );
      }else
      {
        DB::rollback();
        return response()->error( [
            'message' => 'User Tidak Dapat Dihapus',
        ], 422 );
      }
    }
}
