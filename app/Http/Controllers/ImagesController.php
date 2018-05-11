<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Response;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($file)
    {
        $cekpdf = substr($file, -3);
        if($cekpdf == 'pdf'){
            return response()->error([
                'message' => "you can't access this site !",
            ]);
        }else{
            $storagePath = public_path('uploads/'.$file);
            return Image::make($storagePath)->response();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show2($nik, $pdf, $key)
    {
        $date = date('dmy');
        \Log::info($date);
        if($key == "key=".$date){
            return response()->download(public_path('uploads/'.$nik.'/'.$pdf), null, [], null);
        }else{
            return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
           ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show3(Request $request, $folder, $file)
    {
        $cekpdf = substr($file, -3);
        if($cekpdf == 'pdf'){
            \Log::info("========MASUK KESINI GAK (PDF)================");
            $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
            \Log::info($secure);
            if($secure == 1 ){
                $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
                    // return response()->download(public_path('uploads/'.$folder.'/'.$file), null, [], null);
                    return Response::download(public_path('uploads/'.$folder.'/'.$file), null, [], null);
                }else{
                    return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
                ]);
            }
        }
        
        $header = $request->ip();
        $server = $request->server();
        $ip = env('ACCESS_CLAS_IP', '127.0.0.1');
        $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
        
        if($secure != 1 ){
            $storagePath = public_path('uploads/'.$folder.'/'.$file);
            return Image::make($storagePath)->response();
        }else{
            return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
           ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show4(Request $request, $folder, $id, $file)
    {
        $header = $request->ip();
        $server = $request->server();
        $ip = env('ACCESS_CLAS_IP', '127.0.0.1');
        $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;

        if($secure != 1 ){
            $storagePath = public_path('uploads/'.$folder.'/'.$id.'/'.$file);
            return Image::make($storagePath)->response();
        }else{
            return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
           ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show5(Request $request, $folder, $other, $id, $file)
    {
        $header = $request->ip();
        $server = $request->server();
        $ip = env('ACCESS_CLAS_IP', '127.0.0.1');
        $secure = $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') ? $request->server('HTTP_UPGRADE_INSECURE_REQUESTS') : NULL;
    
        if($secure != 1 ){
            $storagePath = public_path('uploads/'.$folder.'/'.$other.'/'.$id.'/'.$file);
            return Image::make($storagePath)->response();
        }else{
            return response()->error([
                'message' => "you can't access this site !",
                'contents' => ["scure" => "Permission Denied Access"]
           ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
