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
                            		<td>data</td>
                            		<td>value</td>
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