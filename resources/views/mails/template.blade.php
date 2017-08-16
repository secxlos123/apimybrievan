<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    <title>@yield( 'title' )</title>
    <style type="text/css">
        .ReadMsgBody { width: 100%; background-color: #ffffff; }
        .ExternalClass { width: 100%; background-color: #ffffff; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        html { width: 100%; }
        body { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }
        table { border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin: 0 auto; }
        table table table { table-layout: auto; }
        img { display: block !important; }
        table td { border-collapse: collapse; }
        .yshortcuts a { border-bottom: none !important; }
        a { color: #f7941e; text-decoration: none; }
        .textbutton a { font-family: 'open sans', arial, sans-serif !important; color: #ffffff !important; }
        .text-link a { color: #3b3b3b !important; }
         @media only screen and (max-width: 640px) {
        body { width: auto !important; }
        table[class="table600"] { width: 450px !important; }
        table[class="table-inner"] { width: 90% !important; }
        table[class="table3-3"] { width: 100% !important; text-align: center !important; }
        }
         @media only screen and (max-width: 479px) {
        body { width: auto !important; }
        table[class="table600"] { width: 290px !important; }
        table[class="table-inner"] { width: 82% !important; }
        table[class="table3-3"] { width: 100% !important; text-align: center !important; }
        }
    </style>
</head>
<body>
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#00529C">
        <tr>
            <td align="center">
                <table class="table600" width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td height="60"></td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table style="border-top:3px solid #F7941E; border-radius:4px;box-shadow: 0px 3px 0px #F7941E;" bgcolor="#FFFFFF" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <table width="550" align="center" class="table-inner" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td height="15"></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table class="table3-3" width="50" border="0" align="left" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td align="center" style="line-height:0px;">
                                                                <img style="display:block; line-height:0px; font-size:0px; border:0px;" src="{!! $message->embed(public_path( 'img/logo.png' )) !!}" width="40" height="40" alt="logo" />
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table width="1" height="15" border="0" cellpadding="0" cellspacing="0" align="left">
                                                        <tr>
                                                            <td height="15" style="font-size: 0;line-height: 0;border-collapse: collapse;">
                                                                <p style="padding-left: 24px;">&nbsp;</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table align="right" class="table3-3" width="160" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">
                                                                <span style="font-weight: bold; color:#00529c;">Tanggal</span>: {!! date('d-m-Y') !!}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="15"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="25"></td>
                    </tr>
                    <tr>
                        <td align="center">
                            @yield( 'content' )
                        </td>
                    </tr>
                    <tr>
                        <td height="20"></td>
                    </tr>
                    <tr>
                        <td>
                            <table align="left" class="table3-3" width="390" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="font-family: 'Open Sans', Arial, sans-serif; font-size:12px; color:#ffffff; line-height:30px;">
                                        © 2017
                                        <a href="#" style="color: #f7941e; text-decoration: none;">Bank Rakyat Indonesia</a>
                                        . All Rights Reserved.
                                    </td>
                                </tr>
                            </table>
                            <table width="1" height="25" border="0" cellpadding="0" cellspacing="0" align="left">
                                <tr>
                                    <td height="25" style="font-size: 0;line-height: 0;border-collapse: collapse;">
                                        <p style="padding-left: 24px;">&nbsp;</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="60"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>