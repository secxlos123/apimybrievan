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
                        <td class="break-word">: @if(!empty($collateraldata->property->pks_number])){{ $collateraldata->property->pks_number }}@else - @endif</td>
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

                <table>
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

                <table>
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
                                <p class="form-control-static">{{number_format(round($propItem->price, 0, ",", "."))}}</p>
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

            <table>
                <tbody>
                    <tr>
                        <td class="label">Tanggal Penilaian NPW Tanah</td>
                        <td class="break-word">: {{ $collateraldata->ots_valuation->scoring_land_date }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Tanah</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->ots_valuation->npw_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Tanah</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->ots_valuation->nl_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Tanah</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->ots_valuation->pnpw_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Tanah</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->ots_valuation->pnl_land)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Penilaian NPW Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->ots_valuation->scoring_building_date }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Bangunan</td>
                        <td class="break-word">: Rp.  {{ number_format(str_replace('.','',$collateraldata->ots_valuation->npw_building )) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->ots_valuation->nl_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->ots_valuation->pnpw_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Bangunan</td>
                        <td class="break-word">: Rp. {{ number_format(str_replace('.','',$collateraldata->ots_valuation->pnl_building)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Penilaian NPW Tanah & Bangunan</td>
                        <td class="break-word">: {{ $collateraldata->ots_valuation->scoring_all_date }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPW Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format(str_replace('.','',$collateraldata->ots_valuation->npw_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">NL Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format(str_replace('.','',$collateraldata->ots_valuation->nl_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">PNPW Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format(str_replace('.','',$collateraldata->ots_valuation->pnpw_all))}}</td>
                    </tr>
                    <tr>
                        <td class="label">PNL Tanah & Bangunan</td>
                        <td class="break-word">: Rp. {{number_format(str_replace('.','',$collateraldata->ots_valuation->pnl_all))}}</td>
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
                                <td class="break-word">: {{ date('d M Y', strtotime($collateraldata->ots_area->created_at)) }}</td>
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

</html>