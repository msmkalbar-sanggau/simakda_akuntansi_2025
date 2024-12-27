<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buku Besar</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            font-size: 13px;
        }

        .bordered {
            width: 100%;
            border-collapse: collapse;
        }

        .bordered th,
        .bordered td {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered th {
            background-color: #cccccc;
        }
    </style>
</head>

<body>
    <div style="text-align: center; font-weight: bold;">
        BUKU BESAR
    </div> <br>
    <table>
        <tbody>
            <tr>
                <td style="width: 25%;">SKPD</td>
                <td>:</td>
                <td>{{ $skpd->kd_skpd }} - {{ $skpd->nm_skpd }}</td>
            </tr>
            <tr>
                <td>Rekening</td>
                <td>:</td>
                <td>{{ $dataRekening }}</td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>:</td>
                <td>{{ tanggal($tgl_awal) }} s/d {{ tanggal($tgl_akhir) }}</td>
            </tr>
            <tr>
                <td>Diketahui Pengguna Anggaran</td>
                <td>:</td>
                <td>{{ $ttd->nip }} - {{ $ttd->nama }}</td>
            </tr>
        </tbody>
    </table> <br>
    
    <table class="bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Uraian</th>
                <th>Ref</th>
                <th>Debet</th>
                <th>Kredit</th>
                <th>Saldo</th>
            </tr>
            <tr style="font-weight: bold;">
                <td></td>
                <td align="right">Saldo Awal</td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{ $saldo_awal >= 0 ? rupiah($saldo_awal) : '(' . rupiah(abs($saldo_awal)) . ')' }}</td>
            </tr>
        </thead>
       <body>
            @php
                $debet = 0;
                $kredit = 0;
            @endphp
            @foreach($dataBukuBesar as $key => $value)
            @php
                if ((substr($rekening, 0, 1) == '8') or (substr($rekening, 0, 1) == '5') or (substr($rekening, 0, 2) == '62') or (substr($rekening, 0, 1) == '1')) {
                    $saldo_awal = $saldo_awal + $value->debet - $value->kredit;
                } else {
                    $saldo_awal = $saldo_awal + $value->kredit - $value->debet;
                }
                $debet += $value->debet;
                $kredit += $value->kredit;
            @endphp
            <tr>
                <td style="text-align: center;">{{ tanggal($value->tgl_voucher) }}</td>
                <td style="text-align: left; width: 550px;">{{ $value->ket }}</td>
                <td style="text-align: center;">{{ $value->no_voucher }}</td>
                <td style="text-align: right;">{{ $value->debet >= 0 ? rupiah($value->debet) : '(' . rupiah(abs($value->debet)) . ')' }}</td>
                <td style="text-align: right;">{{ $value->kredit >= 0 ? rupiah($value->kredit) : '(' . rupiah(abs($value->kredit)) . ')' }}</td>
                <td style="text-align: right;">{{ $saldo_awal >= 0 ? rupiah($saldo_awal) : '(' . rupiah(abs($saldo_awal)) . ')' }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td style="text-align: right;"></td>
                <td style="text-align: right;">Jumlah</td>
                <td style="text-align: right;"></td>
                <td style="text-align: right;">{{ $debet >= 0 ? rupiah($debet) : '(' . rupiah(abs($debet)) . ')' }}</td>
                <td style="text-align: right;">{{ $kredit >= 0 ? rupiah($kredit) : '(' . rupiah(abs($kredit)) . ')' }}</td>
                <td style="text-align: right;">{{ $saldo_awal >= 0 ? rupiah($saldo_awal) : '(' . rupiah(abs($saldo_awal)) . ')' }}</td>
            </tr>
       </body>
    </table>
</body>

</html>
