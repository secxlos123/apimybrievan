<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Events\EForm\Approved;
use App\Events\EForm\RejectedEform;
use App\Events\EForm\VerifyEForm;
use App\Models\User;
use App\Models\DIRRPC;
use App\Models\DIRRPC_DETAIL;
use App\Models\DIRRPC2;
use DB;

class dirrpcController extends Controller
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
        if($request->act=='search'){
			$newDir = DIRRPC::filter( $request )->paginate( $limit );
		}elseif($request->act=='maintance'){
			$newDir = DIRRPC_DETAIL::filter( $request )->paginate( $limit );
		}
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newDir
        ], 200 );
    }
	 public function getdir_rpc( Request $request )
    {
        \Log::info($request->all());
        $limit = $request->input( 'limit' ) ?: 10;
        $newDir = DIRRPC2::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newDir
        ], 200 );
    }
	
		 public function hapus_dir( Request $request )
    {
        \Log::info($request->all());
		$data = $request->dirrpc;
		$no = $data['no'];  
			$limit = 10;
		       
		if($data['act']=='maintance'){
			$count = DIRRPC_DETAIL::where("id_detail",$data['no'])->count();
			$get = DIRRPC_DETAIL::where("id_detail",$data['no'])->get();
			$newDir2 = DIRRPC_DETAIL::where("id_detail",$data['no'])->delete();
				if($count=='1'){
					$no = $get['0']['id_header'];
					$newDir = DIRRPC::where("no",$no)->delete();
				}
		}else{
			
				$newDir2 = DIRRPC_DETAIL::where("id_header",$data['no'])->delete();
		        $newDir = DIRRPC::where("no",$data['no'])->delete();
		}
//		$briguna = DIRRPC::where("no","=",$no);
//        $newDir = DIRRPC2::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newDir
        ], 200 );
    }
	
	 public function get_dir( Request $request )
    {
        \Log::info($request->all());
			$no = $request->no;  
			$limit = 10;
            $newDir = DIRRPC::where("no",$no)->get();
		return response()->success( [
            'message' => 'Sukses',
            'contents' => $newDir
        ], 200 );
    }
	
	 public function get_dir_detail( Request $request )
    {
        \Log::info($request->all());
			$no = $request->no;  
			$limit = 10;
            $newDir = DIRRPC_DETAIL::where("no",$no)->get();
		return response()->success( [
            'message' => 'Sukses',
            'contents' => $newDir
        ], 200 );
    }

    public function show_briguna( Request $request )
    {
        \Log::info($request->all());
          $eform = EformBriguna::filter( $request )->get();
		  $eform = $eform->toArray();
		  $eform[0]['Url'] = 'http://api.dev.net/uploads/'.$eform[0]['user_id'];
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
     * @param  \App\Http\Requests\API\v1\GimmickRequest  $request
     * @return \Illuminate\Http\Response
     */
	function detailadd($data,$i){
		$array = [
				'penghasilan_minimal'=>$data[0],
				'penghasilan_maksimal'=>$data[1],
				'dir_persen'=>$data[2],
				'payroll'=>$data[3],
				'id_header'=>$data[4],
				'id_detail'=>$data[4].$i,
		];
		return $array;
	}
    public function store( GimmickRequest $request )
    {
        $baseRequest = $request->all();
		$datad =$baseRequest['dirrpc'];
		
		
		$x = $baseRequest['dirrpc']['countminus1'];
			$dirrpc = DIRRPC::create( $baseRequest['dirrpc'] );
		for($i=0;$i<=$x;$i++){
			 $detaildata = $this->detailadd(json_decode($baseRequest['dirrpc']['data'.$i]),$i);
			 $dirrpc_detail = DIRRPC_DETAIL::create( $detaildata );
		}
		//return $request->all();die();
       /*  $dirrpc = DIRRPC::create( $baseRequest['dirrpc'] ); */
		return $dirrpc;
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
     * @param  \App\Http\Requests\API\v1\GimmickRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function disposition( GimmickRequest $request, $id )
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
     * @param  \App\Http\Requests\API\v1\GimmickRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function approve( GimmickRequest $request, $eform_id )
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
            generate_pdf('uploads/'. $detail->nik, $detail->ref_number.'-lkn.pdf', view('pdf.approval', compact('detail')));

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
                    generate_pdf('uploads/'. $detail->nik, $detail->ref_number.'-permohonan.pdf', view('pdf.permohonan', compact('detail')));
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
