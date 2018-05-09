<?php

namespace App\Http\Controllers\API\v1\Int\mitra;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Models\User;
use App\Models\Mitra\ApprovalFasilitas;
use App\Models\Mitra\ApprovalSimpanan;
use App\Models\Mitra\ApprovalMitra;
use DB;

class ApprovalMitraController extends Controller
{
   
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
	public function approval_mitra_data($data){
		$return['fasilitas_jasa'] = $data['approval']['fasilitas_jasa'];
		$return['daftar_ijin'] = $data['approval']['daftar_ijin'];
		$return['id_approval'] = $data['approval']['id_approval'];
		$return['id_header'] = $data['approval']['id_header'];
		return $return;
	}
	public function approval_simpanan_data($data){
		$return['fasilitas_jasa'] = $data['approval']['fasilitas_jasa'];
		$return['jenis_simpanan'] = $data['approval']['jenis_simpanan'];
		$return['no_rekening'] = $data['approval']['no_rekening'];
		$return['rata_saldo'] = $data['approval']['rata_saldo'];
		$return['no_cif'] = $data['approval']['no_cif'];
		$return['rata_mutasi'] = $data['approval']['rata_mutasi'];
		$return['nama_pemilik_rekening'] = $data['approval']['nama_pemilik_rekening'];
		$return['jumlah_simpanan'] = $data['approval']['jumlah_simpanan'];
		$return['id_approval'] = $data['approval']['id_approval'];
		return $return;
	}
	public function approval_fasilitas_data($data){
		$return['fasilitas_jasa'] = $data['approval']['fasilitas_jasa'];
		$return['jenis_simpanan'] = $data['approval']['jenis_simpanan'];
		$return['total_os'] = $data['approval']['total_os'];
		$return['presentase_npl'] = $data['approval']['presentase_npl'];
		$return['os_pl'] = $data['approval']['os_pl'];
		$return['jumlah_debitur'] = $data['approval']['jumlah_debitur'];
		$return['os_npl'] = $data['approval']['os_npl'];
		$return['jumlah_debitur_npl'] = $data['approval']['jumlah_debitur_npl'];
		$return['id_approval'] = $data['approval']['id_approval'];
		return $return;
	}
    public function store( Request $request )
    {
	try{
		$baseRequest = $request->all();
		$baseRequest['approval']['id_approval']=date("YmdHms");
		$approval_mitra_data = approval_mitra_data($baseRequest);
		ApprovalMitra::create( $approval_mitra_data );
		if($baseRequest['approval']['fasilitas_jasa']=='simpanan'){			
			$approval_simpanan_data = approval_simpanan_data($baseRequest);
			ApprovalSimpanan::create( $approval_simpanan_data );
		}elseif($baseRequest['approval']['fasilitas_jasa']=='fasilitas'){
			$approval_fasilitas_data = approval_fasilitas_data($baseRequest);
			ApprovalFasilitas::create( $approval_simpanan_data );
		}		
			$return = [
                    'message' => 'Data Berhasil Diberi Penilaian.',
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
