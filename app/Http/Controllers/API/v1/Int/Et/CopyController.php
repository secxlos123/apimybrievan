<?php

namespace App\Http\Controllers\API\v1\Int\Et;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\GimmickRequest;
use App\Models\User;
use DB;

class CopyController extends Controller
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

	public function insert($insert){
		
		$name_file = basename(__FILE__);
		$table = str_replace('Controller.php','_tbl',$name_file);
		DB::table('et_'.$table)->insert(
			$insert
			);
	}
    		/* $name_array = array_keys($_GET);
		$testing = '';
		for($i=0;$i<count($name_array);$i++){
			$name = $name_array[$i];
			$testing .=$name;
		} */
    public function store( Request $request )
    {
			$insert = $this->insert($request);
		try{
			if(isset($insert)){
				$return = [
						'message' => 'Data berhasil Ditambahkan.',
						'contents' => 'Sukses'
					];
			}else{
				DB::rollback();
				$return = [
						'message' => 'Terjadi Kesalahan Silahkan Tunggu Beberapa Saat Dan Ulangi.',
						'contents' => 'Gagal'
					];
			}
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
