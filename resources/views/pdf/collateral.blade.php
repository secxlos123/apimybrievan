<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>E-COLLATERAL PDF</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <style type="text/css" media="all">
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
                page-break-before: always;
            }
            .term-02 {
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
                max-width: 300px;
                word-wrap: break-word;
            }
            .underline {
                width: 200px;
                margin: 0 auto;
                border-bottom: solid 1px black;
            }
            #collateral {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            #collateral td, #collateral th {
                border: 1px solid #ddd;
                padding: 8px;
            }

            #collateral tr:nth-child(even){background-color: #f2f2f2;}

            #collateral tr:hover {background-color: #ddd;}

            #collateral th {
                padding-top: 12px;
                padding-bottom: 12px;
                text-align: left;
                background-color: #00529C;
                color: white;
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
                            <div class="color-orange">e-Collateral</div>
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
                        @if ($collateral->developer->id == 1)
                            <td class="no-ref full-width">No. Reff Aplikasi : {{ $eform->ref_number }}</td>
                        @else
                            <td class="no-ref full-width"> Nama Proyek : {{ $collateral->property->name }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>

            @if ($collateral->developer->id == 1)
            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">DATA NASABAH</td>
                    </tr>
                </tbody>
            </table>
            <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">NIK</td>
                        <td class="break-word">: {{ $eform->nik }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama</td>
                        <td class="break-word">: {{ $eform->customer->personal['name'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="break-word">: {{ $eform->customer->personal['address'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status tempat tinggal</td>
                        <td class="break-word">: {{ $eform->customer->personal['address_status'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tempat, Tanggal lahir</td>
                        <td class="break-word">: {{ $eform->customer->personal['birth_place'] }}, {{ date('d M Y', strtotime($eform->customer->personal['birth_date'])) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Kelamin</td>
                        <td class="break-word">: {{ $eform->customer->personal['gender'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status Pernikahan</td>
                        <td class="break-word">: {{ $eform->customer->personal['status'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kewarganegaraan</td>
                        <td class="break-word">: {{ $eform->customer->personal['citizenship'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Telepon</td>
                        <td class="break-word">: {{ $eform->customer->personal['phone'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Handphone</td>
                        <td class="break-word">: {{ $eform->customer->personal['mobile_phone'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Email</td>
                        <td class="break-word">: {{ $eform->customer->personal['email'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Ibu Kandung</td>
                        <td class="break-word">: {{ $eform->customer->personal['mother_name'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jumlah Tanggungan</td>
                        <td class="break-word">: {{ $eform->customer->financial['dependent_amount'] }}</td>
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
                        <td class="label">Jenis KPP</td>
                        <td class="break-word">: {{ $eform->visit_report['kpp_type_name'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Properti</td>
                        <td class="break-word">: {{ $eform->kpr['kpr_type_property_name'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Harga Rumah</td>
                        <td class="break-word">: Rp. {{ number_format(round($eform->kpr['price']), 0, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <td class="label">Luas Bangunan</td>
                        <td class="break-word">: {{ $eform->kpr['building_area'] }} m<sup>2</sup></td>
                    </tr>
                    <tr>
                        <td class="label">Lokasi Rumah</td>
                        <td class="break-word">: {{ $eform->kpr['home_location'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jangka Waktu</td>
                        <td class="break-word">: {{ $eform->kpr['year'] }} Bulan</td>
                    </tr>
                    <tr>
                        <td class="label">KPR Aktif ke</td>
                        <td class="break-word">: {{ $eform->kpr['active_kpr_preview'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Uang Muka</td>
                        <td class="break-word">: Rp. {{ number_format(round($eform->kpr['down_payment']), 0, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jumlah Permohonan</td>
                        <td class="break-word">: Rp. {{ number_format(round($eform->kpr['request_amount']), 0, ",", ".") }}</td>
                    </tr>
                </tbody>
            </table>
            @else
            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">DATA PROPERTI</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td class="label">Kota</td>
                        <td class="break-word">: {{ $collateral->property->city->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kategori</td>
                        <td class="break-word">: @if($collateral->property->category == 0) Rumah Tapak
                                @elseif($collateral->property->category == 1)Rumah Susun/Apartment
                                @elseif($collateral->property->category == 2)Rumah Toko
                                @endif</td>
                    </tr>
                    <tr>
                        <td class="label">Nama PIC Proyek</td>
                        <td class="break-word">: {{ $collateral->property->pic_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Properti</td>
                        <td class="break-word">: {{ $collateral->property->address }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor PKS</td>
                        <td class="break-word">: @if(!empty($collateral->property->pks_number)){{ $collateral->property->pks_number }}@else - @endif</td>
                    </tr>

                    <tr>
                        <td class="label">No. HP PIC Project</td>
                        <td class="break-word">: {{ $collateral->property->pic_phone }}</td>
                    </tr>

                    <tr>
                        <td class="label">Fasilitas</td>
                        <td class="break-word">: {{ strip_tags($collateral->property->facilities, "<a><br><i><ul><li><ol>") }}</td>
                    </tr>

                    <tr>
                        <td class="label">Deskripsi Properti</td>
                        <td class="break-word">: {!! strip_tags($collateral->property->description, "<a><br><i><ul><li><ol>") !!}</td>
                    </tr>

                </tbody>
            </table>


                <table class="full-width">
                    <tbody>
                        <tr>
                            <td class="title" colspan="2">TIPE PROPERTI</td>
                        </tr>
                    </tbody>
                </table>

                <table class="full-width" id="collateral">
                    <thead>
                        <tr>
                            <th>Nama Tipe</th>
                            <th>Luas Bangunan</th>
                            <th>Luas Tanah</th>
                            <th>Sertifikat</th>
                        </tr>
                    </thead>
                    <tbody>
                       @if (count($collateral->property->propertyTypes)>0)
                    @foreach($collateral->property->propertyTypes as $index => $propType)
                        <tr>

                            <td>
                                <p class="form-control-static">{{$propType->name}}</p>
                            </td>
                            <td>
                                <p class="form-control-static">{{$propType->building_area}}</p>
                            </td>
                            <td>
                                <p class="form-control-static">{{$propType->surface_area}}</p>
                            </td>
                            <td>
                                <p class="form-control-static">{{$propType->certificate}}</p>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>

                <table class="full-width">
                    <tbody>
                        <tr>
                            <td class="title" colspan="2">UNIT PROPERTI</td>
                        </tr>
                    </tbody>
                </table>

                <table class="full-width" id="collateral" >
                    <thead>
                        <tr>
                            <th>Tipe Proyek</th>
                            <th>Alamat</th>
                            <th>Harga</th>
                            <th>Available</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                       @if (count($collateral->property->propertyItems)>0)
                    @foreach($collateral->property->propertyItems as $index => $propItem)
                        <tr>
                            <td>

                                <p class="form-control-static">{{$propItem->property_type_id}}</p>
                            </td>
                            <td>
                                <p class="form-control-static">{{$propItem->address}}</p>
                            </td>
                            <td>
                                <p class="form-control-static">{{number_format(round($propItem->price), 0, ",", ".") }}</p>
                            </td>
                            <td>
                                <p class="form-control-static">{{($propItem->is_available == 1 ? 'Tersedia' : 'Tidak Tersedia')}}</p>
                            </td>
                            <td>
                                <p class="form-control-static">{{ucwords($propItem->status)}}</p>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            @endif

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">INFORMASI PENILAIAN</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">Tanggal Penilaian NPW Tanah</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsValuation->scoring_land_date )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Tanah</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsValuation->npw_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Tanah</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsValuation->nl_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Tanah</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsValuation->pnpw_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Tanah</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsValuation->pnl_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Penilaian NPW Bangunan</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsValuation->scoring_building_date)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Bangunan</td>
                        <td class="break-word">: Rp.  {{ number_format((int) str_replace('.','',$collateral->otsValuation->npw_building )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsValuation->nl_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsValuation->pnpw_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsValuation->pnl_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Penilaian NPW Tanah & Bangunan</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsValuation->scoring_all_date )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format((int) str_replace('.','',$collateral->otsValuation->npw_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format((int) str_replace('.','',$collateral->otsValuation->nl_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format((int) str_replace('.','',$collateral->otsValuation->pnpw_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format((int) str_replace('.','',$collateral->otsValuation->pnl_all))}}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">IDENTIFIKASI TANAH DI LAPANGAN</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">Lokasi</td>
                        <td class="break-word">: {{ $collateral->otsInArea->location }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tipe Agunan</td>
                        <td class="break-word">: {{ $collateral->otsInArea->collateral_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kota/Kabupaten</td>
                        <td class="break-word">: {{ $collateral->otsInArea->city->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kecamatan/Desa</td>
                        <td class="break-word">: {{ $collateral->otsInArea->district }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kelurahan/Desa</td>
                        <td class="break-word">: {{ $collateral->otsInArea->sub_district }}</td>
                    </tr>
                    <tr>
                        <td class="label">RT/RW</td>
                        <td class="break-word">: {{ $collateral->otsInArea->rt}}/{{$collateral->otsInArea->rw}}</td>
                    </tr>
                    <tr>
                        <td class="label">Kode Pos</td>
                        <td class="break-word">: {{ $collateral->otsInArea->zip_code }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jarak</td>
                        <td class="break-word">: {{ intval($collateral->otsInArea->distance) }}
                            @if ($collateral->otsInArea->unit_type == 1)
                            Kilometer
                            @else
                            Meter
                            @endif {{ $collateral->otsInArea->distance_from }} </td>
                    </tr>
                    <tr>
                        <td class="label">Posisi Terhadap Jalan</td>
                        <td class="break-word">: {{ $collateral->otsInArea->position_from_road }}</td>
                    </tr>
                    <tr>
                        <td class="label">Bentuk Tanah</td>
                        <td class="break-word">: {{ $collateral->otsInArea->ground_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jarak Posisi terhadap Jalan</td>
                        <td class="break-word">: {{ intval($collateral->otsInArea->distance_of_position) }} Meter</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Utara</td>
                        <td class="break-word">: {{ $collateral->otsInArea->north_limit }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Timur</td>
                        <td class="break-word">: {{ $collateral->otsInArea->east_limit }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Selatan</td>
                        <td class="break-word">: {{ $collateral->otsInArea->south_limit }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Barat</td>
                        <td class="break-word">:  {{ $collateral->otsInArea->west_limit }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan Lain</td>
                        <td class="break-word">: {{ $collateral->otsInArea->another_information }}</td>
                    </tr>
                    <tr>
                        <td class="label">Permukaan Tanah</td>
                        <td class="break-word">: {{ $collateral->otsInArea->ground_level }}</td>
                    </tr>
                     <tr>
                        <td class="label">Luas Tanah Sesuai Lapang </td>
                        <td class="break-word">: {{ intval($collateral->otsInArea->surface_area) }} M<sup>2</sup></td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">IDENTIFIKASI TANAH BERDASARKAN SURAT TANAH</td>
                    </tr>
                </tbody>
            </table>

             <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">Jenis Surat Tanah</td>
                        <td class="break-word">: {{ $collateral->otsLetter->type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hak Atas Tanah</td>
                        <td class="break-word">: {{ $collateral->otsLetter->authorization_land }} </td>
                    </tr>
                    <tr>
                        <td class="label">Kecocokan Data Dengan Kantor Anggara/BPN</td>
                        <td class="break-word">: {{ $collateral->otsLetter->match_bpn }} </td>
                    </tr>
                    <tr>
                        <td class="label">Kecocokan Pemeriksaan Lokasi Tanah Dilapangan</td>
                        <td class="break-word">: {{ $collateral->otsLetter->match_area }} </td>
                    </tr>
                    <tr>
                        <td class="label">Kecocokan Batas Tanah Dilapangan</td>
                        <td class="break-word">: {{ $collateral->otsLetter->match_limit_in_area }} </td>
                    </tr>
                    <tr>
                        <td class="label">Luas Tanah Berdasarkan Surat Tanah</td>
                        <td class="break-word">: {{ intval($collateral->otsLetter->surface_area_by_letter) }} M<sup>2</sup></td>
                    </tr>
                    <tr>
                        <td class="label">No Surat Tanah</td>
                        <td class="break-word">: {{ $collateral->otsLetter->number }} </td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Surat Tanah</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsLetter->date)) }} </td>
                    </tr>
                    <tr>
                        <td class="label">Atas Nama</td>
                        <td class="break-word">: {{ $collateral->otsLetter->on_behalf_of }} </td>
                    </tr>
                    <tr>
                        <td class="label">Masa Hak tanah</td>
                        <td class="break-word">: {{ $collateral->otsLetter->duration_land_authorization?$collateral->otsLetter->duration_land_authorization:'-' }} </td>
                    </tr>
                    <tr>
                        <td class="label">Nama Kantor Anggaran/BPN</td>
                        <td class="break-word">: {{ $collateral->otsLetter->bpn_name?$collateral->otsLetter->bpn_name:'-' }} </td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">URAIAN BANGUNAN</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">No Izin Mendirikan Bangunan</td>
                        <td class="break-word">: {{ $collateral->otsBuilding->permit_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Izin Mendirikan Bangunan</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsBuilding->permit_date))}} </td>
                    </tr>
                    <tr>
                        <td class="label">Atas Nama Izin Mendirikan Bangunan</td>
                        <td class="break-word">: {{ $collateral->otsBuilding->on_behalf_of }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Bangunan</td>
                        <td class="break-word">: {{ $collateral->otsBuilding->type_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jumlah Bangunan</td>
                        <td class="break-word">: {{ $collateral->otsBuilding->count }}</td>
                    </tr>
                    <tr>
                        <td class="label">Luas Bangunan</td>
                        <td class="break-word">: {{ $collateral->otsBuilding->spacious }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tahun Mendirikan Bangunan</td>
                        <td class="break-word">: {{ $collateral->otsBuilding->year }}</td>
                    </tr>
                    <tr>
                        <td class="label">Uraian Bangunan</td>
                        <td class="break-word">: {{ $collateral->otsBuilding->description }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Utara</td>
                        <td class="break-word">: {{ intval($collateral->otsBuilding->north_limit) }} Meter Dari Bangunan {{ $collateral->otsBuilding->north_limit_from }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Timur</td>
                        <td class="break-word">: {{ intval($collateral->otsBuilding->east_limit) }} Meter Dari Bangunan {{ $collateral->otsBuilding->east_limit_from }} </td>
                    </tr>
                     <tr>
                        <td class="label">Batas Selatan</td>
                        <td class="break-word">: {{ intval($collateral->otsBuilding->south_limit) }} Meter Dari Bangunan {{ $collateral->otsBuilding->south_limit_from }} </td>
                    </tr>
                     <tr>
                        <td class="label">Batas Barat</td>
                        <td class="break-word">: {{ intval($collateral->otsBuilding->west_limit) }} Meter Dari Bangunan {{ $collateral->otsBuilding->west_limit_from }} </td>
                    </tr>
                </tbody>
            </table>

             <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">IDENTIFIKASI DATA LINGKUNGAN</td>
                    </tr>
                </tbody>
            </table>

              <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">Peruntukan Tanah</td>
                        <td class="break-word">: {{ $collateral->otsEnvironment->designated_land }}</td>
                    </tr>
                     <tr>
                        <td class="label">Fasilitas Umum Yang Ada</td>
                        <td class="break-word">: {{ $collateral->otsEnvironment->designated_pln == 1 ? 'PLN,':''}} {{ $collateral->otsEnvironment->designated_phone == 1 ? 'Telepon Umum,':''}} {{ $collateral->otsEnvironment->designated_pam == 1 ? 'PAM,':''}} {{ $collateral->otsEnvironment->designated_telex == 1 ? 'Telex,':''}}  </td>
                    </tr>
                    <tr>
                        <td class="label">Fasilitas Umum Lain</td>
                        <td class="break-word">: {{ $collateral->otsEnvironment->other_designated }}</td>
                    </tr>
                    <tr>
                        <td class="label">Sarana Transportasi</td>
                        <td class="break-word">: {{ $collateral->otsEnvironment->transportation }}</td>
                    </tr>
                    <tr>
                        <td class="label">Lingkungan Terdekat Dari Lokasi Sebagian Besar</td>
                        <td class="break-word">: {{ $collateral->otsEnvironment->nearest_location }}</td>
                    </tr>
                    <tr>
                        <td class="label">Petunjuk Lain</td>
                        <td class="break-word">: {{ $collateral->otsEnvironment->other_guide }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jarak Dari Lokasi</td>
                        <td class="break-word">: {{ intval($collateral->otsEnvironment->distance_from_transportation) }} Meter</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">LAIN-LAIN</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">Jenis Ikatan</td>
                        <td class="break-word">: {{ $collateral->otsOther->bond_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penggunaan Bangunan Sesuai Fungsinya</td>
                        <td class="break-word">: {{ $collateral->otsOther->use_of_building_function }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pertukaran Bangunan</td>
                        <td class="break-word">: {{ $collateral->otsOther->building_exchange }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hal-Hal Yang Perlu Diketahui Bank</td>
                        <td class="break-word">: {{ $collateral->otsOther->things_bank_must_know }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penggunaan Bangunan Secara Optimal</td>
                        <td class="break-word">: {{ $collateral->otsOther->optimal_building_use }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">AGUNAN TANAH & RUMAH TINGGAL</td>
                    </tr>
                </tbody>
            </table>
            <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">Status Agunan</td>
                        <td class="break-word">: {{ $collateral->otsSeven->collateral_status }}</td>
                    </tr>
                    <tr>
                        <td class="label">Atas Nama (Pemilik)</td>
                        <td class="break-word">: {{ $collateral->otsSeven->on_behalf_of }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Bukti Kepemilikan</td>
                        <td class="break-word">: {{ $collateral->otsSeven->ownership_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Lokasi</td>
                        <td class="break-word">: {{ ($collateral->otsSeven->city) ? $collateral->otsSeven->city->name : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat Agunan</td>
                        <td class="break-word">: {{ $collateral->otsSeven->address_collateral }}</td>
                    </tr>
                    <tr>
                        <td class="label">Deskripsi</td>
                        <td class="break-word">: {{ $collateral->otsSeven->description }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status Bukti Kepemilikan</td>
                        <td class="break-word">: {{ $collateral->otsSeven->ownership_status }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Bukti</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsSeven->date_evidence)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kelurahan/Desa</td>
                        <td class="break-word">: {{ $collateral->otsSeven->village }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kecamatan</td>
                        <td class="break-word">: {{ $collateral->otsSeven->districts }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">NILAI LIKUIDITAS SAAT REALISASI</td>
                    </tr>
                </tbody>
            </table>
             <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">Nilai Likuiditas saat Realisasi</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsEight->liquidation_realization )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Pasar Wajar</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsEight->fair_market)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Likuidasi</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsEight->liquidation)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Proyeksi Nilai Pasar Wajar</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsEight->fair_market_projection)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Proyeksi Nilai Likuidasi</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsEight->liquidation_projection)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Jual Objek Pajak (NJOP)</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsEight->njop)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penilaian Dilakukan Oleh</td>
                        <td class="break-word">: {{ $collateral->otsEight->appraisal_by }}</td>
                    </tr>
                    @if ($collateral->otsEight->appraisal_by == "Lembaga Penilai")
                    <tr>
                        <td class="label">Penilai Independent</td>
                        <td class="break-word">: {{ $collateral->otsEight->independent_appraiser }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Tanggal Penilaian Terakhir</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsEight->date_assessment ))}}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Pengikatan</td>
                        <td class="break-word">: {{ $collateral->otsEight->type_binding_name }}</td>
                    </tr>
                    {{-- <tr>
                        <td class="label">No. Bukti Pengikatan</td>
                        <td class="break-word">: {{ $collateral->otsEight->districts }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Pengikatan</td>
                        <td class="break-word">: {{ $collateral->otsEight->districts }}</td>
                    </tr> --}}
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">PEMECAHAN SERTIFIKAT</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">PEMECAHAN SERTIFIKAT</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateral->otsNine->certificate_status  }}</td>
                    </tr>
                    @if ($collateral->otsNine->certificate_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsNine->receipt_date)) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateral->otsNine->information  }}</td>
                    </tr>
                    <tr>
                        <td class="label" >DOKUMEN NOTARIS DEVELOPER</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateral->otsNine->notary_status  }}</td>
                    </tr>
                    @if ($collateral->otsNine->notary_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsNine->receipt_date_notary)) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateral->otsNine->information_notary  }}</td>
                    </tr>
                    <tr>
                        <td class="label" >DOKUMEN TAKE OVER</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateral->otsNine->takeover_status  }}</td>
                    </tr>
                    @if ($collateral->otsNine->takeover_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsNine->receipt_date_takeover)) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateral->otsNine->information_takeover  }}</td>
                    </tr>
                    <tr>
                        <td class="label" >PERJANJIAN KREDIT</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateral->otsNine->credit_status  }}</td>
                    </tr>
                    @if ($collateral->otsNine->credit_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsNine->receipt_date_credit)) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateral->otsNine->information_credit  }}</td>
                    </tr>
                    <tr>
                        <td class="label" >SKMHT</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateral->otsNine->skmht_status  }}</td>
                    </tr>
                    @if ($collateral->otsNine->skmht_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsNine->receipt_date_skmht)) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateral->otsNine->information_skmht  }}</td>
                    </tr>
                    <tr>
                        <td class="label" >IMB</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateral->otsNine->imb_status  }}</td>
                    </tr>
                    @if ($collateral->otsNine->imb_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsNine->receipt_date_imb)) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateral->otsNine->information_imb  }}</td>
                    </tr>
                    <tr>
                        <td class="label" >SHGB</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateral->otsNine->shgb_status  }}</td>
                    </tr>
                    @if ($collateral->otsNine->shgb_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsNine->receipt_date_shgb)) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateral->otsNine->information_shgb  }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="full-width">
                <tbody>
                    <tr>
                        <td class="title" colspan="2">PARIPASU</td>
                    </tr>
                </tbody>
            </table>
             <table class="full-width" >
                <tbody>
                    <tr>
                        <td class="label">Paripasu</td>
                        <td class="break-word">: {{ $collateral->otsTen->paripasu  }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Paripasu Agunan Bank</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsTen->paripasu_bank )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Flag Asuransi</td>
                        <td class="break-word">: {{ $collateral->otsTen->insurance  }}</td>
                    </tr>
                    @if ($collateral->otsTen->insurance == "Ya")
                    <tr>
                        <td class="label">Nama Perusahaan Asuransi</td>
                        <td class="break-word">: {{ $collateral->otsTen->insurance_company_name  }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Asuransi</td>
                        <td class="break-word">: Rp. {{ number_format((int) str_replace('.','',$collateral->otsTen->insurance_value )) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Eligibility</td>
                        <td class="break-word">: {{ $collateral->otsTen->eligibility  }}</td>
                    </tr>
                </tbody>
            </table>

                <div class="term-02">
                    <table class="full-width">
                        <tbody>
                            <tr>
                                <td class="title" colspan="2">Staff / AO </td>
                            </tr>
                        </tbody>
                    </table>

                    <table>
                        <tbody>
                            <tr>
                                <td class="label">Tanggal</td>
                                <td class="break-word">: {{ date('d M Y', strtotime($collateral->otsInArea->created_at)) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="full-width">
                        <div class="barcode">
                            <img src="{{ asset('img/qr-code.png') }}">
                            <p class="underline">{{ $collateral->staff_name ? $collateral->staff_name : '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="term-02">
                    <table class="full-width">
                        <tbody>
                            <tr>
                                <td class="title" colspan="2">MANAGER</td>
                            </tr>
                        </tbody>
                    </table>

                    <table>
                        <tbody>
                             <tr>
                                <td class="label">Catatan</td>
                                <td class="break-word">: {{ $collateral->remark }}</td>
                            </tr>
                            <tr>
                                <td class="label">Tanggal</td>
                                <td class="break-word">: {{ date('d M Y', strtotime($collateral->created_at)) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="full-width">
                        <div class="barcode">
                            <img src="{{ asset('img/qr-code.png') }}">
                            <p class="underline">{{ $collateral->manager_name ? $collateral->manager_name : '-' }}</p>
                        </div>
                    </div>
                </div>
                <br/>
            </div>
        </div>
    </body>
</html>