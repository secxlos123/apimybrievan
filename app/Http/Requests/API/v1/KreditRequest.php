<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\BaseRequest;

class KreditRequest extends BaseRequest{

	public function authorize(){
        return true;
    }

    public function rules(){
    	\Log::info('=======kredit request=======');
    	\Log::info($this->all());	

    	switch (strtolower($this->method())){
    		case 'post':
    			//cek segment. ajukan
    			if($this->segment(5)=='ajukankredit'){
    				return [
    				'jenis_nasabah'=>'required|in:nasabah,debitur',
    				'pilihan_kartu'=>'required',
    				'penghasilan_perbulan'=>'required|in:<=10juta,>10juta',
    				'memiliki_kk_bank_lain' =>'required|boolean',
    				'jumlah_penerbit_kartu'=>'',
    				'limit_tertinggi'=>'',
    				'PersonalNIK' => 'required|numeric|digits:16',
    				'PersonalName' => 'required',
    				'PersonalTempatLahir'=>'required',
    				'PersonalStatusPernikahan'=>'required',
    				'PersonalTanggalLahir'=>'required',
    				'PersonalNoHP'=>'required',
    				'PersonalNoTlpRumah'=>'required',
    				'PersonalEmail'=>'required|email',
    				'PersonalNamaGadisKandung'=>'required',
    				'image_npwp'=>'required|mimes:jpg,jpeg,png,zip,pdf',
    				'image_ktp'=>'required|mimes:jpg,jpeg,png,zip,pdf',

    				'image_slip_gaji'=>'required|mimes:jpg,jpeg,png,zip,pdf',
    				'image_nametag'=>'required_if:jenis_nasabah,nasabah|mimes:jpg,jpeg,png,zip,pdf',
    				'image_kartu_bank_lain'=>'required_if:jenis_nasabah,nasabah|mimes:jpg,jpeg,png,zip,pdf',

    				'ao_id'=>'required',
    				'branch_id'=>'required',
    				
    				];
    			}else if($this->segment(5) == 'update-data-los'){ // verifikasi
    				return [
						'PersonalNIK' => 'required',
	    				'PersonalAlamatDomisili' => 'required|max:255',
	    				'PersonalAlamatDomisili2' =>'max:255', 
						'PersonalAlamatDomisili3' =>'max:255',
						'Camat' => '',
						'Lurah' => '',
						'Rt'=> '',
						'Rw' => '',
	    				'PersonalName' => 'required',
	    				'PersonalTanggalLahir'=>'required',
	    				'PersonalTempatLahir'=>'required',
	    				'PersonalJenisKelamin'=>'required',
	    				'PersonalStatusTempatTinggal'=>'required',
	    				'PersonalKewarganegaraan'=>'required', 
						'PersonalLamaMenempatiRumahMM'=>'required',
						'PersonalLamaMenempatiRumahYY'=>'required',
						'PersonalPendidikanTerakhir'=>'required',
						'PersonalKodePos'=>'required',
						'PersonalStatusPernikahan'=>'required',
						'PersonalNamaGadisKandung'=>'required',
						'PersonalNoHP'=>'required',
						'PersonalNoTlpRumah'=>'required',
						'PersonalEmail'=>'required',
						'JobBidangUsaha'=>'required',
						'JobKategoriPekerjaan'=>'required',
						'JobStatusPekerjaan'=>'required',
						'JobTotalPekerja'=>'required',
						'JobNamaPerusahaan'=>'required',
						'JobPangkat'=>'required',
						'JobLamaKerjaYY'=>'required',
						'JobLamaKerjaMM'=>'required',
						'JobAlamatKantor'=>'required',
						'JobKodePos'=>'required',
						'FinanceGajiPerbulan'=>'required',
						'FinanceGajiPertahun'=>'required',
						'FinancePendapatanLainPerbulan'=>'required',
						'FinanceJumlahTanggungan'=>'required',
						'EmergencyNama'=>'required',
						'EmergencyHubunganKeluarga'=>'required',
						'EmergencyAlamat'=>'required',
						'EmergencyKota'=>'required',
						'EmergencyNoTlp'=>'required',
						'subBidangUsaha'=>'required',
						'eform_id'=>'required',
						'PersonalKota'=>'required',
	    				// 'apregno'=>'required',
    				];
    			}else if($this->segment(5) == 'putusan-pinca'){
    				return([
    					'msg'=>'',
    					'limit'=>'',
    					'apRegno'=>'required',
    					'putusan'=>'required|in:approved,rejected',
    					'by' => 'required_if:putusan,approved',
						'userId' =>'required',
						'cpAprLimit'=>'required_if:putusan,approved',
						'potCode'=>'required_if:putusan,approved',
						'wvCode'=>'required_if:putusan,approved',
						'apBillCycle'=>'required_if:putusan,approved',
						'rjCode'=>'required_if:putusan,rejected',
    				]);
    			}else if($this->segment(5) == 'finish-analisa'){
    				return([
    					'apRegno'=>'required',
    					'los_score'=>'required',
    					'catatanRekomendasiAO'=>'',
						'rekomendasiLimitKartu'=>'required',
						'cardType'=>'required',
						'range_limit'=>'required',
						'eform_id'=>'required'
    				]);
    			}else if($this->segment(5) == 'tosms'){
    				return([
    					'appregno' => 'required',
    					'handphone' => 'required',
    					'eform_id' =>'required'
    				]);
    			} 
    			
    			break;

    			//cek segment. update


    	}


    }

}
