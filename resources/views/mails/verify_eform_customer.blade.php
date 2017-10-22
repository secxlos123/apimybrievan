@extends( 'mails.template' )
@section( 'title', 'Verification E Form Customer' )
@section( 'content' )
    <table align="center" bgcolor="#FFFFFF" style="box-shadow: 0px 3px 0px #bdc3c7; border-radius:4px;" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table align="center" class="table-inner" width="500" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="50"></td>
                    </tr>
                    <tr>
                        <td align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:30px; color:#3b3b3b; font-weight: bold; ">@yield( 'title' )</td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table width="25" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td height="20" style="border-bottom:2px solid #00529c;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20"></td>
                    </tr>
                    <tr>
                        <td align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">
                            <span style="font-weight: bold;">Hai, {!! $mail[ 'name' ] !!}!</span>
                            <br>
                            Berikut E Form yang sudah kami verifikasi.
                            <br>
                            <table align="left" class="table-inner" width="550" border="1" cellspacing="0" cellpadding="0" style="font-family: 'Open Sans', Arial, sans-serif;">
                            	
                                <tr>
                                    <td style="padding:5px; ">NIK : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['nik'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Nama Lengkap : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['name'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Alamat : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['address'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">No. Telepon : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['phone'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">No. Handphone : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['mobile_phone'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Nama Gadis Ibu Kandung : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['mother_name'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Tempat Tanggal Lahir : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['birth_place_id'].",".$mail['birth_date']  !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Kartu Tanda Penduduk : </td>
                                    <td style="padding:5px; font-weight: bold;"><img style="display:block; line-height:0px; font-size:0px; border:0px;" src="{!! $message->embed(public_path( $mail['identity'] )) !!}" width="200" height="100" alt="ktp" /></td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Jenis Kelamin : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['gender'] !!}</td>
                                </tr>
                                
                                <tr>
                                    <td style="padding:5px; ">Status Pernikahan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['status'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Status Tempat Tinggal : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['address_status'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Kewarganegaraan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['citizenship_id'] !!}</td>
                                </tr>

                                @if ($mail['status'] == 2 )

                                <tr>
                                    <td align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">DATA PASANGAN</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">NIK Pasangan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['couple_nik'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Nama Lengkap : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['couple_name'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">KTP Pasangan : </td>
                                    <td style="padding:5px; font-weight: bold;"><img style="display:block; line-height:0px; font-size:0px; border:0px;" src="{!! $message->embed(public_path( $mail['couple_identity'] )) !!}" width="200" height="100" alt="ktp_pasangan" /></td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Tempat Tanggal Lahir : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['couple_birth_place_id'].",".$mail['couple_birth_date']  !!}</td>
                                </tr>

                                @endif

                                

                                <tr>
                                    <td align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">DATA PEKERJAAN</td>
                                </tr>

                                <tr>
                                    <td style="padding:5px; ">Bidang Pekerjaan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['job_field_id'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Jenis Pekerjaan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['job_type_id'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Pekerjaan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['job_id'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Nama Perusahaan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['company_name'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Jabatan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['position'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Lama Kerja : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['work_duration'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Alamat Kantor : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['office_address'] !!}</td>
                                </tr>

                                <tr>
                                    <td align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">DATA KEUANGAN</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Gaji/Pendapatan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['salary'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Pendapatan Lain : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['other_salary'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Angsuran Pinjaman : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['loan_installment'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Jumlah Tanggungan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['dependent_amount'] !!}</td>
                                </tr>

                                @if ($mail['status'] == 2 )
                                
                                <tr>
                                    <td align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">DATA KEUANGAN PASANGAN</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Gaji/Pendapatan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['couple_salary'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Pendapatan Lain : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['couple_other_salary'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Angsuran Pinjaman : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['couple_loan_installment'] !!}</td>
                                </tr>

                                @endif

                                <tr>
                                    <td align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">DATA KELUARGA/KERABAT TERDEKAT</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Nama : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['emergency_name'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">No. Handphone : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['emergency_contact'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Hubungan : </td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['emergency_relation'] !!}</td>
                                </tr>

                            </table>

                            <br>
                            Silahkan response dengan menekan salah satu tombol yang ada di bawah ini.
                            <br>
                            <table align="center" class="table-inner" width="550" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td height="30" colspan="2"></td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table class="textbutton" align="center" bgcolor="#F7941E" border="0" cellspacing="0" cellpadding="0" style=" border-radius:30px; box-shadow: 0px 2px 0px #dedfdf;">
                                            <tr>
                                                <td height="55" align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:16px; color:#7f8c8d; line-height:30px; font-weight: bold;padding-left: 25px;padding-right: 25px;">
                                                    <a href="{!! $mail[ 'url' ] !!}/approve">Setuju</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td align="center">
                                        <table class="textbutton" align="center" bgcolor="#00529C" border="0" cellspacing="0" cellpadding="0" style=" border-radius:30px; box-shadow: 0px 2px 0px #dedfdf;">
                                            <tr>
                                                <td height="55" align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:16px; color:#7f8c8d; line-height:30px; font-weight: bold;padding-left: 25px;padding-right: 25px;">
                                                    <a href="{!! $mail[ 'url' ] !!}/reject">Tidak Setuju</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="30" colspan="2"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="40"></td>
        </tr>
    </table>
@endsection()