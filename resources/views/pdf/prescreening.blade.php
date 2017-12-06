<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>E-PRESCREENING PDF</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <style type="text/css">
            html {
                margin: 0px;
            }
            body {
                background: white;
                margin: 0px;
            }
            @page {
                margin: 0px;
            }
            page[size="A4"] {
              width: 21cm;
              height: 29.7cm;
              display: block;
              margin: 0 auto;
              margin-bottom: 0.5cm;
              box-shadow: 0 0 0.2cm rgba(0,0,0,0.5);
            }
            @media print {
              body, page[size="A4"] {
                margin: 0px;
                box-shadow: 0;
              }
            }
            body {
                font-size: 14px;
            }
            body, h1, h2, h3, h4, h5, h6 {
                font-family: "Open Sans",sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-weight: 600;
                margin-top: 0px;
            }
            .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
                line-height: 1.1;
                color: inherit;
            }
            h1 small, h2 small, h3 small, h4 small, h5 small, h6 small {
                font-size: 10px;
                letter-spacing: 0;
                font-weight: 300;
                font-style: italic;
            }
            p {
                margin: 0px;
            }
            .page-content {
                padding-top: 0.5cm;
                padding-left: 1cm;
                padding-right: 1cm;
                padding-bottom: 1cm;
            }
            td {
                padding: 5px 5px 5px 0px;
            }
            td.label {
                width: 170px;
            }
            .full-width {
                width: 100%;
            }
            .half-width {
                width: 50%;
                float: left;
            }
            .logo-mybri>img {
                width: 130px;
                height: auto;
            }
            .logo-bri {
                text-align: right;
            }
            .logo-bri>img {
                width: 80px;
                height: auto;
            }
            .no-ref {
                display: block;
                border: solid 1px #F7941E;
                text-align: center;
                padding: 10px 10px;
                margin: 20px 200px 10px 200px;
            }
            .title {
                background-color: #eee;
                font-weight: bold;
                text-align: center;
                text-transform: uppercase;
            }
            .title {
                background-color: #eee;
                font-weight: bold;
                text-align: center;
                text-transform: uppercase;
                display: block;
                width: auto;
                margin-top: 20px;
                margin-bottom: 5px;
            }
            .term {
                display: block;
                border: solid 1px #cecece;
                padding: 0px 10px;
                margin-top: 20px;
            }
            .term>ol {
                padding-left: 30px;
            }
            .barcode {
                text-align: center;
                margin-top: 15px;
            }
            .barcode>img {
                height: 120px;
                width: auto;
            }
            .color-orange {
                color: #f7941e;
                margin-bottom: 10px;
                display: inline-block;
            }
            .color-blue {
                color: #00529C;
                margin-bottom: 10px;
                display: inline-block;
            }
            .position-bottom {
                position: absolute;
                bottom: 0;
            }
            .clear {
                clear: both;
            }
            .Hijau {
                color: green;
            }
            .Merah {
                color: red;
            }
            .Kuning {
                color: yellow;
            }
            .break-word {
                max-width: 300px;
                word-wrap: break-word;
            }
            .underline {
                width: 200px;
                margin: 0 auto;
                border-bottom: solid 1px black;
            }
        </style>
    </head>
    <body>
        <div class="page-content">

            <table class="full-width">
                <tbody>
                    <tr>
                        <!-- Gambar logo cuma dummy, pake external link -->
                        <td class="logo-mybri full-width">
                            <div class="color-orange">e-Prescreening</div>
                            <div class="color-blue">BRI</div>
                            <br/>
                            <img src="{{ asset('img/logo-mybri.png') }}">
                        </td>
                        <td class="logo-bri full-width">
                            <img src="{{ asset('img/logo-bri.png') }}">
                        </td>
                    </tr>
                </tbody>
            </table>

            <hr>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="no-ref full-width">No. Reff Aplikasi : {{ $detail->ref_number }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">Hasil Prescreening</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td class="label">NIK</td>
                        <td class="break-word">: {{ $detail->customer->personal['nik'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Calon Nasabah</td>
                        <td class="break-word">: {{ $detail->customer->personal['name'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hasil Prescreening</td>
                        <td class="break-word">: <span class="{{ $detail->prescreening_status }}">{{ $detail->prescreening_status }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan Terkait Risiko</td>
                        <td class="break-word">: {{ $detail->ket_risk }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">Pefindo</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td class="label">Score</td>
                        <td class="break-word">: {{ $detail->pefindo_score }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hasil Pefindo</td>
                        <td class="break-word">: <span class="{{ $detail->pefindo_color }}">{{ $detail->pefindo_color }}</span></td>
                    </tr>
                </tbody>
            </table>

            @if( $detail->dhn_detail )
                @php( $dhn = json_decode((string) $detail->dhn_detail) )

                @if( isset($dhn->responseData) )
                    @php( $dhn = $dhn->responseData[0] )
                    <table class="full-width">
                        <tbody>
                            <tr>
                                <td class="title" colspan="2">Daftar Hitam Nasabah</td>
                            </tr>
                        </tbody>
                    </table>

                    <table>
                        <tbody>
                            <tr>
                                <td class="label">Hasil DHN</td>
                                <td class="break-word">: <span class="{{ $dhn->warna }}">{{ $dhn->warna }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            @endif

            @if( $detail->sicd_detail )
                @php( $sicdData = json_decode((string) $detail->sicd_detail) )

                @if( isset($sicdData->responseData) )
                    @php( $sicdData = $sicdData->responseData )
                    <table class="full-width">
                        <tbody>
                            <tr>
                                <td class="title" colspan="2">SICD</td>
                            </tr>
                        </tbody>
                    </table>

                    @foreach( $sicdData as $key => $sicd )
                        @if( $sicd->bikole == '1' || $sicd->bikole == '-' || $sicd->bikole == '' || $sicd->bikole == null )
                            @php( $warna = 'Hijau' )

                        @elseif( $sicd->bikole == '2' )
                            @php( $warna = 'Kuning' )

                        @else
                            @php( $warna = 'Merah' )

                        @endif

                        <table>
                            <tbody>
                                <tr>
                                    <td class="label">Nama Nasabah</td>
                                    <td class="break-word">: {{ isset($sicd->nama_debitur) ? $sicd->nama_debitur : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">NIK</td>
                                    <td class="break-word">: {{ isset($sicd->no_identitas) ? $sicd->no_identitas : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Tanggal Lahir</td>
                                    <td class="break-word">: {{ isset($sicd->tgl_lahir) ? date('d M Y', strtotime( $sicd->tgl_lahir ) ) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Kolektibilitas</td>
                                    <td class="break-word">: {{ isset($sicd->bikole) ? $sicd->bikole : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Hasil SICD</td>
                                    <td class="break-word">: <span class="{{ $warna }}">{{ $warna }}</span> {!! $detail->selected_sicd == $key ? '<strong>(Dipilih)</strong>' : '' !!}</td>
                                </tr>
                            </tbody>
                        </table>
                        <br/>
                        <br/>
                    @endforeach
                @endif
            @endif

            <br/>

            <div class="barcode">
                <p>{{ date('d M Y', strtotime($detail->created_at)) }}</p>
                <img src="{{ asset('img/qr-code.png') }}">
                <p class="underline">{{ $detail->prescreening_name ? $detail->prescreening_name : '-' }}</p>
                <p>{{ $detail->prescreening_position ? $detail->prescreening_position : '-' }}</p>
            </div>
        </div>

    </body>

</html>