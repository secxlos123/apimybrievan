<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\EForm;
use App\Models\CustomerDetail;
use App\Http\Requests\API\v1\EFormRequest;
use File;



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
    	'hp','email','user_id','eform_id','nik','alamat',
    	'jenis_kelamin','nama','tempat_lahir','telephone',
    	'pendidikan','pekerjaan','tiering_gaji',
    	'agama','jenis_nasabah','pilihan_kartu',
    	'penghasilan_perbulan','jumlah_penerbit_kartu',
    	'memiliki_kk_bank_lain','range_limit','nama_ibu_kandung',
        'status_pernikahan','image_npwp','image_ktp','image_slip_gaji',
        'image_nametag','image_kartu_bank_lain','pn','tanggal_lahir',
        'los_score','analyzed_status'
    ];

    protected $hidden = [
        'id','updated_at'
    ];

    public $timestamps = false;

    function globalImageCheck( $filename ){
        $path =  'img/noimage.jpg';
        $id = substr ($filename,0,14);
        if( ! empty( $filename ) ) {
            $image = 'uploads/' . $id . '/' . $filename;
            if( File::exists( public_path( $image ) ) ) {
                $path = $image;
            }
        }

        return url( $path );
    }

    public function getImageNpwpAttribute( $value ){
        return $this->globalImageCheck( $value );
    }

    public function getImageKtpAttribute( $value ){
        return $this->globalImageCheck( $value );
    }

    public function getImageSlipGajiAttribute( $value ){
        return $this->globalImageCheck( $value );
    }

    public function getImageNametagAttribute( $value ){
        return $this->globalImageCheck( $value );
    }

    public function getImageKartuBankLainAttribute( $value ){
        return $this->globalImageCheck( $value );
    }


    public function convertToAddDataLosFormat($req,$type){

        try{
                
            $personalName = $req['PersonalName'];
            $personalNIK = $req['PersonalNIK'];
            $personalTempatLahir = $req['PersonalTempatLahir'];
            $personalTanggalLahir = $req['PersonalTanggalLahir'];
            $personalAlamatDomisili = $req['PersonalAlamatDomisili'];

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
            
            $emergencyNama = $req['EmergencyNama'];
            $emergencyHubunganKeluarga = $req['EmergencyHubunganKeluarga'];
            $emergencyAlamat = $req['EmergencyAlamat'];
            $emergencyKota = $req['EmergencyKota'];

            $emergencyNoTlp = $req['EmergencyNoTlp'];
            $emergencyKota = $req['EmergencyKota'];
            

            if ($type == 'update'){
                $appNumber = $req['appNumber'];
                $subBidangUsaha = $req['subBidangUsaha'];

                $personalAlamatDomisili2 = $req['PersonalAlamatDomisili2'];
                $personalAlamatDomisili3 = $req['PersonalAlamatDomisili3'];
                $camat = $req['Camat'];
                $lurah = $req['Lurah'];
                $rt = $req['Rt'];

                $rw = $req['Rw'];

                $jobAlamatKantor2 = $req['JobAlamatKantor2'];
                $jobAlamatKantor3 = $req['JobAlamatKantor3'];
                $jobCamat = $req['JobCamat'];
                $jobLurah = $req['JobLurah'];
                $jobRt = $req['JobRt'];
                $jobRw = $req['JobRw'];

                $jobBidangUsaha = $req['JobBidangUsaha'];
                $jobKategoriPekerjaan = $req['JobKategoriPekerjaan'];
                $jobStatusPekerjaan = $req['JobStatusPekerjaan'];
                $jobTotalPekerja =  $req['JobTotalPekerja'];
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

                $personalKota = $req['PersonalKota'];

            }else{
                $cardType = $req['CardType'];
                $jobBidangUsaha = '0';
                $jobKategoriPekerjaan = '0';
                $jobStatusPekerjaan = '0';
                $jobTotalPekerja = '0';
                $jobNamaPerusahaan = '0';
                $jobPangkat = '0';
                $jobLamaKerjaYY = '0';
                $jobLamaKerjaMM = '0';
                $jobAlamatKantor = '0';
                $jobKodePos = '0';
                $financeGajiPerbulan = '0';
                $financeGajiPertahun = '0';
                $financePendapatanLainPerbulan = '0';
                $financeJumlahTanggungan = '0';
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


            
            'EmergencyNama' =>$emergencyNama,
            'EmergencyHubunganKeluarga' =>$emergencyHubunganKeluarga,
            'EmergencyAlamat' =>$emergencyAlamat,
            'EmergencyKota' => $emergencyKota,
            'EmergencyNoTlp' =>$emergencyNoTlp,
            'EmergencyKota' => $emergencyKota,
            
        ];

        $informasiLos['JobBidangUsaha'] = $jobBidangUsaha;
        $informasiLos['JobKategoriPekerjaan'] = $jobKategoriPekerjaan;
        $informasiLos['JobStatusPekerjaan'] = $jobStatusPekerjaan;
        $informasiLos['JobTotalPekerja'] = $jobTotalPekerja ;
        $informasiLos['JobNamaPerusahaan'] = $jobNamaPerusahaan ;
        $informasiLos['JobPangkat'] = $jobPangkat ;
        $informasiLos['JobLamaKerjaYY'] = $jobLamaKerjaYY ;
        $informasiLos['JobLamaKerjaMM'] = $jobLamaKerjaMM ;
        $informasiLos['JobAlamatKantor'] = $jobAlamatKantor;
        $informasiLos['JobKodePos'] = $jobKodePos;
        $informasiLos['FinanceGajiPerbulan'] = $financeGajiPerbulan;
        $informasiLos['FinanceGajiPertahun'] = $financeGajiPertahun;
        $informasiLos['FinancePendapatanLainPerbulan'] = $financePendapatanLainPerbulan;
        $informasiLos['FinanceJumlahTanggungan'] = $financeJumlahTanggungan;

        if ($type == 'update'){
            $informasiLos['appNumber'] = $appNumber;
            $informasiLos['subBidangUsaha'] = $subBidangUsaha;

            $informasiLos['PersonalAlamatDomisili2'] = $personalAlamatDomisili2;
            $informasiLos['PersonalAlamatDomisili3'] = $personalAlamatDomisili3;
            $informasiLos['Camat'] =$camat;
            $informasiLos['Lurah']= $lurah;
            $informasiLos['Rt'] = $rt;
            $informasiLos['Rw'] = $rw;

            $informasiLos['JobAlamatKantor2'] = $jobAlamatKantor2;
            $informasiLos['JobAlamatKantor3'] = $jobAlamatKantor3;
            $informasiLos['JobCamat'] = $jobCamat;
            $informasiLos['JobLurah'] = $jobLurah;
            $informasiLos['JobRt'] = $jobRt;
            $informasiLos['JobRw'] = $jobRw;

            $informasiLos['PersonalKota'] = $personalKota;
            
           
           
        }else{
            $informasiLos['CardType'] = $cardType;
        }
        \Log::info('========update=======');
        \Log::info($informasiLos);

        $informasiLos = $this->overwriteEmptyRecord($informasiLos);

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
        $ef['response_status'] = 'unverified'; 

        $ef['ao_name'] = $req['ao_name'];

        $ef = $this->overwriteEmptyRecord($ef);
        $eform = EForm::create($ef);
        \Log::info($eform);

        return $eform;
    }

    public function createKartuKreditDetails($req){
         \Log::info($req);
        //get user id
        $nik= $req['nik'];
        $e = EForm::where('nik',$nik)->first();
        $userId = $e->user_id;

        $data['user_id'] = $userId;
        

        if ($req['jenis_nasabah'] == 'debitur'){
            $data['image_npwp'] = $req['NPWP'];
            $data['image_ktp'] = $req['KTP'];
            $data['image_slip_gaji'] = $req['SLIP_GAJI'];
            $data['image_nametag'] = '-';
            $data['image_kartu_bank_lain'] = '-';
        }else{
            $data['image_npwp'] = $req['NPWP'];
            $data['image_ktp'] = $req['KTP'];
            $data['image_slip_gaji'] = $req['SLIP_GAJI'];
            $data['image_nametag'] = $req['NAME_TAG'];
            $data['image_kartu_bank_lain'] = $req['KARTU_BANK_LAIN'];
        }

        if ($req['memiliki_kk_bank_lain'] == 'true'){
            $data['memiliki_kk_bank_lain'] = true;
        }else{
            $data['memiliki_kk_bank_lain'] = false;
        }

        $data['penghasilan_perbulan'] = $req['penghasilan_diatas_10_juta'];
        $data['jumlah_penerbit_kartu'] = $req['jumlah_penerbit_kartu'];
        
        $data['range_limit'] = $req['range_kartu'];
        $data['jenis_nasabah'] = $req['jenis_nasabah'];
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
        $data['pilihan_kartu'] = $req['pilihan_kartu'];
        $data['nama_ibu_kandung'] = $req['nama_ibu_kandung'];
        $data['status_pernikahan'] = $req['status'];
        $data['eform_id'] = $req['eform_id'];
        $data['pn'] = $req['ao_id'];
        $data['tanggal_lahir'] = $req['ttl'];


        $kkDetails = KartuKredit::create($data);
        
        $kkDetails['range_kartu'] = $kkDetails['range_limit'];
        \Log::info('=======kk details=========');
        \Log::info($kkDetails);
        return $kkDetails;

    }

    public function createApprovedRequirements($req){
        $data['msg'] = $req['msg'];
        $data['apRegno'] = $req['apRegno'];
        $data['by'] = $req['by'];
        $data['userId'] = $req['userId'];
        $data['cpAprLimit'] = $req['cpAprLimit'];
        $data['potCode'] = $req['potCode'];
        $data['wvCode']= $req['wvCode'];
        $data['apBillCycle']= $req['apBillCycle'];

        return $data;
    }

    public function createRejectedRequirements($req){
        $data['apRegno'] = $req['apRegno'];
        $data['userId'] = $req['userId'];
        $data['rjCode'] = $req['rjCode'];

        return $data;
    }

}
