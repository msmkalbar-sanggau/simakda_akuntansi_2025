<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Neraca Saldo</title>
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
        NERACA SALDO
    </div> <br>
    <table>
        <tbody>
            <tr>
                <td style="width: 25%;">SKPD</td>
                <td>:</td>
                <td>{{ $skpd->kd_skpd }} - {{ $skpd->nm_skpd }}</td>
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
                <th>Debet</th>
                <th>Kredit</th>
            </tr>
        </thead>

        <body>
            @php
                $debet = 0;
                $kredit = 0;
                $totdebet = 0;
                $totkredit = 0;
            @endphp
            @foreach ($dataNeracaSaldo as $key => $value)
            @php
                $totdebet += $value->debet;
                $totkredit += $value->kredit;
            @endphp
                    <tr>
                        <td style="text-align: left;">{{ $value->kd_rek6 }}</td>
                        <td style="text-align: left;">{{ $value->nm_rek6 }}</td>
                        <td style="text-align: right;"> {{ $value->debet >= 0 ? rupiah($value->debet) : '(' . rupiah(abs($value->debet)) . ')' }}</td>
                        <td style="text-align: right;"> {{ $value->kredit >= 0 ? rupiah($value->kredit) : '(' . rupiah(abs($value->kredit)) . ')' }}</td>
                    </tr>

            @endforeach
                <tr>
                    <td colspan="2" style="text-align:center;"><b>J U M L A H</b></td>
                    <td style="text-align:right;"><b>{{ rupiah($totdebet) }}</b></td>
                    <td style="text-align:right;"><b>{{ rupiah($totkredit) }}</b></td>
                </tr>
        </body>
    </table>
</body>

</html>
