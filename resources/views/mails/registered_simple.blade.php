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
                    <td align="center">Selamat Bergabung</td>
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
                        @if ($mail['role'] == 5)
                        <p align="justify">Terima kasih telah mengajukan aplikasi kredit di Bank BRI, pada Tanggal {{ date('d F Y', strtotime($mail['created_at'])) }} Pukul {{ date('H:i', strtotime($mail['created_at'])) }}. Data yang telah diinput telah masuk ke sistem kami.</p>
                        <br>
                        <p align="justify">Untuk Keamanan Anda, kami telah membuatkan akun dan kata sandi MyBRI untuk Anda yang dapat digunakan untuk melihat status aplikasi Anda. Untuk mengakses MyBRI silahkan Anda mengunduh aplikasinya di playstore/google play atau Anda dapat mengakses di browser dengan alamat mybri.bri.co.id,</p>
                        <br>
                        @else
                        <p align="justify">Terima kasih telah menjadi mitra kerja sama Bank BRI.
                        Kami telah membuatkan akun dan kata sandi MyBRI untuk Anda mewakili atas nama Perusahaan Anda yang dapat digunakan untuk melakukan aktivitas di apliksi MyBRI sesuai dengan yang ada di Perjanjian Kerja Sama. Aplikasi MyBRI  dapat diakses dengan alamat mybri.bri.co.id</p>
                        <br>
                        @endif
                        berikut informasi akun Anda di My BRI :
                        <br>
                        <table width="100%" border="1">
                            <tr>
                                <td>Email : </td>
                                <td>{!! $mail['email'] !!}</td>
                            </tr>
                            <tr>
                                <td>Password : </td>
                                <td>{!! $mail['password'] !!}</td>
                            </tr>
                        </table>
                        <br>
                        *Harap ubah kata sandi Anda setelah mengakses.
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
            <img align="center" width="500" src="https://mybri.stagingapps.net/assets/images/logo/footer.png">
        </td>
    </tr>
</table>