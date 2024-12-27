<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cetak Jurnal</title>
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

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        <h4 style="margin: 2px 0px;">PEMERINTAH {{ strtoupper($header1->nm_pemda) }}</h4>
        <h4 style="margin: 2px 0px; text-transform: uppercase;">DINAS {{ $skpd->nm_skpd }}</h4>
        <h4 style="margin: 2px 0px;">JURNAL UMUM</h4>
        <h4 style="margin: 2px 0px; text-transform: uppercase;">PERIODE {{ tanggal($tgl_awal) }} s/d {{ tanggal($tgl_akhir) }}</h4>
        <div style="clear: both;"></div>
    </div> <br>

    <table class="bordered">
        <thead>
            <tr>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">Nomor<br>Bukti</th>
                <th rowspan="2" colspan="6">Kode Rekening</th>
                <th rowspan="2">uraian</th>
                <th rowspan="2">Ref</th>
                <th colspan="2">Jumlah Rp</th>
            </tr>
            <tr>
                <th>Debit</th>
                <th>Kredit</th>
            </tr>
            <tr>
                <td class="center">1</td>
                <td class="center">2</td>
                <td class="center" colspan="6">3</td>
                <td class="center">4</td>
                <td class="center"></td>
                <td class="center">5</td>
                <td class="center">6</td>
            </tr>
        </thead>
       <body>
        @php
            $dataV = '';
        @endphp
        @foreach($data as $key => $value)
            @if($total->tot == $key+1)
                <tr>
                    <td style="border-bottom: none; border-top:none"></td>
                    <td style="border-bottom: none; border-top:none"></td>
                    <td style="border-bottom: none;">{{ substr($value->kd_rek6, 0, 1) }}</td>
                    <td style="border-bottom: none;">{{ substr($value->kd_rek6, 1, 1) }}</td>
                    <td style="border-bottom: none;">{{ substr($value->kd_rek6, 2, 2) }}</td>
                    <td style="border-bottom: none;">{{ substr($value->kd_rek6, 4, 2) }}</td>
                    <td style="border-bottom: none;">{{ substr($value->kd_rek6, 6, 2) }}</td>
                    <td style="border-bottom: none;">{{ substr($value->kd_rek6, 8, 4) }}</td>
                    <td style="border-bottom: none;">{{ $value->nm_rek6 }}</td>
                    <td style="border-bottom: none;"></td>
                    @if($value->rk == 'K')
                        <td style="border-bottom: none; text-align: right">{{ rupiah(0) }}</td>
                        <td style="border-bottom: none; text-align: right">{{ $value->kredit >= 0 ? rupiah($value->kredit) : '(' . rupiah(abs($value->kredit)) . ')' }}</td>
                    @else
                        <td style="border-bottom: none; text-align: right">{{ $value->debet >= 0 ? rupiah($value->debet) : '(' . rupiah(abs($value->debet)) . ')' }}</td>
                        <td style="border-bottom: none; text-align: right">{{ rupiah(0) }}</td>
                    @endif
                </tr>
            @else
                @if($dataV == $value->no_voucher)
                    <tr>
                        <td style="border-bottom: none; border-top:none;"></td>
                        <td style="border-bottom: none; border-top:none"></td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 0, 1) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 1, 1) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 2, 2) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 4, 2) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 6, 2) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 8, 4) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ $value->nm_rek6 }}</td>
                        <td style="border-bottom: none; border: 1px solid black;"></td>
                        @if($value->rk == 'K')
                            <td style="border-bottom: none; border: 1px solid black; text-align: right">{{ rupiah(0) }}</td>
                            <td style="border-bottom: none; border: 1px solid black; text-align: right">{{ $value->kredit >= 0 ? rupiah($value->kredit) : '(' . rupiah(abs($value->kredit)) . ')' }}</td>
                        @else
                            <td style="border-bottom: none; border: 1px solid black; text-align: right">{{ $value->debet >= 0 ? rupiah($value->debet) : '(' . rupiah(abs($value->debet)) . ')' }}</td>
                            <td style="border-bottom: none; border: 1px solid black; text-align: right">{{ rupiah(0) }}</td>
                        @endif
                    </tr>
                @else
                    <tr>
                        <td style="border-bottom: none; border-top:none; border-top: 1px solid black;">{{ tanggal($value->tgl_voucher) }}</td>
                        <td style="border-bottom: none; border-top:none; border-top: 1px solid black;">{{ $value->no_voucher }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 0, 1) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 1, 1) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 2, 2) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 4, 2) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 6, 2) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ substr($value->kd_rek6, 8, 4) }}</td>
                        <td style="border-bottom: none; border: 1px solid black;">{{ $value->nm_rek6 }}</td>
                        <td style="border-bottom: none; border: 1px solid black;"></td>
                        @if($value->rk == 'K')
                            <td style="border-bottom: none; border: 1px solid black; text-align: right">{{ rupiah(0) }}</td>
                            <td style="border-bottom: none; border: 1px solid black; text-align: right">{{ $value->kredit >= 0 ? rupiah($value->kredit) : '(' . rupiah(abs($value->kredit)) . ')' }}</td>
                        @else
                            <td style="border-bottom: none; border: 1px solid black; text-align: right">{{ $value->debet >= 0 ? rupiah($value->debet) : '(' . rupiah(abs($value->debet)) . ')' }}</td>
                            <td style="border-bottom: none; border: 1px solid black; text-align: right">{{ rupiah(0) }}</td>
                        @endif
                    </tr>
                @endif
            @endif
            @php
                $dataV = $value->no_voucher;
            @endphp
        @endforeach
        <tr>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
            <td style="border-top:none"></td>
        </tr>
       </body>
    </table>
</body>

</html>
