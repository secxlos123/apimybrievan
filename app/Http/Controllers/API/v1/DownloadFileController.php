<?php

namespace App\Http\Controllers\API\v1;

use App\Order;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use App\Mail\suratrekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class DownloadFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function Download(Request $request)
	{ 
	$file = storage_path('/app/PDF/Surat_Kuasa_Potong_Upah.pdf');
        return Response::download($file, "Surat_Kuasa_Potong_Upah.pdf");
	}
	public function Download2(Request $request)
	{ 
	$file2 = storage_path('/app/PDF/Surat_Rekomendasi_Atasan.pdf');
        return Response::download($file2);
	}
   
}

