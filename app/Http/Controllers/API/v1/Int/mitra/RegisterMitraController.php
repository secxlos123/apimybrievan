<?php

namespace App\Http\Controllers\API\v1\Int\mitra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Models\User;
use App\Models\Mitra\mitra0;
use App\Models\Mitra\mitra1;
use App\Models\Mitra\mitra2;
use App\Models\Mitra\mitra3;
use App\Models\Mitra\mitra4;
use App\Models\Mitra\mitra5;
/* use App\Models\Mitra\MitraHeader;
use App\Models\Mitra\MitraDetail;
use App\Models\Mitra\MitraPemutus; */
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use URL;

class RegisterMitraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    function client() {
		$host = (env('APP_URL') == 'http://apimybri.bri.co.id/')? config('restapi.api_asmx_las_prod'):config('restapi.api_asmx_las_dev');
        return new \SoapClient($host);
    }
    public function index( Request $request )
    {
        \Log::info($request->all());
        $limit = $request->input( 'limit' ) ?: 10;
        $newForm = GIMMICK::filter( $request )->paginate( $limit );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $newForm
        ], 200 );
    }

    public function uploadimage($image,$id,$atribute) {
        //$eform = EForm::findOrFail($id);
        $path = public_path( 'uploads/mitra/' . $id . '/' );

        if ( ! empty( $this->attributes[ $atribute ] ) ) {
            File::delete( $path . $this->attributes[ $atribute ] );
        }
        $filename = null;
        if ($image) {
            if (!$image->getClientOriginalExtension()) {
                if ($image->getMimeType() == '.pdf') {
                    $extension = '.pdf';
                }elseif ($image->getMimeType() == '.jpg') {
                    $extension = 'jpg';
                }elseif ($image->getMimeType() == '.jpeg') {
                    $extension = 'jpeg';
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

    public function store( Request $request )
    {
		try{
        $baseRequest = $request->all();
		if($baseRequest['lo_mitra']=='mitra'){
			$mitra = mitra0::create( $baseRequest );
		}elseif($baseRequest['lo_mitra']=='mitra_detail_dasar'){
			$mitra = mitra1::create( $baseRequest );
		}elseif($baseRequest['lo_mitra']=='mitra_detail_data'){
			$laporan_keuangan = $this->uploadimage($baseRequest['laporan_keuangan'],$baseRequest['id_file'],'laporan_keuangan');
			$baseRequest['laporan_keuangan'] = $laporan_keuangan;
			$legalitas_perusahaan = $this->uploadimage($baseRequest['legalitas_perusahaan'],$baseRequest['id_file'],'legalitas_perusahaan');
			$baseRequest['legalitas_perusahaan'] = $legalitas_perusahaan;
			$mitra = mitra2::create( $baseRequest );
		}elseif($baseRequest['lo_mitra']=='mitra_detail_fasilitas'){
			$upload_fasilitas_bank = $this->uploadimage($baseRequest['upload_fasilitas_bank'],$baseRequest['id_file'],'upload_fasilitas_bank');
			$baseRequest['upload_fasilitas_bank'] = $upload_fasilitas_bank;
			$upload_ijin = $this->uploadimage($baseRequest['upload_ijin'],$baseRequest['id_file'],'upload_ijin');
			$baseRequest['upload_ijin'] = $upload_ijin;
			$mitra = mitra3::create( $baseRequest );
		}elseif($baseRequest['lo_mitra']=='mitra_detail_payroll'){
			$mitra = mitra4::create( $baseRequest );
		}elseif($baseRequest['lo_mitra']=='mitra_pemutus'){
			$mitra = mitra5::create( $baseRequest );
		}elseif($baseRequest['lo_mitra']=='mitra_las'){
			$client = $this->client();
            $mitra = $client->insertUpdateInstansiBRI($baseRequest);
		}
/* 		$mitraheader = MitraHeader::create( $baseRequest['mitra']['header'] );
        $mitradetail = MitraDetail::create( $baseRequest['mitra']['detail'] );
        $mitrapemutus = MitraPemutus::create( $baseRequest['mitra']['pemutus'] ); */
			$return = [
                    'message' => 'Data mitra berhasil Ditambahkan.',
                    'contents' => 'Sukses'
                ];
		} catch (Exception $e) {
            DB::rollback();
           $return = [
                    'message' => 'Terjadi Kesalahan Silahkan Tunggu Beberapa Saat Dan Ulangi.',
                    'contents' => 'Gagal'
                ];
        }
		return $return;
    }

}
