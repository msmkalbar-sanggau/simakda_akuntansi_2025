<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LRA KESELARASAN</title>
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
        <h4 style="margin: 8px 0px;">PEMERINTAH {{ strtoupper($header->nm_pemda) }}</h4>
        <h4 style="margin: 8px 0px;">REKAPITULASI REALISASI BELANJA DAERAH UNTUK</h4>
        <h4 style="margin: 8px 0px;">KESELARASAN DAN KETERPADUAN URUSAN PEMERINTAH DAERAH</h4>
        <h4 style="margin: 8px 0px;">DAN FUNGSI DALAM KERANGKA PENGELOLAAN KEUANGAN NEGARA</h4>
        <h4 style="margin: 8px 0px;">TAHUN ANGGARAN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div>
    <table class="bordered">
        <thead>
            <tr>
                <th rowspan="2">KODE</th>
                <th rowspan="2">URAIAN</th>
                <th rowspan="2">ANGGARAN</th>
                <th rowspan="2">REALISASI</th>
                <th colspan="2">BERTAMBAH/BERKURANG</th>
            </tr>
            <tr>
                <th>(Rp)</th>
                <th>(%)</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
            </tr>
        </thead>

        <body>
            @php
                $total_ang = 0;
                $total_rea = 0;
                $total_ang_rea = 0;
            @endphp
            @foreach ($data as $key => $value)
                @php
                    if (strlen($value->kode) == '1') {
                        $total_ang += $value->anggaran;
                        $total_rea += $value->realisasi;
                        $total_ang_rea = $total_ang - $total_rea;
                    }
                @endphp
                @switch(strlen($value->kode))
                    @case(1)
                        <tr>
                            <td><b>{{ $value->kode }}</b></td>
                            <td><b>{{ $value->nama }}</b></td>
                            <td><b>{{ rupiah($value->anggaran) }}</b></td>
                            <td><b>{{ rupiah($value->realisasi) }}</b></td>
                            <td><b>{{ $value->selisih >= 0 ? rupiah($value->selisih) : '(' . rupiah(abs($value->selisih)) . ')' }}</b>
                            </td>
                            <td><b>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->realisasi * 100) / $value->anggaran) }}</b>
                            </td>
                        </tr>
                    @break

                    @default
                        <tr>
                            <td>{{ $value->kode }}</td>
                            <td>{{ $value->nama }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->realisasi) }}</td>
                            <td>{{ $value->selisih >= 0 ? rupiah($value->selisih) : '(' . rupiah(abs($value->selisih)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->realisasi * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                @endswitch
            @endforeach
            <tr>
                <td style="text-align: center" colspan="2"><b>TOTAL</b></td>
                <td style="text-align: right"><b>{{ rupiah($total_ang) }}</b></td>
                <td style="text-align: right"><b>{{ rupiah($total_rea) }}</b></td>
                <td style="text-align: right">
                    <b>{{ $total_ang_rea >= 0 ? rupiah($total_ang_rea) : '(' . rupiah(abs($total_ang_rea)) . ')' }}</b>
                </td>
                <td style="text-align: right">
                    <b>{{ $total_ang == 0 ? rupiah(0) : rupiah(($total_rea * 100) / $total_ang) }}</b>
                </td>
            </tr>
        </body>
    </table>

</body>

</html>
