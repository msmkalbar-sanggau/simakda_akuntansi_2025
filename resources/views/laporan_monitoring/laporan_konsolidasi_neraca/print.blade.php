<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LAPORAN KONSOLIDASI NERACA</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }

        .bordered {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .bordered th,
        .bordered td {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered th {
            background-color: #cccccc;
        }

        .bordered td:nth-child(n+3) {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div id="header" style="border: 1px solid black; text-align: center;">
        <img style="width: 84px; float: left; margin: 8px;"
            src="{{ asset('template/assets/images/' . $header1->logo_pemda_hp) }}" width="75" height="100" />
        <h4 style="margin: 8px 0px;">PEMERINTAH {{ strtoupper($header1->nm_pemda) }}</h4>
        <h4 style="margin: 8px 0px;">LAPORAN KONSOLIDASI NERACA</h4>
        <h4 style="margin: 8px 0px;">{{ $arraybulan[$bulan] }} {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div>
    <table class="bordered">
        <thead>
            <tr>
                <th>Kode SKPD</th>
                <th>Nama SKPD</th>
                <th>Aset</th>
                <th>Kewajiban <br />& <br /> Ekuitas</th>
                <th>Selisih</th>
            </tr>
        </thead>

        <body>
            @php
                $selisih = 0;
            @endphp
            @foreach ($data as $value)
                @php
                    $selisih = $value->nilai_aset - $value->nilai_kew_eku;
                @endphp
                <tr>
                    <td>{{ $value->kd_skpd }}</td>
                    <td>{{ $value->nm_skpd }}</td>
                    <td>{{ $value->nilai_aset >= 0 ? rupiah($value->nilai_aset) : '(' . rupiah(abs($value->nilai_aset)) . ')' }}
                    </td>
                    <td>{{ $value->nilai_kew_eku >= 0 ? rupiah($value->nilai_kew_eku) : '(' . rupiah(abs($value->nilai_kew_eku)) . ')' }}
                    </td>
                    <td>{{ $selisih >= 0 ? rupiah($selisih) : '(' . rupiah(abs($selisih)) . ')' }}</td>
                </tr>
            @endforeach
        </body>
    </table>
</body>

</html>
