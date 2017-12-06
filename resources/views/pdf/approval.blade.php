<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>E-LKN PDF</title>
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
              background: white;
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
            .break-word {
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
                            <span class="color-orange">e-LKN</span>
                            <span class="color-blue">BRI</span>
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
                        <td class="title" colspan="2">Data Kunjungan</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td>Nama RM</td>
                        <td class="break-word">: {{ $detail->ao_name }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Kunjungan</td>
                        <td class="break-word">: {{ $detail->address }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Kunjungan</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($detail->appointment_date)) }}</td>
                    </tr>
                    <tr>
                        <td>Nama Calon Debitur</td>
                        <td class="break-word">: {{ $detail->customer->personal['name'] }}</td>
                    </tr>
                    <tr>
                        <td>Tujuan Kunjungan</td>
                        <td class="break-word">: {{ $detail->visit_report['purpose_of_visit'] }}</td>
                    </tr>
                    <tr>
                        <td>Jumlah Permohonan</td>
                        <td class="break-word">: Rp. {{ number_format(round($detail->nominal), 0, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Permohonan</td>
                        <td class="break-word">: {{ strtoupper($detail->product_type) }}</td>
                    </tr>
                    <tr>
                        <td>Nomor NPWP</td>
                        <td class="break-word">: {{ $detail->visit_report['npwp_number_masking'] }}</td>
                    </tr>
                    <tr>
                        <td>Hasil Kunjungan</td>
                        <td class="break-word">: {{ $detail->visit_report['visit_result'] }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">Data Permohonan Kredit</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td>Jenis KPP</td>
                        <td class="break-word">: {{ $detail->visit_report['kpp_type_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Properti</td>
                        <td class="break-word">: {{ $detail->kpr['kpr_type_property_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Harga Rumah</td>
                        <td class="break-word">: Rp. {{ number_format(round($detail->kpr['price']), 0, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <td>Luas Bangunan</td>
                        <td class="break-word">: {{ $detail->kpr['building_area'] }} m<sup>2</sup></td>
                    </tr>
                    <tr>
                        <td>Lokasi Rumah</td>
                        <td class="break-word">: {{ $detail->kpr['home_location'] }}</td>
                    </tr>
                    <tr>
                        <td>Jangka Waktu</td>
                        <td class="break-word">: {{ $detail->kpr['year'] }} Bulan</td>
                    </tr>
                    <tr>
                        <td>KPR Aktif ke</td>
                        <td class="break-word">: {{ $detail->kpr['active_kpr'] }}</td>
                    </tr>
                    <tr>
                        <td>Uang Muka</td>
                        <td class="break-word">: Rp. {{ number_format(round($detail->kpr['down_payment']), 0, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <td>Jumlah Permohonan</td>
                        <td class="break-word">: Rp. {{ number_format(round($detail->kpr['request_amount']), 0, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Dibiayai</td>
                        <td class="break-word">: {{ $detail->visit_report['type_financed_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Sektor Ekonomi</td>
                        <td class="break-word">: {{ $detail->visit_report['economy_sector_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Project</td>
                        <td class="break-word">: {{ $detail->visit_report['project_list_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Program</td>
                        <td class="break-word">: {{ $detail->visit_report['program_list_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Tujuan Penggunaan</td>
                        <td class="break-word">: {{ $detail->visit_report['use_reason_name'] }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">Data Keuangan</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td>Gaji/Pendapatan</td>
                        <td class="break-word">: Rp. {{ number_format(round($detail->customer->financial['salary']), 0, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <td>Pendapatan Lain</td>
                        <td class="break-word">: Rp. {{ number_format(round($detail->customer->financial['other_salary']), 0, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <td>Angsuran Pinjaman</td>
                        <td class="break-word">: Rp. {{ number_format(round($detail->customer->financial['loan_installment']), 0, ",", ".") }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">Data Mutasi Rekening</td>
                    </tr>
                </tbody>
            </table>

            @foreach( $detail->visit_report['mutation'] as $mutation )
                <table>
                    <tbody>
                        <tr>
                            <td>Nama Bank</td>
                            <td class="break-word">: {{ $mutation['bank'] }}</td>
                        </tr>
                        <tr>
                            <td>No. Rekening</td>
                            <td class="break-word">: {{ $mutation['number'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table class="full-width">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nominal</th>
                                            <th>Jenis Transaksi</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach( $mutation['bankstatement'] as $bank )
                                            <tr>
                                                <td>{{ $bank['date'] }}</td>
                                                <td>Rp. {{ $bank['amount'] > 0 ? number_format(round($bank['amount']), 0, ",", ".") : '' }}</td>
                                                <td class="break-word">{{ $bank['type'] }}</td>
                                                <td class="break-word">{{ $bank['note'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">Analisa RM</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td>Pros (Hal yang mendukung analisa)</td>
                        <td class="break-word">: {{ $detail->visit_report['pros'] }}</td>
                    </tr>
                    <tr>
                        <td>Con (Hal yang tidak mendukung analisa)</td>
                        <td class="break-word">: {{ $detail->visit_report['cons'] }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="term">
                <br/>Dengan ini saya meyakini kebenaran data nasabah dan merekomendasikan permohonan kredit untuk dapat diproses lebih lanjut :

                <div class="full-width">
                    <div class="half-width">
                        <table class="full-width">
                            <tbody>
                                <tr>
                                    <td class="title" colspan="2">RM</td>
                                </tr>
                            </tbody>
                        </table>

                        <table>
                            <tbody>
                                <tr>
                                    <td>Rekomendasi</td>
                                    <td class="break-word">: {{ $detail->visit_report['recommended'] == 'yes' ? 'Ya' : 'Tidak' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="half-width">
                        <table class="full-width">
                            <tbody>
                                <tr>
                                    <td class="title" colspan="2">Pinca</td>
                                </tr>
                            </tbody>
                        </table>

                        <table>
                            <tbody>
                                <tr>
                                    <td>Rekomendasi</td>
                                    <td class="break-word">: {{ $detail->recommended ? 'Ya' : 'Tidak' }}</td>
                                </tr>
                                <tr>
                                    <td>Catatan</td>
                                    <td class="break-word">: {{ $detail->recommendation }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="clear"></div>
                </div>
            </div>

            <br/><br/>

            <div class="full-width">
                <div class="half-width">
                    <div class="barcode">
                        <p>&nbsp;</p>
                        <img src="{{ asset('img/qr-code.png') }}">
                        <p class="underline">{{ $detail->ao_name ? $detail->ao_name : '-' }}</p>
                        <p>{{ $detail->ao_position ? $detail->ao_position : '-' }}</p>
                    </div>
                </div>
                <div class="half-width">
                    <div class="barcode">
                        <p>{{ date('d M Y', strtotime($detail->created_at)) }}</p>
                        <img src="{{ asset('img/qr-code.png') }}">
                        <p class="underline">{{ $detail->pinca_name ? $detail->pinca_name : '-' }}</p>
                        <p>{{ $detail->pinca_position ? $detail->pinca_position : '-' }}</p>
                    </div>
                </div>

                <div class="clear"></div>
            </div>
        </div>

    </body>

</html>