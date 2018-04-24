<?php

namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Mitra3;
use Sentinel;
use DB;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use RestwsHc;
use Cache;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class SentSMSNotifController extends Controller
{
    
	public function message( $request )
	{
		//parameter
		//nama_cust,no_reff,no_hp,rm_mantri,plafond,year,angsuran,kode_message
		$id_trans = '1-'.date("Ymd His");
		if($request['kode_message']=='1'){
				//1. Pengajuan Kredit 
			$message = "Yth.Bapak/Ibu ".$request['nama_cust'].". Aplikasi Kredit Anda sudah kami terima dengan Nomor Referensi ".$request['no_reff'].". Petugas kami akan segera menghubungi Anda. Terima kasih.";
		}elseif($request['kode_message']=='2'){
			$message = "Yth Bapak/Ibu ".$request['nama_cust'].". Pengajuan Anda dengan Nomor Referensi ".$request['no_reff']." akan ditindaklanjuti 
						oleh Sdr/i. ".$request['rm_mantri']." / dari BRI ".$request['unit_kerja'].". 
						Petugas kami akan segera menghubungi Anda. Terima kasih. ";
				//2. Pengajuan Kredit Anda Sedang Dalam Proses
		}elseif($request['kode_message']=='3'){
				//3. Persetujuan Kredit
/* 							$message = "Yth.Bapak/Ibu ".$request['nama_cust'].", kredit Anda disetujui ".$request['plafond'].".,- JK ".$request['year'].". bulan,angs. 1 bulanan Rp ".$request['angsuran'].
						".,-.Kunjungi BRI ".$request['unit_kerja'].". untuk akad & pencairan kredit.. ";
 */
			$message = "Yth.Bapak/Ibu ".$request['nama_cust'].", kredit Anda disetujui ".$request['plafond'].".,- JK ".$request['year'].". bulan,angs. 1 bulanan ".
						".,-.Kunjungi BRI ".$request['unit_kerja'].". untuk akad dan pencairan kredit. ";
		}elseif($request['kode_message']=='4'){
				//4. Pengajuan Kredit Tidak Disetujui
			$message = "Yth.Bapak/Ibu ".$request['nama_cust'].",Kami informasikan pengajuan kredit Anda Rp. ".$request['plafond'].",- JK ".$request['year'].
						" bulan belum disetujui/belum terpenuhinya persyaratan Bank.";
		}elseif($request['kode_message']=='5'){
				//5. Pembatalan Pengajuan Pinjaman
			$message = "Yth.Bapak/Ibu ".$request['nama_cust'].",Pengajuan kredit Anda Rp. ".$request['plafond'].",- JK ".$request['year'].
						" bulan dibatalkan.Ajukan kembali melalui MyBRI atau Kantor BRI terdekat.";
		}elseif($request['kode_message']=='6'){
				//6. SMS reminder angsuran pinjaman (Khusus Debitur Payroll)
			$message = "Yth Bapak/Ibu ".$request['nama_cust'].", kewajiban angsuran Anda sebesar Rp. ".$request['plafond'].",- jatuh tempo ".$request['jatuh_tempo'].". 
						Harap melakukan pembayaran. Bilamana telah melakukan pembayaran, abaikan sms ini.";
		}elseif($request['kode_message']=='7'){
				//7. SMS reminder tunggakan (Khusus Debitur Payroll)
			$message = "Yth. Bapak/Ibu  ".$request['nama_cust'].". Kami informasikan kewajiban angsuran Anda telah melewati jatuh tempo sebesar 
						Rp.  ".$request['plafond'].",-. Harap segera melakukan pembayaran. Terima kasih.";
		}else{
			$message = $request['message'];
		}
		return $message;
	}
	public function sentsms( $data )
	{		
		\Log::info('==========sent sms==============');
//		\Log::info($request);
		$host = env('APP_URL');
			if($host == 'http://api.dev.net/'){		
				$divisi = 'SIT';
				$produk = 'Sms Dev';
			}else{
				$divisi = 'KRK';
				$produk = 'Kredit Konsumer';
			}

		//$data = $request->all();
		$message = $this->message($data);
		 $client = new Client();
		 $url = '';
		$host = env('APP_URL');
	  if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/'){
		$url = 'http://10.35.65.61:9997/';
		}else{
		$url = 'http://172.21.56.34:9994/';  
	  }
      $requestLeads = $client->request('POST', $url.'Service.asmx',
        [
          'headers' =>
          [
            'Content-Type' => 'text/xml',
            'SOAPAction' => 'http://tempuri.org/FCD_SMS'
			
          ],
          'body' =>'<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FCD_SMS xmlns="http://tempuri.org/">
      <norek>0</norek>
      <divisi>'.$divisi.'</divisi>
      <produk>'.$produk.'</produk>
      <fitur></fitur>
      <hp>'.$data['no_hp'].'</hp>
      <pesan>'.$message.'</pesan>
      <flag>0</flag>
    </FCD_SMS>
  </soap:Body>
</soap:Envelope>'
        ]
      );

        return response()->success([
            'contents' => $requestLeads
        ]);
	}


}
