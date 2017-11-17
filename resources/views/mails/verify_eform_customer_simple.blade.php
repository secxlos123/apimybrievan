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
                    <td align="center">Verifikasi Permohonan Kredit KPR-BRI</td>
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
                        <span>Hai, {!! $mail['name'] !!}!</span>
                        <br>
                        Berikut, data permohonan anda:
                        <br>
                        <table align="left" width="550" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>No. Ref Aplikasi : </td>
                                <td>{!! $mail['no_ref'] !!}</td>
                            </tr>
                            <tr>
                                <td>NIK : </td>
                                <td>{!! $mail['nik'] !!}</td>
                            </tr>
                            <tr>
                                <td>Nama Lengkap : </td>
                                <td>{!! $mail['name'] !!}</td>
                            </tr>
                            <tr>
                                <td>Alamat : </td>
                                <td>{!! $mail['address'] !!}</td>
                            </tr>
                            <tr>
                                <td>Kota : </td>
                                <td>{!! $mail['city_id'] !!}</td>
                            </tr>
                            <tr>
                                <td>No. Telepon : </td>
                                <td>{!! $mail['phone'] !!}</td>
                            </tr>
                            <tr>
                                <td>No. Handphone : </td>
                                <td>{!! $mail['mobile_phone'] !!}</td>
                            </tr>
                            <tr>
                                <td>Nama Gadis Ibu Kandung : </td>
                                <td>{!! $mail['mother_name'] !!}</td>
                            </tr>
                            <tr>
                                <td>Tempat Tanggal Lahir : </td>
                                <td>{!! $mail['birth_place_id'].",".$mail['birth_date'] !!}</td>
                            </tr>
                            <tr>
                                <td>Kartu Tanda Penduduk : </td>
                                <td>
                                    <img src="{!! $mail['identity'] !!}" width="200" height="100" alt="ktp" />
                                </td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin : </td>
                                <td>{!! $mail['gender'] !!}</td>
                            </tr>
                            <tr>
                                <td>Status Pernikahan : </td>
                                <td>{!! $mail['status'] !!}</td>
                            </tr>
                            <tr>
                                <td>Status Tempat Tinggal : </td>
                                <td>{!! $mail['address_status'] !!}</td>
                            </tr>
                            <tr>
                                <td>Kewarganegaraan : </td>
                                <td>{!! $mail['citizenship_name']!!}</td>
                            </tr>

                            @if ( $mail['status_id'] == '2' )
                                <tr>
                                    <td align="center">DATA PASANGAN</td>
                                </tr>
                                <tr>
                                    <td>NIK Pasangan : </td>
                                    <td>{!! $mail['couple_nik'] !!}</td>
                                </tr>
                                <tr>
                                    <td>Nama Lengkap : </td>
                                    <td>{!! $mail['couple_name'] !!}</td>
                                </tr>
                                <tr>
                                    <td>KTP Pasangan : </td>
                                    <td>
                                        <img src="{!! $mail['couple_identity'] !!}" width="200" height="100" alt="ktp_pasangan" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tempat Tanggal Lahir : </td>
                                    <td>{!! $mail['couple_birth_place_id'].",".$mail['couple_birth_date']  !!}</td>
                                </tr>
                            @endif
                            
                            <tr>
                                <td align="center">DATA PEKERJAAN</td>
                            </tr>
                            <tr>
                                <td>Bidang Pekerjaan : </td>
                                <td>{!! $mail['job_field_name'] !!}</td>
                            </tr>
                            <tr>
                                <td>Jenis Pekerjaan : </td>
                                <td>{!! $mail['job_type_name'] !!}</td>
                            </tr>
                            <tr>
                                <td>Pekerjaan : </td>
                                <td>{!! $mail['job_name'] !!}</td>
                            </tr>
                            <tr>
                                <td>Nama Perusahaan : </td>
                                <td>{!! $mail['company_name'] !!}</td>
                            </tr>
                            <tr>
                                <td>Jabatan : </td>
                                <td>{!! $mail['position_name'] !!}</td>
                            </tr>
                            <tr>
                                <td>Lama Kerja : </td>
                                <td>{!! $mail['work_duration'] !!}</td>
                            </tr>
                            <tr>
                                <td>Alamat Kantor : </td>
                                <td>{!! $mail['office_address'] !!}</td>
                            </tr>

                            <tr>
                                <td align="center">DATA KEUANGAN</td>
                            </tr>
                            <tr>
                                <td>Gaji/Pendapatan : </td>
                                <td>{!! $mail['salary'] !!}</td>
                            </tr>
                            <tr>
                                <td>Pendapatan Lain : </td>
                                <td>{!! $mail['other_salary'] !!}</td>
                            </tr>
                            <tr>
                                <td>Angsuran Pinjaman : </td>
                                <td>{!! $mail['loan_installment'] !!}</td>
                            </tr>
                            <tr>
                                <td>Jumlah Tanggungan : </td>
                                <td>{!! $mail['dependent_amount'] !!}</td>
                            </tr>

                            @if ($mail['status_id'] == '2' )
                                <tr>
                                    <td align="center">DATA KEUANGAN PASANGAN</td>
                                </tr>
                                <tr>
                                    <td>Gaji/Pendapatan : </td>
                                    <td>{!! $mail['couple_salary'] !!}</td>
                                </tr>
                                <tr>
                                    <td>Pendapatan Lain : </td>
                                    <td>{!! $mail['couple_other_salary'] !!}</td>
                                </tr>
                                <tr>
                                    <td>Angsuran Pinjaman : </td>
                                    <td>{!! $mail['couple_loan_installment'] !!}</td>
                                </tr>
                            @endif

                            <tr>
                                <td align="center">DATA KELUARGA/KERABAT TERDEKAT</td>
                            </tr>
                            <tr>
                                <td>Nama : </td>
                                <td>{!! $mail['emergency_name'] !!}</td>
                            </tr>
                            <tr>
                                <td>No. Handphone : </td>
                                <td>{!! $mail['emergency_contact'] !!}</td>
                            </tr>
                            <tr>
                                <td>Hubungan : </td>
                                <td>{!! $mail['emergency_relation'] !!}</td>
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
                                            Dengan ini Saya/ Kami mengajukan KPR BRI dan mengizinkan pihak Bank BRI untuk menggunakan data tersebut diatas untuk kepentingan permohonan kredit.
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
                        <td align="center">
                            <a href="{!! $mail[ 'url' ] !!}/approve">Setuju</a>
                        </td>
                        <td align="center">
                            <a href="{!! $mail[ 'url' ] !!}/reject">Tidak Setuju</a>
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