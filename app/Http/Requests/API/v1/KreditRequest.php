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
    				'penghasilan_perbulan'=>'required',
    				'jumlah_penerbit_kartu'=>'required',
    				'limit_tertinggi'=>'required_if:jumlah_penerbit_kartu,1',
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
    				'pn'=>'required',

    	// 			'PersonalAlamatDomisili' => 'required',
    				
    				
    	// 			'PersonalJenisKelamin'=>'required',
    	// 			'PersonalStatusTempatTinggal'=>'required',
    	// 			'PersonalKewarganegaraan'=>'required', 
					// 'PersonalLamaMenempatiRumahMM'=>'required',
					// 'PersonalLamaMenempatiRumahYY'=>'required',
					// 'PersonalPendidikanTerakhir'=>'required',
					// 'PersonalKodePos'=>'required',
					
					// 'PersonalNoTlpRumah'=>'required',
					
					// 'JobBidangUsaha'=>'required',
					// 'JobKategoriPekerjaan'=>'required',
					// 'JobStatusPekerjaan'=>'required',
					// 'JobTotalPekerja'=>'required',
					// 'JobNamaPerusahaan'=>'required',
					// 'JobPangkat'=>'required',
					// 'JobLamaKerjaYY'=>'required',
					// 'JobLamaKerjaMM'=>'required',
					// 'JobAlamatKantor'=>'required',
					// 'JobKodePos'=>'required',
					// 'FinanceGajiPerbulan'=>'required',
					// 'FinanceGajiPertahun'=>'required',
					// 'FinancePendapatanLainPerbulan'=>'required',
					// 'FinanceJumlahTanggungan'=>'required',
					// 'EmergencyNama'=>'required',
					// 'EmergencyHubunganKeluarga'=>'required',
					// 'EmergencyAlamat'=>'required',
					// 'EmergencyKota'=>'required',
					// 'EmergencyNoTlp'=>'required',
    				
    				];
    			}else if($this->segment(5) == 'updateverifikasikredit'){
    				return [
						'PersonalNIK' => 'required',
	    				'PersonalAlamatDomisili' => 'required',
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
						'SubBidangUsaha'=>'required',
	    				'apregno'=>'required',
    				];
    			}else if($this->segment(5) == 'putusan-pinca'){
    				return([
    					'msg'=>'',
    					'apRegno'=>'required',
    					'putusan'=>'required|in:approved,rejected',
    					'by' => 'required',
    					'stg' => 'required|in:APRV',
						'userId' =>'required',
						'tc' => 'required',
						'cpId'=> 'required',
						'networkId'=> 'required',
						'productId'=>'required',
						'cardTypeId'=>'required|numeric|digits:3',
						'plasticId'=>'required',
						'cpAprLimit'=>'required',
						'potCode'=>'required',
						'wvCode'=>'required',
						'apBillCycle'=>'required',
						'apImigincator'=>'required',
						'cpSeq'=>'required',
						'aprStatus'=>'required',
						'mode'=>'required',
						'fwd'=>''
    				]);
    			}
    			
    			break;

    			//cek segment. update


    	}


    }

}