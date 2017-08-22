@extends( 'mails.template' )
@section( 'title', 'Aktivasi Akun Anda' )
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
                            <span style="font-weight: bold;">Hai, {!! $mail['email'] !!}!</span>
                            <br>
                            Terima kasih telah mendaftarkan akun Anda di My BRI.
                            <br>
                            Untuk langkah selanjutnya, silahkan meng-klik tombol di bawah ini untuk mengaktivasi akun Anda.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="40"></td>
        </tr>
        <tr>
            <td align="center" bgcolor="#ecf0f1">
                <table align="center" class="table-inner" width="550" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="30"></td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table class="textbutton" align="center" bgcolor="#F7941E" border="0" cellspacing="0" cellpadding="0" style=" border-radius:30px; box-shadow: 0px 2px 0px #dedfdf;">
                                <tr>
                                    <td height="55" align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:16px; color:#7f8c8d; line-height:30px; font-weight: bold;padding-left: 25px;padding-right: 25px;">
                                        <a href="{!! $mail[ 'url' ] !!}">Aktivasi Akun</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="30"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection()