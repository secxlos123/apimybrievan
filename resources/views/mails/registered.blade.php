@extends( 'mails.template' )
@section( 'title', 'Selamat Bergabung' )
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
                            <span style="font-weight: bold;">Hai, {!! $mail['name'] !!}!</span>
                            <br>
                            Selamat bergabung di My BRI.
                            <br>
                            Kemudahan anda untuk investasi properti Anda,
                            <br>
                            berikut informasi akun Anda di My BRI:
                            <br>
                            <table width="100%" border="1">
                                <tr>
                                    <td style="padding:5px; ">Email</td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['email'] !!}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px; ">Password</td>
                                    <td style="padding:5px; font-weight: bold;">{!! $mail['password'] !!}</td>
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