@extends( 'mails.template' )
@section( 'title', 'Reset Password' )
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
                            Password My BRI Anda sudah berhasil di reset.
                            <br>
                            Berikut adalah informasi password anda yang baru untuk login:
                            <br>
                            <span style="font-weight: bold;">{!! $mail['password'] !!}</span>
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