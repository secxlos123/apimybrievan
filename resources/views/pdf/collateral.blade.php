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
                        @if ($collateraldata->property->id == 1)
                            <td class="no-ref full-width">No. Reff Aplikasi : {{ $eformdata->ref_number }}</td>
                        @else
                            <td class="no-ref full-width"> Nama Proyek : {{ $collateraldata->property->name }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>

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
                        <td class="break-word">: {{ $collateraldata->property->city->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kategori</td>
                        <td class="break-word">: @if($collateraldata->property->category == 0) Rumah Tapak
                                @elseif($collateraldata->property->category == 1)Rumah Susun/Apartment
                                @elseif($collateraldata->property->category == 2)Rumah Toko
                                @endif</td>
                    </tr>
                    <tr>
                        <td class="label">Nama PIC Proyek</td>
                        <td class="break-word">: {{ $collateraldata->property->pic_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Properti</td>
                        <td class="break-word">: {{ $collateraldata->property->address }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor PKS</td>
                        <td class="break-word">: @if(!empty($collateraldata->property->pks_number)){{ $collateraldata->property->pks_number }}@else - @endif</td>
                    </tr>

                    <tr>
                        <td class="label">No. HP PIC Project</td>
                        <td class="break-word">: {{ $collateraldata->property->pic_phone }}</td>
                    </tr>

                    <tr>
                        <td class="label">Fasilitas</td>
                        <td class="break-word">: {{ strip_tags($collateraldata->property->facilities, "<a><br><i><ul><li><ol>") }}</td>
                    </tr>

                    <tr>
                        <td class="label">Deskripsi Properti</td>
                        <td class="break-word">: {!! strip_tags($collateraldata->property->description, "<a><br><i><ul><li><ol>") !!}</td>
                    </tr>

                </tbody>
            </table>

                @if ($collateraldata->developer->id != "1" )
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
                       @if (count($collateraldata->property->propertyTypes)>0)
                    @foreach($collateraldata->property->propertyTypes as $index => $propType)
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
                       @if (count($collateraldata->property->propertyItems)>0)
                    @foreach($collateraldata->property->propertyItems as $index => $propItem)
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
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsValuation->scoring_land_date )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Tanah</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsValuation->npw_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Tanah</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsValuation->nl_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Tanah</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsValuation->pnpw_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Tanah</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsValuation->pnl_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Penilaian NPW Bangunan</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsValuation->scoring_building_date)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Bangunan</td>
                        <td class="break-word">: Rp.  {{ number_format(str_replace('.','',$collateraldata->otsValuation->npw_building )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsValuation->nl_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsValuation->pnpw_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsValuation->pnl_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Penilaian NPW Tanah & Bangunan</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsValuation->scoring_all_date )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format(str_replace('.','',$collateraldata->otsValuation->npw_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format(str_replace('.','',$collateraldata->otsValuation->nl_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format(str_replace('.','',$collateraldata->otsValuation->pnpw_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format(str_replace('.','',$collateraldata->otsValuation->pnl_all))}}</td>
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
                        <td class="break-word">: {{ $collateraldata->otsInArea->location }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tipe Agunan</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->collateral_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kota/Kabupaten</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->city->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kecamatan/Desa</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->district }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kelurahan/Desa</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->sub_district }}</td>
                    </tr>
                    <tr>
                        <td class="label">RT/RW</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->rt}}/{{$collateraldata->otsInArea->rw}}</td>
                    </tr>
                    <tr>
                        <td class="label">Kode Pos</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->zip_code }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jarak</td>
                        <td class="break-word">: {{ intval($collateraldata->otsInArea->distance) }} 
                            @if ($collateraldata->otsInArea->unit_type == 1)
                            Kilometer
                            @else
                            Meter
                            @endif {{ $collateraldata->otsInArea->distance_from }} </td>
                    </tr>
                    <tr>
                        <td class="label">Posisi Terhadap Jalan</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->position_from_road }}</td>
                    </tr>
                    <tr>
                        <td class="label">Bentuk Tanah</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->ground_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jarak Posisi terhadap Jalan</td>
                        <td class="break-word">: {{ intval($collateraldata->otsInArea->distance_of_position) }} Meter</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Utara</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->north_limit }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Timur</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->east_limit }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Selatan</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->south_limit }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Barat</td>
                        <td class="break-word">:  {{ $collateraldata->otsInArea->west_limit }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan Lain</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->another_information }}</td>
                    </tr>
                    <tr>
                        <td class="label">Permukaan Tanah</td>
                        <td class="break-word">: {{ $collateraldata->otsInArea->ground_level }}</td>
                    </tr>
                     <tr>
                        <td class="label">Luas Tanah Sesuai Lapang </td>
                        <td class="break-word">: {{ intval($collateraldata->otsInArea->surface_area) }} M<sup>2</sup></td>
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
                        <td class="break-word">: {{ $collateraldata->otsLetter->type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hak Atas Tanah</td>
                        <td class="break-word">: {{ $collateraldata->otsLetter->authorization_land }} </td>
                    </tr>
                    <tr>
                        <td class="label">Kecocokan Data Dengan Kantor Anggara/BPN</td>
                        <td class="break-word">: {{ $collateraldata->otsLetter->match_bpn }} </td>
                    </tr>
                    <tr>
                        <td class="label">Kecocokan Pemeriksaan Lokasi Tanah Dilapangan</td>
                        <td class="break-word">: {{ $collateraldata->otsLetter->match_area }} </td>
                    </tr>
                    <tr>
                        <td class="label">Kecocokan Batas Tanah Dilapangan</td>
                        <td class="break-word">: {{ $collateraldata->otsLetter->match_limit_in_area }} </td>
                    </tr>
                    <tr>
                        <td class="label">Luas Tanah Berdasarkan Surat Tanah</td>
                        <td class="break-word">: {{ intval($collateraldata->otsLetter->surface_area_by_letter) }} M<sup>2</sup></td>
                    </tr>
                    <tr>
                        <td class="label">No Surat Tanah</td>
                        <td class="break-word">: {{ $collateraldata->otsLetter->number }} </td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Surat Tanah</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsLetter->date)) }} </td>
                    </tr>
                    <tr>
                        <td class="label">Atas Nama</td>
                        <td class="break-word">: {{ $collateraldata->otsLetter->on_behalf_of }} </td>
                    </tr>
                    <tr>
                        <td class="label">Masa Hak tanah</td>
                        <td class="break-word">: {{ $collateraldata->otsLetter->duration_land_authorization?$collateraldata->otsLetter->duration_land_authorization:'-' }} </td>
                    </tr>
                    <tr>
                        <td class="label">Nama Kantor Anggaran/BPN</td>
                        <td class="break-word">: {{ $collateraldata->otsLetter->letter_bpn_name?$collateraldata->otsLetter->letter_bpn_name:'-' }} </td>
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
                        <td class="break-word">: {{ $collateraldata->otsBuilding->permit_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Izin Mendirikan Bangunan</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsBuilding->permit_date))}} </td>
                    </tr>
                    <tr>
                        <td class="label">Atas Nama Izin Mendirikan Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->otsBuilding->on_behalf_of }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->otsBuilding->type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jumlah Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->otsBuilding->count }}</td>
                    </tr>
                    <tr>
                        <td class="label">Luas Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->otsBuilding->spacious }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tahun Mendirikan Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->otsBuilding->year }}</td>
                    </tr>
                    <tr>
                        <td class="label">Uraian Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->otsBuilding->description }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Utara</td>
                        <td class="break-word">: {{ intval($collateraldata->otsBuilding->north_limit) }} Meter Dari Bangunan {{ $collateraldata->otsBuilding->north_limit_from }}</td>
                    </tr>
                    <tr>
                        <td class="label">Batas Timur</td>
                        <td class="break-word">: {{ intval($collateraldata->otsBuilding->east_limit) }} Meter Dari Bangunan {{ $collateraldata->otsBuilding->east_limit_from }} </td>
                    </tr>
                     <tr>
                        <td class="label">Batas Selatan</td>
                        <td class="break-word">: {{ intval($collateraldata->otsBuilding->south_limit) }} Meter Dari Bangunan {{ $collateraldata->otsBuilding->south_limit_from }} </td>
                    </tr>
                     <tr>
                        <td class="label">Batas Barat</td>
                        <td class="break-word">: {{ intval($collateraldata->otsBuilding->west_limit) }} Meter Dari Bangunan {{ $collateraldata->otsBuilding->west_limit_from }} </td>
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
                        <td class="break-word">: {{ $collateraldata->otsEnvironment->designated_land }}</td>
                    </tr>
                     <tr>
                        <td class="label">Fasilitas Umum Yang Ada</td>
                        <td class="break-word">: {{ $collateraldata->otsEnvironment->designated_pln == 1 ? 'PLN,':''}} {{ $collateraldata->otsEnvironment->designated_phone == 1 ? 'Telepon Umum,':''}} {{ $collateraldata->otsEnvironment->designated_pam == 1 ? 'PAM,':''}} {{ $collateraldata->otsEnvironment->designated_telex == 1 ? 'Telex,':''}}  </td>
                    </tr>
                    <tr>
                        <td class="label">Fasilitas Umum Lain</td>
                        <td class="break-word">: {{ $collateraldata->otsEnvironment->other_designated }}</td>
                    </tr>
                    <tr>
                        <td class="label">Sarana Transportasi</td>
                        <td class="break-word">: {{ $collateraldata->otsEnvironment->transportation }}</td>
                    </tr>
                    <tr>
                        <td class="label">Lingkungan Terdekat Dari Lokasi Sebagian Besar</td>
                        <td class="break-word">: {{ $collateraldata->otsEnvironment->nearest_location }}</td>
                    </tr>
                    <tr>
                        <td class="label">Petunjuk Lain</td>
                        <td class="break-word">: {{ $collateraldata->otsEnvironment->other_guide }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jarak Dari Lokasi</td>
                        <td class="break-word">: {{ intval($collateraldata->otsEnvironment->distance_from_transportation) }} Meter</td>
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
                        <td class="break-word">: {{ $collateraldata->otsOther->bond_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penggunaan Bangunan Sesuai Fungsinya</td>
                        <td class="break-word">: {{ $collateraldata->otsOther->use_of_building_function }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pertukaran Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->otsOther->building_exchange }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hal-Hal Yang Perlu Diketahui Bank</td>
                        <td class="break-word">: {{ $collateraldata->otsOther->things_bank_must_know }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penggunaan Bangunan Secara Optimal</td>
                        <td class="break-word">: {{ $collateraldata->otsOther->optimal_building_use }}</td>
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
                        <td class="break-word">: {{ $collateraldata->otsSeven->collateral_status }}</td>
                    </tr>
                    <tr>
                        <td class="label">Atas Nama (Pemilik)</td>
                        <td class="break-word">: {{ $collateraldata->otsSeven->on_behalf_of }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Bukti Kepemilikan</td>
                        <td class="break-word">: {{ $collateraldata->otsSeven->ownership_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Lokasi</td>
                        <td class="break-word">: {{ $collateraldata->otsSeven->location }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat Agunan</td>
                        <td class="break-word">: {{ $collateraldata->otsSeven->address_collateral }}</td>
                    </tr>
                    <tr>
                        <td class="label">Deskripsi</td>
                        <td class="break-word">: {{ $collateraldata->otsSeven->description }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status Bukti Kepemilikan</td>
                        <td class="break-word">: {{ $collateraldata->otsSeven->ownership_status }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Bukti</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsSeven->date_evidence)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kelurahan/Desa</td>
                        <td class="break-word">: {{ $collateraldata->otsSeven->village }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kecamatan</td>
                        <td class="break-word">: {{ $collateraldata->otsSeven->districts }}</td>
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
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsEight->liquidation_realization )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Pasar Wajar</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsEight->fair_market)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Likuidasi</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsEight->liquidation)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Proyeksi Nilai Pasar Wajar</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsEight->fair_market_projection)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Proyeksi Nilai Likuidasi</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsEight->liquidation_projection)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Jual Objek Pajak (NJOP)</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsEight->njop)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penilaian Dilakukan Oleh</td>
                        <td class="break-word">: {{ $collateraldata->otsEight->appraisal_by }}</td>
                    </tr>
                    @if ($collateraldata->otsEight->appraisal_by == "Lembaga Penilai")
                    <tr>
                        <td class="label">Penilai Independent</td>
                        <td class="break-word">: {{ $collateraldata->otsEight->independent_appraiser }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Tanggal Penilaian Terakhir</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsEight->date_assessment ))}}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Pengikatan</td>
                        <td class="break-word">: {{ $collateraldata->otsEight->type_binding }}</td>
                    </tr>
                    {{-- <tr>
                        <td class="label">No. Bukti Pengikatan</td>
                        <td class="break-word">: {{ $collateraldata->otsEight->districts }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Pengikatan</td>
                        <td class="break-word">: {{ $collateraldata->otsEight->districts }}</td>
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
                        <td class="label" colspan="2">PEMECAHAN SERTIFIKAT</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->certificate_status  }}</td>
                    </tr>
                    @if ($collateraldata->otsNine->certificate_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsNine->receipt_date)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->information  }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label" colspan="2">DOKUMEN NOTARIS DEVELOPER</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->notary_status  }}</td>
                    </tr>
                    @if ($collateraldata->otsNine->notary_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsNine->receipt_date_notary)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->information_notary  }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label" colspan="2">DOKUMEN TAKE OVER</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->takeover_status  }}</td>
                    </tr>
                    @if ($collateraldata->otsNine->takeover_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsNine->receipt_date_takeover)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->information_takeover  }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label" colspan="2">PERJANJIAN KREDIT</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->credit_status  }}</td>
                    </tr>
                    @if ($collateraldata->otsNine->credit_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsNine->receipt_date_credit)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->information_credit  }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label" colspan="2">SKMHT</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->skmht_status  }}</td>
                    </tr>
                    @if ($collateraldata->otsNine->skmht_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsNine->receipt_date_skmht)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->information_skmht  }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label" colspan="2">IMB</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->imb_status  }}</td>
                    </tr>
                    @if ($collateraldata->otsNine->imb_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsNine->receipt_date_imb)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->information_imb  }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label" colspan="2">SHGB</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->shgb_status  }}</td>
                    </tr>
                    @if ($collateraldata->otsNine->shgb_status == "Sudah Diberikan")
                    <tr>
                        <td class="label">Tanggal Penelitian</td>
                        <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsNine->receipt_date_shgb)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="break-word">: {{ $collateraldata->otsNine->information_shgb  }}</td>
                    </tr>
                    @endif
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
                        <td class="break-word">: {{ $collateraldata->otsTen->paripasu  }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Paripasu Agunan Bank</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsTen->paripasu_bank )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Flag Asuransi</td>
                        <td class="break-word">: {{ $collateraldata->otsTen->insurance  }}</td>
                    </tr>
                    @if ($collateraldata->otsTen->insurance == "Ya")
                    <tr>
                        <td class="label">Nama Perusahaan Asuransi</td>
                        <td class="break-word">: {{ $collateraldata->otsTen->insurance_company  }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nilai Asuransi</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->otsTen->insurance_value )) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Eligibility</td>
                        <td class="break-word">: {{ $collateraldata->otsTen->eligibility  }}</td>
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
                                <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->otsInArea->created_at)) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="full-width">
                        <div class="barcode">
                            <img src="{{ asset('img/qr-code.png') }}">
                            <p class="underline">{{ $collateraldata->staff_name ? $collateraldata->staff_name : '-' }}</p>
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
                                <td class="label">Tanggal</td>
                                <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->created_at)) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="full-width">
                        <div class="barcode">
                            <img src="{{ asset('img/qr-code.png') }}">
                            <p class="underline">{{ $collateraldata->manager_name ? $collateraldata->manager_name : '-' }}</p>
                        </div>
                    </div>

                </div>
                <br/>
            </div>
        </div>

    </body>
  {{die()}}
</html>