<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EForm;
use App\Models\KartuKredit;
use GuzzleHttp\Client;

class KreditEmailGenerator extends Model{

	public function sendEmailVerification($data,$apregno,$host){

      //sementara panggil eform liat nik
      $selectEformid = KartuKredit::select('eform_id')->where('eform_id',$data['eform_id'])->first();
      $eformid = $selectEformid['eform_id'];
      $nik = EForm::where('id',$eformid)->first();
      $data['nik'] = $nik['nik'];

      if ($data['jenis_kelamin'] == '1'){
         $data['jenis_kelamin'] = 'Laki-laki';
      }else if ($data['jenis_kelamin'] == '2'){
         $data['jenis_kelamin'] = 'Perempuan';
      }

      if ($data['status_pernikahan'] == '1'){
         $data['status_pernikahan'] = 'Menikah';
      }else if ($data['status_pernikahan'] == '2'){
         $data['status_pernikahan'] = 'Belum Menikah';
      }

		return '<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
   </head>
   <body>
      <table align="center" bgcolor="#fafafa" width="100%" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <td height="20px"></td>
         </tr>
         <tr>
            <td align="center">
               <table align="center" class="table-inner" width="500px" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                     <td align="center">
                        <img align="center" width="200px" src="https://mybri.stagingapps.net/assets/images/logo/Logo-Website.png">
                     </td>
                  </tr>
                  <tr>
                     <td height="20px"></td>
                  </tr>
                  <tr>
                     <td align="center">Verifikasi Permohonan Kartu Kredit BRI</td>
                  </tr>
                  <tr>
                     <td height="20px"></td>
                  </tr>
                  <tr>
                     <td bgcolor="#F7941E" height="5" align="center"></td>
                  </tr>
                  <tr>
                     <td height="20px"></td>
                  </tr>
                  <tr>
                     <td align="center">
                        <span>Hai,'.$data['nama'].'</span>
                        <br>
                        Berikut data permohonan kartu kredit anda:
                        <br>
                        <table align="left" width="550" border="1" cellspacing="0" cellpadding="0">
                           <tr>
                              <td>No. Ref Aplikasi : </td>
                              <td>'.$data['appregno'].'</td>
                           </tr>
                           <tr>
                              <td>NIK : </td>
                              <td>'.$data['nik'].'</td>
                           </tr>
                           <tr>
                              <td>Nama Lengkap : </td>
                              <td>'. $data['nama'].'</td>
                           </tr>
                           <tr>
                              <td>Alamat : </td>
                              <td>'.$data['alamat_lengkap'].'</td>
                           </tr>
                           <tr>
                              <td>No. Telepon : </td>
                              <td>'.$data['telephone'].'</td>
                           </tr>
                           <tr>
                              <td>No. Handphone : </td>
                              <td>'.$data['hp'].'</td>
                           </tr>
                           <tr>
                              <td>Nama Gadis Ibu Kandung : </td>
                              <td>'.$data['nama_ibu_kandung'].'</td>
                           </tr>
                           <tr>
                              <td>Tempat Tanggal Lahir : </td>
                              <td>'.$data['tempat_lahir'].",".$data['tanggal_lahir'].'</td>
                           </tr>
                           <tr>
                              <td>Kartu Tanda Penduduk : </td>
                              <td>
                                 <img src="'.$data['image_ktp'].'" width="200" height="100" alt="ktp" />
                              </td>
                           </tr>
                           <tr>
                              <td>Jenis Kelamin : </td>
                              <td>'.$data['jenis_kelamin'].'</td>
                           </tr>
                           <tr>
                              <td>Status Pernikahan : </td>
                              <td>'.$data['status_pernikahan'].'</td>
                           </tr>
                           <tr>
                              <td align="center">DATA KEUANGAN</td>
                           </tr>
                           <tr>
                              <td>Tiering Pendapatan : </td>
                              <td>Rp. '.$data['tiering_gaji'].'</td>
                           </tr>
                           <tr>
                              <td align="center">DATA PENGAJUAN</td>
                           </tr>
                           <tr>
                              <td>Jenis Kartu Kredit : </td>
                              <td>'.$data['jenis_kartu'].'</td>
                              
                           </tr>
                           
                          
                           <tr>
                              <td height="20px"></td>
                           </tr>
                        </table>
                        <br>
                        <table align="left" width="550" border="1" cellspacing="0" cellpadding="0">
                           <tr>
                              <td>
                                 <ol>
                                    <li>
                                       Dengan ini Saya/ Kami mengajukan Kartu Kredit BRI dan mengizinkan pihak Bank BRI untuk menggunakan data tersebut diatas untuk kepentingan permohonan kredit.
                                    </li>
                                    <li>
                                       Saya/ Kami menyatakan bahwa semua informasi yang diberikan dalam formulir aplikasi ini adalah sesuai keadaan yang sebenarnya.
                                    </li>
                                    <li>
                                       Saya / Kami memberikan kuasa kepada Bank BRI / pihak yang ditunjuk oleh Bank BRI untuk memeriksa atau mencari informasi lebih jauh dari sumber layak manapun, dan akan memberikan informasi terbaru apabila terdapat perubahan data sehubungan dengan permohonan ini.
                                    </li>
                                    <li>
                                       Bank BRI mempunyai hak untuk menolak untuk menerima permohonan saya/ kami tanpa memberitahukan alasannya.
                                    </li>
                                    <li>
                                       Sehubungan dengan disetujuinya verifikasi permohonan kredit ini, saya/ kami menyatakan akan mentaati segala persyaratan ketentuan yang berlaku di Bank BRI.
                                    </li>
                                 </ol>
                              </td>
                           </tr>
                        </table>
                        <br>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td height="20px"></td>
         </tr>
         <tr>
            <td align="center" bgcolor="#fafafa">
               <table class="textbutton" align="center" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                     <tr>
                        <td>
                           <p>
                              <b>Kami telah mengirimkan kode verifikasi ke nomor HP anda,
                              <br> silakan masukkan kode verifikasi tersebut pada form dibawah
                              </b>
                           </p>
                        </td>
                     </tr>
                     <tr>
                        <td height="20px"></td>
                     </tr>
                     <tr>
                        <td>
                           <form action="'.$host.'">
                              <label for="email">Verification Code:</label>
                              <input type="text" class="form-control" id="email" placeholder="Enter Verification Code" name="code">
                              <input type="hidden" name="apregno" value="'.$apregno.'">
                              <button type="submit" class="btn btn-default">Submit</button>
                           </form>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
         <tr>
            <td align="center" bgcolor="#fafafa">
               <img align="center" width="500" src="https://mybri.stagingapps.net/assets/images/logo/footer.png">
            </td>
         </tr>
      </table>
   </body>
</html>';

	}

   public function convertToFinishVerificationEmailFormat($data,$apregno,$qrcode){

      $selectEformid = KartuKredit::select('eform_id')->where('eform_id',$data['eform_id'])->first();
      $eformid = $selectEformid['eform_id'];
      $nik = EForm::where('id',$eformid)->first();
      $data['nik'] = $nik['nik'];

      if ($data['jenis_kelamin'] == '1'){
         $data['jenis_kelamin'] = 'Laki-laki';
      }else if ($data['jenis_kelamin'] == '2'){
         $data['jenis_kelamin'] = 'Perempuan';
      }

      if ($data['status_pernikahan'] == '1'){
         $data['status_pernikahan'] = 'Menikah';
      }else if ($data['status_pernikahan'] == '2'){
         $data['status_pernikahan'] = 'Belum Menikah';
      }

      $data['tanggal'] = ERR;

      return '<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
   </head>
   <body>
      <table align="center" bgcolor="#fafafa" width="100%" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <td height="20px"></td>
         </tr>
         <tr>
            <td align="center">
               <table align="center" class="table-inner" width="500px" border="00" cellspacing="0" cellpadding="0">
                  <tr>
                     <td align="center">
                        <img align="center" width="200px" src="https://mybri.stagingapps.net/assets/images/logo/Logo-Website.png">
                     </td>
                  </tr>
                  <tr>
                     <td height="20px"></td>1
                  </tr>
                  <tr>
                     <td align="center">Verifikasi Permohonan Kartu Kredit BRI</td>
                  </tr>
                  <tr>
                     <td height="20px"></td>
                  </tr>
                  <tr>
                     <td bgcolor="#F7941E" height="5" align="center"></td>
                  </tr>
                  <tr>
                     <td height="20px"></td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table align="left" width="550" border="0" cellspacing="8" cellpadding="0">
                            <tr style="background:#17528F;color:white;">
                              <td align="center"  height="22" colspan="2">DATA NASABAH</td>
                              
                           </tr>
                           <tr>
                              <td>No. Ref Aplikasi : </td>
                              <td>'.$data['appregno'].'</td>
                           </tr>
                           <tr>
                              <td>NIK : </td>
                              <td>'.$data['nik'].'</td>
                           </tr>
                           <tr>
                              <td>Nama Lengkap : </td>
                              <td>'. $data['nama'].'</td>
                           </tr>
                           <tr>
                              <td>Alamat : </td>
                              <td>'.$data['alamat_lengkap'].'</td>
                           </tr>
                           <tr>
                              <td>No. Telepon : </td>
                              <td>'.$data['telephone'].'</td>
                           </tr>
                           <tr>
                              <td>No. Handphone : </td>
                              <td>'.$data['hp'].'</td>
                           </tr>
                           <tr>
                              <td>Nama Gadis Ibu Kandung : </td>
                              <td>'.$data['nama_ibu_kandung'].'</td>
                           </tr>
                           <tr>
                              <td>Tempat Tanggal Lahir : </td>
                              <td>'.$data['tempat_lahir'].",".$data['tanggal_lahir'].'</td>
                           </tr>
                           <tr>
                              <td>Kartu Tanda Penduduk : </td>
                              <td>
                                 <img src="'.$data['image_ktp'].'" width="200" height="100" alt="ktp" />
                              </td>
                           </tr>
                           <tr>
                              <td>Jenis Kelamin : </td>
                              <td>'.$data['jenis_kelamin'].'</td>
                           </tr>
                           <tr>
                              <td>Status Pernikahan : </td>
                              <td>'.$data['status_pernikahan'].'</td>
                           </tr>
                           <tr style="background:#17528F;color:white;">
                              <td align="center"  height="22" colspan="2">DATA KEUANGAN</td>
                              
                           </tr>
                           <tr>
                              <td>Tiering Pendapatan : </td>
                              <td>Rp. '.$data['tiering_gaji'].'</td>
                           </tr>
                            <tr style="background:#17528F;color:white;">
                             <td align="center"  height="22" colspan="2">DATA PENGAJUAN</td>
                           </tr>
                           <tr>
                              <td>Jenis Kartu Kredit : </td>
                              <td>'.$data['jenis_kartu'].'</td>
                              
                           </tr>
                           
                          
                           <tr>
                              <td height="20px"></td>
                           </tr>
                        </table>
                        <br>
            </td>
         </tr>
         <tr>
            <td height="8px"></td>
         </tr>
       
         <tr>
             
             <td align="right" >
                 <p align="right" style="margin-right:18px;">'.$data['tanggal'].'</p>
                <img src="'.$data['qrcode'].'" width="150" height="150" alt="qrcode"  hspace="8" style="margin-top:-9px;"/>
                <p align="right" style="margin-right:18px;margin-top:2px">'.$data['nama'].'</p>
             </td>
         </tr>
        
         <tr>
            <td align="center" bgcolor="#fafafa">
               <img align="center" width="100%" src="https://mybri.stagingapps.net/assets/images/logo/footer.png">
            </td>
         </tr>
      </table>
   </body>
</html>';
   }
}

