<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran IV</title>
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

        .bordered th {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered td {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .bordered tr:last-child td {
            border-bottom: 1px solid black;
        }

        .bordered td:nth-child(n+5) {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .kanan {
            text-align: right;
        }

        #header {
            width: 100%;
            padding-left: 820px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <table id="header">
        <tbody>
            <tr>
                <td>Lampiran IV</td>
                <td>:</td>
                <td>Rancangan Peraturan Daerah {{ $daerah->daerah }}</td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div style="clear: both;"></div> <br>
    <div style="text-align: center; font-size:14px;">
        <h4 style="margin: 2px 0px;">PEMERINTAH {{ $header1->nm_pemda }}</h4>
        <h4 style="margin: 2px 0px;">LAPORAN PERUBAHAN EKUITAS</h4>
        <h4 style="margin: 2px 0px;">UNTUK TAHUN YANG BERAKHIR SAMPAI DENGAN 31 DESEMBER {{ tahun_anggaran() }} DAN
            {{ tahun_anggarans() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th bgcolor="#CCCCCC">Uraian</th>
                <th bgcolor="#CCCCCC">{{ tahun_anggaran() }}</th>
                <th bgcolor="#CCCCCC">{{ tahun_anggarans() }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $value)
            <tr>
                @switch($value->nor)
                    @case('1')
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $saldo_awal >= 0 ? rupiah($saldo_awal) : '(' . rupiah(abs($saldo_awal)) . ')'  }}</td>
                        <td class="kanan">{{ $saldo_awal_lalu >= 0 ? rupiah($saldo_awal_lalu) : '(' . rupiah(abs($saldo_awal_lalu)) . ')'  }}</td>
                    @break

                    @case('2')
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $surplus >= 0 ? rupiah($surplus) : '(' . rupiah(abs($surplus)) . ')'  }}</td>
                        <td class="kanan">{{ $surplus_lalu >= 0 ? rupiah($surplus_lalu) : '(' . rupiah(abs($surplus_lalu)) . ')'  }}</td>
                    @break

                    @case('3')
                        <td>{{ $value->uraian }}</td>
                        <td></td>
                        <td></td>
                    @break

                    @case('4')
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $lpe->knp >= 0 ? rupiah($lpe->knp) : '(' . rupiah(abs($lpe->knp)) . ')'  }}</td>
                        <td class="kanan">{{ $lpe_lalu->knp >= 0 ? rupiah($lpe_lalu->knp) : '(' . rupiah(abs($lpe_lalu->knp)) . ')'  }}</td>
                    @break

                    @case('5')
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $lpe->srat >= 0 ? rupiah($lpe->srat) : '(' . rupiah(abs($lpe->srat)) . ')'  }}</td>
                        <td class="kanan">{{ $lpe_lalu->srat >= 0 ? rupiah($lpe_lalu->srat) : '(' . rupiah(abs($lpe_lalu->srat)) . ')'  }}</td>
                    @break

                    @case('6')
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $lpe->ll >= 0 ? rupiah($lpe->ll) : '(' . rupiah(abs($lpe->ll)) . ')'  }}</td>
                        <td class="kanan">{{ $lpe_lalu->ll >= 0 ? rupiah($lpe_lalu->ll) : '(' . rupiah(abs($lpe_lalu->ll)) . ')'  }}</td>
                    @break

                    @default
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $eku >= 0 ? rupiah($eku) : '(' . rupiah(abs($eku)) . ')'  }}</td>
                        <td class="kanan">{{ $eku_lalu >= 0 ? rupiah($eku_lalu) : '(' . rupiah(abs($eku_lalu)) . ')'  }}</td>
                    @break
                    @endswitch
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding: 16px; font-size: 14px;">
        <div style="float: right; text-align: center;">
            {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}<br />
            {{-- Sungai Raya, ........<br /> --}}
            {{ $ttd_nm->jabatan }}
            <div style="height: 44px;"></div>
            <p style="text-transform:uppercase">{{ $ttd_nm->nama }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>
