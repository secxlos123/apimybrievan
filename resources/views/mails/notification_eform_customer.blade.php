@extends( 'mails.template' )
@section( 'title', 'Notification E Form Customer' )
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
                            Terima Kasih telah melakukan konfirmasi E Form.
                            <br/>
                            Anda dengan memilih <strong>{!! $mail[ 'status' ] !!}</strong>.
                            <br>
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