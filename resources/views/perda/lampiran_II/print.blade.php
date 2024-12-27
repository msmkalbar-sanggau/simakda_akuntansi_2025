<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran II</title>
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
                <td>Lampiran II</td>
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
        <h4 style="margin: 2px 0px;">PEMERINTAH {{ strtoupper($header1->nm_pemda) }}</h4>
        <h4 style="margin: 2px 0px;">LAPORAN PERUBAHAN SALDO ANGGARAN LEBIH</h4>
        <h4 style="margin: 2px 0px;">TAHUN ANGGARAN {{ tahun_anggaran() }}</h4>
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
            @foreach($data as $value)
                @switch($value->nor)
                    @case('1')
                    <tr>
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $kas_lalu_thn->nilai >= 0 ? rupiah($kas_lalu_thn->nilai) : '(' . rupiah(abs($kas_lalu_thn->nilai)) . ')'  }}</td>
                        <td class="kanan">{{ $kas_lalu_thns->nilai >= 0 ? rupiah($kas_lalu_thns->nilai) : '(' . rupiah(abs($kas_lalu_thns->nilai)) . ')'  }}</td>
                    </tr>
                    @break

                    @case('2')
                    <tr>
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $lain1->nilai >= 0 ? rupiah($lain1->nilai) : '(' . rupiah(abs($lain1->nilai)) . ')'  }}</td>
                        <td class="kanan">{{ $lain_s1->nilai >= 0 ? rupiah($lain_s1->nilai) : '(' . rupiah(abs($lain_s1->nilai)) . ')'  }}</td>
                    </tr>
                    @break

                    @case('3')
                    @php
                        $sub = $kas_lalu_thn->nilai + $lain1->nilai;
                        $sub_s = $kas_lalu_thns->nilai + $lain_s1->nilai;
                    @endphp
                    <tr>
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $sub >= 0 ? rupiah($sub) : '(' . rupiah(abs($sub)) . ')'  }}</td>
                        <td class="kanan">{{ $sub_s >= 0 ? rupiah($sub_s) : '(' . rupiah(abs($sub_s)) . ')'  }}</td>
                    </tr>
                    @break

                    @case('4')
                    <tr>
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $saldoAKhir >= 0 ? rupiah($saldoAKhir) : '(' . rupiah(abs($saldoAKhir)) . ')'  }}</td>
                        <td class="kanan">{{ $saldoAKhirS >= 0 ? rupiah($saldoAKhirS) : '(' . rupiah(abs($saldoAKhirS)) . ')'  }}</td>
                    </tr>
                    @break

                    @case('5')
                    <tr>
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $silpaAkhir >= 0 ? rupiah($silpaAkhir) : '(' . rupiah(abs($silpaAkhir)) . ')'  }}</td>
                        <td class="kanan">{{ $silpaAkhirS >= 0 ? rupiah($silpaAkhirS) : '(' . rupiah(abs($silpaAkhirS)) . ')'  }}</td>
                    </tr>
                    @break

                    @case('6')
                    <tr>
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $lainAkhir >= 0 ? rupiah($lainAkhir) : '(' . rupiah(abs($lainAkhir)) . ')'  }}</td>
                        <td class="kanan">{{ $lainAkhirS >= 0 ? rupiah($lainAkhirS) : '(' . rupiah(abs($lainAkhirS)) . ')'  }}</td>
                    </tr>
                    @break

                    @case('7')
                    <tr>
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ rupiah($koreksi) }}</td>
                        <td class="kanan">{{ rupiah($koreksiS) }}</td>
                    </tr>
                    @break

                    @default
                    <tr>
                        <td>{{ $value->uraian }}</td>
                        <td class="kanan">{{ $saldoAKhir >= 0 ? rupiah($saldoAKhir) : '(' . rupiah(abs($saldoAKhir)) . ')'  }}</td>
                        <td class="kanan">{{ $saldoAKhirS >= 0 ? rupiah($saldoAKhirS) : '(' . rupiah(abs($saldoAKhirS)) . ')'  }}</td>
                    </tr>
                    @break
                @endswitch
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
