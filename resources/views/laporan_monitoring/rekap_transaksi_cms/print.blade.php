<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekap Transaksi CMS</title>
    <style>
        body {
            font-size: 12px;
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

        .bordered td:nth-child(n+4) {
            text-align: right;
        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        <h4 style="margin: 2px 0px;">PEMERINTAH {{ strtoupper($header->nm_pemda) }}</h4>
        <h4 style="margin: 2px 0px; text-transform: uppercase;">DINAS {{ $skpd->nm_skpd }}</h4>
        <h4 style="margin: 2px 0px;">TAHUN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>

    <table class="bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Sub Kegiatan</th>
                <th>Kode Belanja</th>
                <th>Jumlah</th>
            </tr>
        </thead>
       <body>
        @php
            $total = 0;
        @endphp
        @foreach($data as $value)
            @php
                $total += $value->total;
            @endphp
            <tr>
                <td>{{ tanggal($value->tgl_voucher) }}</td>
                <td>{{ $value->kd_sub_kegiatan }}</td>
                <td>{{ $value->kd_rek6 }}</td>
                <td>{{ $value->total >= 0 ? rupiah($value->total) : '(' . rupiah(abs($value->total)) . ')' }}</td>
            </tr>
        @endforeach
        <tr style="background-color: #cccccc">
            <td colspan="3" style="text-align: center; font-weight:bold">Jumlah</td>
            <td style="text-align: right; font-weight: bold">{{ $total >= 0 ? rupiah($total) : '(' . rupiah(abs($total)) . ')' }}</td>
        </tr>
       </body>
    </table>
</body>

</html>
