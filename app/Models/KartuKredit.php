<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\EForm;
use App\Models\CustomerDetail;
use App\Http\Requests\API\v1\EFormRequest;



class KartuKredit extends Model
{
	//=================INFO=================
	//jenis nasabah -> diisi "nasabah" atau "debitur" 
	//1. nasabah = nasabah yang tidak memiliki pinjaman
	//2. debitur = nasabah yang memili pinjamna
	//======================================
	//pilihan kartu -> kartu yang user ajukan. diisi "world access","easy card", "platinum","touch"

    protected $table = 'kartu_kredit_details';

    protected $fillable = [
    	'nik','hp','email','user_id','eform_id',
    	'jenis_kelamin','nama','tempat_lahir','telephone',
    	'pendidikan','pekerjaan','tiering_gaji',
    	'agama','jenis_nasabah','pilihan_kartu',
    	'penghasilan_perbulan','jumlah_penerbit_kartu',
    	'memiliki_kk_bank_lain','limit_tertinggi'
    ];

    protected $hidden = [
        'id','updated_at'
    ];

    public function convertToAddDataLosFormat(Request $req,$type){
        

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
                $subBidangUsaha = $req['subBidangUsaha'];
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
            $informasiLos['subBidangUsaha'] = $subBidangUsaha;
        }

        $informasiLos = $this->checkInformasiLosKosong($informasiLos);

        return $informasiLos;
    }

    function overwriteEmptyRecord($arrays){
        //cek data kosong, jadiin strip
        foreach ($arrays as $key => $value) {
            if($arrays[$key] == '0'){
                $arrays[$key] = 0;
            }else if($arrays[$key] == '' || !$arrays[$key] ){
               $arrays[$key] = '-'; 
            }
            
        }

        return $arrays;
    }

    public function createEform($req){
        $ef['ao_id'] = $req['ao_id'];
        $ef['branch_id'] = $req['branch_id'];
        $ef['address'] = $req['address'];
        $ef['longitude'] = $req['longitude'];
        $ef['latitude'] = $req['latitude'];
        $ef['appointment_date'] = $req['appointment_date'];
        $ef['nik'] = $req['nik'];
        $ef['product_type'] = $req['product_type']; 

        $ef = $this->overwriteEmptyRecord($ef);
        $eform = EForm::create($ef);
        \Log::info($eform);

        return $eform;
    }

    public function createKartuKreditDetails($req){
        //get user id
        $nik= $req['nik'];
        $e = EForm::where('nik',$nik)->first();
        $userId = $e->user_id;
        \Log::info('======== create kk details ========');
        \Log::info($e);

        $data['user_id'] = $userId;
        $data['penghasilan_perbulan'] = $req['penghasilan_diatas_10_juta'];

        if ($req['jenis_nasabah'] == 'debitur'){
            $data['image_npwp'] = $req['NPWP_nasabah'];
            $data['image_ktp'] = $req['KTP'];
        }else{
            $data['image_npwp'] = $req['NPWP_nasabah'];
            $data['image_ktp'] = $req['KTP'];
            $data['image_slip_gaji'] = $req['SLIP_GAJI'];
            $data['image_nametag'] = $req['NAME_TAG'];
            $data['image_kartu_bank_lain'] = $req['LIMIT_KARTU'];
        }



        $data['hp'] = $req['hp'];
        $data['email'] = $req['email'];
        $data['jenis_kelamin'] = $req['jenis_kelamin'];
        $data['nama'] = $req['nama'];
        $data['tempat_lahir'] = $req['tempat_lahir'];
        $data['telephone'] = $req['telephone'];
        $data['pendidikan'] = $req['pendidikan'];
        $data['pekerjaan'] = $req['pekerjaan'];
        $data['tiering_gaji'] = $req['tiering_gaji'];
        $data['agama'] = $req['agama'];
\Log::info($data);
        $kkDetails = KartuKredit::create($data);
        \Log::info($kkDetails);
        return $kkDetails;

    }

    public function eformStatusFail(){
        //update table eform field status_eform jadi 'fail-dedup' kalau gagal di dedup
    }


}
