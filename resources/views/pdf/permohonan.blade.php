<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>E-Form PDF</title>
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
                width: 170px;
            }
            .full-width {
                width: 100%;
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
            .break-word {
                word-wrap: break-word;
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
                            <div class="color-orange">e-Form</div>
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
                        <td class="title" colspan="2">Data Calon Debitur</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td>NIK</td>
                        <td class="break-word">: {{ $detail->nik }}</td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td class="break-word">: {{ $detail->customer->personal['name'] }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td class="break-word">: {{ $detail->customer->personal['address'] }}</td>
                    </tr>
                    <tr>
                        <td>Status tempat tinggal</td>
                        <td class="break-word">: {{ $detail->customer->personal['address_status'] }}</td>
                    </tr>
                    <tr>
                        <td>Tempat, Tanggal lahir</td>
                        <td class="break-word">: {{ $detail->customer->personal['birth_place'] }}, {{ date('d M Y', strtotime($detail->customer->personal['birth_date'])) }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td class="break-word">: {{ $detail->customer->personal['gender'] }}</td>
                    </tr>
                    <tr>
                        <td>Status Pernikahan</td>
                        <td class="break-word">: {{ $detail->customer->personal['status'] }}</td>
                    </tr>
                    <tr>
                        <td>Kewarganegaraan</td>
                        <td class="break-word">: {{ $detail->customer->personal['citizenship'] }}</td>
                    </tr>
                    <tr>
                        <td>No. Telepon</td>
                        <td class="break-word">: {{ $detail->customer->personal['phone'] }}</td>
                    </tr>
                    <tr>
                        <td>No. Handphone</td>
                        <td class="break-word">: {{ $detail->customer->personal['mobile_phone'] }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td class="break-word">: {{ $detail->customer->personal['email'] }}</td>
                    </tr>
                    <tr>
                        <td>Nama Ibu Kandung</td>
                        <td class="break-word">: {{ $detail->customer->personal['mother_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Jumlah Tanggungan</td>
                        <td class="break-word">: {{ $detail->customer->financial['dependent_amount'] }}</td>
                    </tr>
                </tbody>
            </table>

            @if( $detail->customer->personal['status_id'] == 2 )
                <table class="full-width">
                    <tbody>
                        <tr>
                            <td class="title" colspan="2">Data Pasangan</td>
                        </tr>
                    </tbody>
                </table>

                <table>
                    <tbody>
                        <tr>
                            <td>NIK</td>
                            <td class="break-word">: {{ $detail->customer->personal['couple_nik'] }}</td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td class="break-word">: {{ $detail->customer->personal['couple_name'] }}</td>
                        </tr>
                        <tr>
                            <td>Tempat, Tanggal lahir</td>
                            <td class="break-word">: {{ $detail->customer->personal['couple_birth_place'] }}, {{ date('d M Y', strtotime($detail->customer->personal['couple_birth_date'])) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">Data Pekerjaan</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td>Bidang Pekerjaan</td>
                        <td class="break-word">: {{ $detail->customer->work['work_field'] }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Pekerjaan</td>
                        <td class="break-word">: {{ $detail->customer->work['type'] }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td class="break-word">: {{ $detail->customer->work['work'] }}</td>
                    </tr>
                    <tr>
                        <td>Nama Perusahaan</td>
                        <td class="break-word">: {{ $detail->customer->work['company_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td class="break-word">: {{ $detail->customer->work['position'] }}</td>
                    </tr>
                    <tr>
                        <td>Lama Bekerja/Usaha</td>
                        <td class="break-word">: {{ $detail->customer->work['work_duration'] ? $detail->customer->work['work_duration'] : '0' }} Tahun,  {{ $detail->customer->work['work_duration_month'] ? $detail->customer->work['work_duration_month'] : '0' }} Bulan</td>
                    </tr>
                    <tr>
                        <td>Alamat Kantor/Usaha</td>
                        <td class="break-word">: {{ $detail->customer->work['office_address'] }}</td>
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

            @if( $detail->customer->financial['status_finance'] == "Joint Income" )
                <table class="full-width">
                    <tbody>
                        <tr>
                            <td class="title" colspan="2">Data Keuangan pasangan</td>
                        </tr>
                    </tbody>
                </table>

                <table>
                    <tbody>
                        <tr>
                            <td>Gaji/ Pendapatan</td>
                            <td class="break-word">: Rp. {{ number_format(round($detail->customer->financial['salary_couple']), 0, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <td>Pendapatan Lain</td>
                            <td class="break-word">: Rp. {{ number_format(round($detail->customer->financial['other_salary_couple']), 0, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <td>Angsuran Pinjaman</td>
                            <td class="break-word">: Rp. {{ number_format(round($detail->customer->financial['loan_installment_couple']), 0, ",", ".") }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">Data Keluarga Terdekat</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td>Nama</td>
                        <td class="break-word">: {{ $detail->customer->contact['emergency_name'] }}</td>
                    </tr>
                    <tr>
                        <td>No. Handphone</td>
                        <td class="break-word">: {{ $detail->customer->contact['emergency_contact'] }}</td>
                    </tr>
                    <tr>
                        <td>Hubungan</td>
                        <td class="break-word">: {{ $detail->customer->contact['emergency_relation'] }}</td>
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
                </tbody>
            </table>

            <div class="term">
                <ol>
                    <li>Dengan ini Saya/ Kami mengajukan KPR BRI dan mengizinkan pihak Bank BRI untuk menggunakan data tersebut diatas untuk kepentingan permohonan kredit.</li>
                    <li>Saya/ Kami menyatakan bahwa semua informasi yang diberikan dalam formulir aplikasi ini adalah sesuai keadaan yang sebenarnya.</li>
                    <li>Saya / Kami memberikan kuasa kepada Bank BRI / pihak yang ditunjuk oleh Bank BRI untuk memeriksa atau mencari informasi lebih jauh dari sumber layak manapun, dan akan memberikan informasi terbaru apabila terdapat perubahan data sehubungan dengan permohonan ini.</li>
                    <li>Bank BRI mempunyai hak untuk menolak untuk menerima permohonan saya/ kami tanpa memberitahukan alasannya.</li>
                    <li>Sehubungan dengan disetujuinya verifikasi permohonan kredit ini, saya/ kami menyatakan akan mentaati segala persyaratan ketentuan yang berlaku di Bank BRI.</li>
                </ol>
            </div>

            <div class="barcode">
                <p>{{ date('d M Y', strtotime($detail->created_at)) }}</p>
                <img src="{{ asset('img/qr-code.png') }}">
                <p>{{ $detail->customer_name }}</p>
            </div>
        </div>

    </body>

</html>