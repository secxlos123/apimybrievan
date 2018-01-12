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

class SentSMSNotifController extends Controller
{
    
	public function message( $request )
	{
		//parameter
		//nama_cust,no_reff,no_hp,rm_mantri,plafond,year,angsuran
		$id_trans = '1-'.date("Ymd His");
		if($kode_message=='1'){
				//1. Pengajuan Kredit 
			$message = "Kepada Yth. Bapak/Ibu ".$request['nama_cust']." Terima kasih atas pengajuan Anda. ".
						"Aplikasi pengajuan Kredit BRIGUNA Anda telah kami terima dengan detail sbb :". 
						"ID Transaksi : ".$id_trans.", ".
						"No Ref : ".$request['no_reff'].". ".
						"Petugas kami akan segera menghubungi Anda. ".
						"Pantau pengajuan kredit Anda dengan nomor referensi diatas, melalui MyBRI, ".
						"website pinjaman.bri.co.id, atau via Petugas Pemasaran kami.";
		}elseif($kode_message=='2'){
				//2. Pengajuan Kredit Anda Sedang Dalam Proses
			$message = "Kepada Yth. Bapak/Ibu ".$request['nama_cust'].", Form pengajuan Kredit BRIGUNA Anda telah ditindaklanjuti oleh Petugas kami, ".
						"dengan detail sbb : ".
						"No Ref ".$request['no_reff'].", ".
						"RM/Mantri ".$request['rm_mantri'].", ".
						"No Handphone : ".$request['no_hp']." ".
						"Terima kasih atas pengajuan kredit Anda. Petugas kami akan segera menghubungi Anda.";
		}elseif($kode_message=='3'){
				//3. Persetujuan Kredit
			$message = "Kepada Yth. Bapak/Ibu ".$request['nama_cust'].". ".
						"Pinjaman anda dengan No Referensi ".$request['no_reff']." telah disetujui dengan detail sbb : ".
						"Plafond:Rp.".$request['plafond'].",-, ".
						"Jangka Waktu : ".$request['year']." bulan. ".
						"Angsuran : Rp.".$request['angsuran'].",-, ".
						"Pola Angsuran : 1 bulanan. ".
						"Mohon hubungi Petugas Pemasaran kami atau BRI XXX untuk proses akad kredit & pencairan kredit. Terima kasih";
		}elseif($kode_message=='4'){
				//4. Pengajuan Kredit Tidak Disetujui
			$message = "Kepada Yth. Bapak/Ibu ".$request['nama_cust'].". ".
						"Kami informasikan pengajuan kredit Anda dengan detail sbb :".
						" Plafond : Rp. ".$request['plafond'].",-, ".
						"Jangka Waktu:".$request['year']." bulan. ".
						"Belum dapat disetujui sehubungan dengan belum terpenuhinya persyaratan Bank yang telah ditetapkan. Terima kasih";
		}elseif($kode_message=='5'){
				//5. Pembatalan Pengajuan Pinjaman
			$message = "KepadaYth. Bapak/Ibu XXX. ".
						"Kami informasikan pembatalan pengajuan kredit Anda dengan detail sebagai berikut : ".
						"Plafond : Rp. 50.000.000,- ".
						"Jangka Waktu : ".$request['year']." bulan. ".
						"Anda dapat mengajukan pinjaman kembali melalui MyBRI atau BRI terdekat. Terima kasih.";
		}elseif($kode_message=='6'){
				//6. SMS reminder angsuran pinjaman (Khusus Debitur Payroll)
			$message = "Yth. Bapak/Ibu ".$request['nama_cust']." ,".
						"Kami informasikan kewajiban angsuran Anda sebesar Rp.".$request['angsuran']." ,- ".
						"jatuh tempo pada 25 Agustus 2017. Harap segera melakukan pembayaran. ".
						"Bilamana telah melakukan pembayaran, abaikan sms ini. Terima kasih.";
		}elseif($kode_message=='7'){
				//7. SMS reminder tunggakan (Khusus Debitur Payroll)
			$message = "Yth. Bapak/Ibu ".$request['nama_cust'].". ".
						"Kami informasikan kewajiban angsuran Anda telah melewati jatuh tempo sebesar Rp. ".$request['angsuran'].",-. ".
						"Harap segera melakukan pembayaran. Terima kasih.";
		}
	}
	public function sentsms( Request $request )
	{
		$message = $this->message($request->all());
		 $client = new Client();
      $requestLeads = $client->request('POST', 'http://10.35.65.61:9997/Service.asmx',
        [
          'headers' =>
          [
            'Content-Type' => 'text/xml',
            'SOAPAction' => 'http://tempuri.org/FCD_SMS'
			
          ],
          'raw' =>'<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FCD_SMS xmlns="http://tempuri.org/">
      <norek>0</norek>
      <divisi>SIT</divisi>
      <produk>Sms Dev</produk>
      <fitur></fitur>
      <hp>'.$request->no_hp.'</hp>
      <pesan>''</pesan>
      <flag>0</flag>
    </FCD_SMS>
  </soap:Body>
</soap:Envelope>'
        ]
      );
      $leads = json_decode($requestLeads->getBody()->getContents(), true);

        return response()->success([
            'contents' => $histories,
            'message' => $branchs['responseDesc']
        ]);
		}
		else{
			$response = ['code'=>400,'descriptions'=>'Gagal','contents'=>''];
			 return $response;
		}
	}


}
