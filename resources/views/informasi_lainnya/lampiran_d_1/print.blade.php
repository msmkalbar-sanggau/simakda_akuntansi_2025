<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran D.1</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
        }

        .bordered {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .bordered th,
        .bordered td {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered td:nth-child(n+6) {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .kanan {
            text-align: right;
        }
    </style>
</head>

<body>
    <div style="clear: both;"></div> <br>
    <div style="text-align: center; font-size:14px;">
        <h4 style="margin: 2px 0px;">PEMERINTAH Kabupaten Sanggau</h4>
        <h4 style="margin: 2px 0px;">REKAPITULASI REALISASI BELANJA DAERAH UNTUK KESELARASAN DAN KETERPADUAN URUSAN
            PEMERINTAHAN DAERAH DAN FUNGSI DALAM KERANGKA PENGELOLAAN KEUANGAN NEGARA</h4>
        <h4 style="margin: 2px 0px;">TA {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th rowspan="3" colspan="4">Kode</th>
                <th rowspan="3">Uraian</th>
                <th colspan="8">Kelompok Belanja</th>
            </tr>
            <tr>
                <th colspan="2">Operasi</th>
                <th colspan="2">Modal</th>
                <th colspan="2">Tidak Terduga</th>
                <th colspan="2">Transfer</th>
            </tr>
            <tr>
                <th>Anggaran</th>
                <th>Realisasi</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $key => $value)
                @if ($key != 0 && strlen($value->kode) == 1)
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
                <tr>
                    <td>{{ substr($value->kode, 0, 1) }}</td>
                    <td>{{ substr($value->kode, 1, 2) }}</td>
                    <td>{{ substr($value->kode2, 0, 1) }}</td>
                    <td>{{ substr($value->kode2, 2, 2) }}</td>
                    <td>{{ $value->nama }}</td>
                    <td>{{ rupiah($value->ag_operasi) }}</td>
                    <td>{{ rupiah($value->r_operasi) }}</td>
                    <td>{{ rupiah($value->ag_modal) }}</td>
                    <td>{{ rupiah($value->r_modal) }}</td>
                    <td>{{ rupiah($value->ag_btt) }}</td>
                    <td>{{ rupiah($value->r_btt) }}</td>
                    <td>{{ rupiah($value->ag_transfer) }}</td>
                    <td>{{ rupiah($value->r_transfer) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding: 16px; font-size: 14px;">
        <div style="float: right; text-align: center;">
            {{-- Sungai Raya, {{ tanggal($tgl_ttd) }}<br /> --}}
            Sungai Raya, ........<br />
            BUPATI KUBU RAYA
            <div style="height: 44px;"></div>
            <p style="text-transform:uppercase">{{ $ttd_nm->nama }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>
