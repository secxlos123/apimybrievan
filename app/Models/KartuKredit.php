<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\EForm;



class KartuKredit extends Model
{
	//=================INFO=================
	//jenis nasabah -> diisi "nasabah" atau "debitur" 
	//1. nasabah = nasabah yang tidak memiliki pinjaman
	//2. debitur = nasabah yang memili pinjamna
	//======================================
	//pilihan kartu -> kartu yang user ajukan. diisi "world access","easy card", "platinum","touch"

	//gambar gimana dong
    protected $fillable = [
    	'nik','hp','email',
    	'jenis_kelamin','nama','tempat_lahir','telephone',
    	'pendidikan','pekerjaan','tiering_gaji',
    	'agama','jenis_nasabah','pilihan_kartu',
    	'penghasilan_perbulan','jumlah_penerbit_kartu',
    	'memiliki_kk_bank_lain','limit_tertinggi'
    ];

    protected $hidden = [
        'id'
    ];

    public function convertToAddDataLosFormat(Request $req,$type){

         // $validatedData = $req->validate([
         //    'PersonalName' => 'required',
         //    'PersonalNIK' => 'required',
         //    'PersonalTempatLahir' => 'required',
         //    'PersonalTanggalLahir' => 'required'
         // ]);

        

        try{
                
            $personalName = $req['PersonalName'];
            $personalNIK = $req['PersonalNIK'];
            $personalTempatLahir = $req['PersonalTempatLahir'];
            $personalTanggalLahir = $req['PersonalTanggalLahir'];
            $personalAlamatDomisili = $req['PersonalAlamatDomisili'];

            $personalJenisKelamin = $req['PersonalJenisKelamin'];
            $personalStatusTempatTinggal = $req['PersonalStatusTempatTinggal'];
            $personalKewarganegaraan = $req['PersonalKewarganegaraan'];
            $personalLamaMenempatiRumahMM = $req['PersonalLamaMenempatiRumahMM'];
            $personalLamaMenempatiRumahYY = $req['PersonalLamaMenempatiRumahYY'];

            $personalPendidikanTerakhir = $req['PersonalPendidikanTerakhir'];
            $personalKodePos = $req['PersonalKodePos'];
            $personalStatusPernikahan = $req['PersonalStatusPernikahan'];
            $personalNamaGadisKandung = $req['PersonalNamaGadisKandung'];
            $personalNoHP = $req['PersonalNoHP'];

            $personalNoTlpRumah = $req['PersonalNoTlpRumah'];
            $personalEmail= $req['PersonalEmail'];
            $jobBidangUsaha = $req['JobBidangUsaha'];
            $jobKategoriPekerjaan = $req['JobKategoriPekerjaan'];
            $jobStatusPekerjaan = $req['JobStatusPekerjaan'];

            $jobTotalPekerja = $req['JobTotalPekerja'];
            $jobNamaPerusahaan = $req['JobNamaPerusahaan'];
            $jobPangkat = $req['JobPangkat'];
            $jobLamaKerjaYY = $req['JobLamaKerjaYY'];
            $jobLamaKerjaMM = $req['JobLamaKerjaMM'];

            $jobAlamatKantor = $req['JobAlamatKantor'];
            $jobKodePos = $req['JobKodePos'];
            $financeGajiPerbulan = $req['FinanceGajiPerbulan'];
            $financeGajiPertahun = $req['FinanceGajiPertahun'];
            $financePendapatanLainPerbulan = $req['FinancePendapatanLainPerbulan'];

            $financeJumlahTanggungan = $req['FinanceJumlahTanggungan'];
            $emergencyNama = $req['EmergencyNama'];
            $emergencyHubunganKeluarga = $req['EmergencyHubunganKeluarga'];
            $emergencyAlamat = $req['EmergencyAlamat'];
            $emergencyKota = $req['EmergencyKota'];

            $emergencyNoTlp = $req['EmergencyNoTlp'];
            $emergencyKota = $req['EmergencyKota'];
            $cardType = $req['CardType'];

            if ($type == 'update'){
                $appNumber = $req['appNumber'];
            }
            
        }catch (Exception $e){
            echo "terdapat error";
        }

         $informasiLos = [
            'PersonalName'=>$personalName,
            'PersonalNIK'=>$personalNIK,
            'PersonalTempatLahir'=>$personalTempatLahir,
            'PersonalTanggalLahir'=>$personalTanggalLahir,
            'PersonalAlamatDomisili'=>$personalAlamatDomisili,

            'PersonalJenisKelamin'=>$personalJenisKelamin,
            'PersonalStatusTempatTinggal' =>$personalStatusTempatTinggal,
            'PersonalKewarganegaraan' => $personalKewarganegaraan,
            'PersonalLamaMenempatiRumahMM' => $personalLamaMenempatiRumahMM,
            'PersonalLamaMenempatiRumahYY' => $personalLamaMenempatiRumahYY,

            'PersonalPendidikanTerakhir' =>$personalPendidikanTerakhir,
            'PersonalKodePos' => $personalKodePos,
            'PersonalStatusPernikahan'=>$personalStatusPernikahan,
            'PersonalNamaGadisKandung' =>$personalNamaGadisKandung,
            'PersonalNoHP' => $personalNoHP,

            'PersonalNoTlpRumah' => $personalNoTlpRumah,
            'PersonalEmail' => $personalEmail,
            'JobBidangUsaha' => $jobBidangUsaha,
            'JobKategoriPekerjaan'=>$jobKategoriPekerjaan,
            'JobStatusPekerjaan'=>$jobStatusPekerjaan,

            'JobTotalPekerja' => $jobTotalPekerja,
            'JobNamaPerusahaan' => $jobNamaPerusahaan,
            'JobPangkat' => $jobPangkat,
            'JobLamaKerjaYY' => $jobLamaKerjaYY,
            'JobLamaKerjaMM' => $jobLamaKerjaMM,

            'JobAlamatKantor'=>$jobAlamatKantor,
            'JobKodePos'=>$jobKodePos,
            'FinanceGajiPerbulan' =>$financeGajiPerbulan,
            'FinanceGajiPertahun'=>$financeGajiPertahun,
            'FinancePendapatanLainPerbulan'=>$financePendapatanLainPerbulan,

            'FinanceJumlahTanggungan' =>$financeJumlahTanggungan,
            'EmergencyNama' =>$emergencyNama,
            'EmergencyHubunganKeluarga' =>$emergencyHubunganKeluarga,
            'EmergencyAlamat' =>$emergencyAlamat,
            'EmergencyKota' => $emergencyKota,
            'EmergencyNoTlp' =>$emergencyNoTlp,
            'EmergencyKota' => $emergencyKota,
            'CardType' => $cardType
        ];

        if ($type == 'update'){
            $informasiLos['appNumber'] = $appNumber;
        }

        $informasiLos = $this->checkInformasiLosKosong($informasiLos);

        return $informasiLos;
    }

    function checkInformasiLosKosong($arrays){
        //cek data kosong, jadiin strip
        foreach ($informasi as $key => $value) {
            if($informasi[$key] == '' || !$informasi[$key]){
               $informasi[$key] = '-'; 
            }
        }

        return $informasi;
    }

    public function create($data){
        $eform = EForm::create($data);
        \Log::info($eform);
    }


}
