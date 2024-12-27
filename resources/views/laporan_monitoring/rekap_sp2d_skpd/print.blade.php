<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $dataHeader }}</title>
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
            src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75" height="100" />
        {{--  <img style="width: 84px; float: left; margin: 8px;"
            src="{{ asset('template/assets/images/kuburaya-hitamputih.png') }}" alt="">  --}}
        <h4 style="margin: 8px 0px;">PEMERINTAH {{ strtoupper($header->nm_pemda) }}</h4>
        <h4 style="margin: 8px 0px;">{{ $dataHeader }}</h4>
        <h4 style="margin: 8px 0px;">{{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div>
    <table class="bordered">
        <thead>
            <tr>
                <th>Kode SKPD</th>
                <th>Nama SKPD</th>
                <th>No. SP2D</th>
                <th>Tanggal SP2D</th>
                <th>Nilai</th>
            </tr>
        </thead>
       <body>
        @php
            $totalall = 0;
        @endphp
        @foreach($data as $value)
            @php
                $totalall += $value->nilai;
            @endphp
            <tr>
                <td>{{ $value->kd_skpd }}</td>
                <td>{{ $value->nm_skpd }}</td>
                <td>{{ $value->no_sp2d }}</td>
                <td>{{ tanggal($value->tgl_sp2d) }}</td>
                <td>{{ $value->nilai >= 0 ? rupiah($value->nilai) : '(' . rupiah(abs($value->nilai)) . ')' }}</td>
            </tr>
        @endforeach
            <tr>
                <td colspan="4"><b><center>J U M L A H</center></b></td>
                <td align="right">{{ rupiah($totalall) }}</td>
            </tr>
       </body>
    </table>
</body>

</html>
