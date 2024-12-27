<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekap Buku Besar</title>
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
        REKAP BUKU BESAR
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
        </tbody>
    </table> <br>

    <table class="bordered">
        <thead>
            <tr>
                <th>Kode Rekening</th>
                <th>Nama Rekening</th>
                <th>Saldo Awal</th>
                <th>Debet</th>
                <th>Kredit</th>
                <th>Saldo</th>
            </tr>
        </thead>

        <body>
            @php
                $debet = 0;
                $kredit = 0;
            @endphp
            @foreach ($dataBukuBesar as $key => $value)
            @php
                if ((substr($value->kd_rek, 0, 1) == '8') or (substr($value->kd_rek, 0, 1) == '5') or (substr($value->kd_rek, 0, 2) == '62') or (substr($value->kd_rek, 0, 1) == '1')) {
                    $saldo_awal = $value->debet_lalu - $value->kredit_lalu;
                    $saldo = $value->debet_lalu - $value->kredit_lalu + $value->debet - $value->kredit;
                } else {
                    $saldo_awal = $value->kredit_lalu - $value->debet_lalu;
                    $saldo = $value->kredit_lalu - $value->debet_lalu + $value->kredit - $value->debet;
                }
            @endphp
                <tr>
                    <td style="text-align: center;">{{ $value->kd_rek }}</td>
                    <td style="text-align: center;">{{ $value->nm_rek }}</td>
                    <td style="text-align: right;">
                        {{ $saldo_awal >= 0 ? rupiah($saldo_awal) : '(' . rupiah(abs($saldo_awal)) . ')' }}</td>
                    <td style="text-align: right;">
                        {{ $value->debet >= 0 ? rupiah($value->debet) : '(' . rupiah(abs($value->debet)) . ')' }}</td>
                    <td style="text-align: right;">
                        {{ $value->kredit >= 0 ? rupiah($value->kredit) : '(' . rupiah(abs($value->kredit)) . ')' }}</td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ $saldo >= 0 ? rupiah($saldo) : '(' . rupiah(abs($saldo)) . ')' }}</td>
                </tr>
            @endforeach
        </body>
    </table>
</body>

</html>
