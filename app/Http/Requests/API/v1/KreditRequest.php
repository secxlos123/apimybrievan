<?php

namespace App\Http\Requests\API\V1;

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
    				'ao_id'=>'required',
    				'branch_id'=>'required',
    				'pn'=>'required',
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
    			}
    			
    			break;

    			//cek segment. update


    	}


    }

}