<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran D.4</title>
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

        .bordered td:nth-child(n+3) {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .kanan {
            text-align: right;
        }
        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="clear: both;"></div> <br>
    <div style="text-align: center; font-size:14px;">
        <h4 style="margin: 2px 0px;">PEMERINTAH Kabupaten Sanggau</h4>
        <h4 style="margin: 2px 0px;">RINGKASAN REALISASI PENJABARAN APBD YANG DIKLASIFIKASI MENURUT KELOMPOK, JENIS, OBJEK, RINCIAN OBJEK, SUB RINCIAN OBJEK, PENDAPATAN, BELANJA, DAN PEMBIAYAAN </h4>
        <h4 style="margin: 2px 0px;">TA {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Uraian</th>
                <th colspan="2">Jumlah (Rp)</th>
                <th colspan="2">Bertambah/Berkurang</th>
            </tr>
            <tr>
                <th>Anggaran Setelah Perubahan</th>
                <th>Realisasi</th>
                <th>Rp</th>
                <th>%</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @php
                $surplus_ang = 0;
                $surplus_real = 0;
                $netto_ang = 0;
                $netto_real = 0;
                $pendapatan_ang = 0;
                $pendapatan_real = 0;
                $belanja_ang = 0;
                $belanja_real = 0;
                $penerimaan_pembiayaan_ang = 0;
                $penerimaan_pembiayaan_real = 0;
                $pengeluaran_pembiayaan_ang = 0;
                $pengeluaran_pembiayaan_real = 0;
            @endphp
            @foreach ($data as $key => $value)
                @php
                    $bertambah_berkurang = $value->nilai_ag - $value->nilai_real;
                    if($value->kode == '4') {
                        $pendapatan_ang = $value->nilai_ag;
                        $pendapatan_real = $value->nilai_real;
                    }

                    if($value->kode == '5') {
                        $belanja_ang = $value->nilai_ag;
                        $belanja_real = $value->nilai_real;
                    }

                    if($value->kode == '61') {
                        $penerimaan_pembiayaan_ang = $value->nilai_ag;
                        $penerimaan_pembiayaan_real = $value->nilai_real;
                    }

                    if($value->kode == '62') {
                        $pengeluaran_pembiayaan_ang = $value->nilai_ag;
                        $pengeluaran_pembiayaan_real = $value->nilai_real;
                    }
                @endphp
                <tr>
                    <td>{{ $kd_rek_separator($value->kode) }}</td>
                    <td>{{ $value->nama }}</td>
                    @if($value->kode == '6' || $value->kode == '')
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>&nbsp;</td>
                    @else
                        <td>{{ rupiah($value->nilai_ag) }}</td>
                        <td>{{ rupiah($value->nilai_real) }}</td>
                        <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                        </td>
                        <td>{{ $value->nilai_ag == 0 ? rupiah(0) : rupiah(($value->nilai_real * 100) / $value->nilai_ag) }}
                        </td>
                    @endif
                </tr>

                <!-- tabel kosong -->
                @if(strlen($value->kode) == 1 || strlen($value->kode) == 12 && ($key + 1 == count($data) || ($key + 1 < count($data) && substr($value->kode, 0, 4) != substr($data[$key + 1]->kode, 0, 4))))
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif

                <!-- surplus/defisit -->
                @if((substr($value->kode, 0, 1) == '5' && ($key + 1 == count($data) || ($key + 1 < count($data) && substr($data[$key + 1]->kode, 0, 1) != '5'))) ||
                (substr($value->kode, 0, 1) == '4' && ($key + 1 == count($data) || ($key + 1 < count($data) && !in_array(substr($data[$key + 1]->kode, 0, 1), array('4', '5'))))))
                    @php
                        $surplus_ang = $pendapatan_ang - $belanja_ang;
                        $surplus_real = $pendapatan_real - $belanja_real;
                        $bertambah_berkurang_surplus = $surplus_ang - $surplus_real;
                    @endphp
                <tr>
                    <td></td>
                    <td>Surplus/Defisit</td>
                    <td>{{ $surplus_ang >= 0 ? rupiah($surplus_ang) : '(' . rupiah(abs($surplus_ang)) . ')'  }}</td>
                    <td>{{ $surplus_real >= 0 ? rupiah($surplus_real) : '(' . rupiah(abs($surplus_real)) . ')'  }}</td>
                    <td>{{ $bertambah_berkurang_surplus >= 0 ? rupiah($bertambah_berkurang_surplus) : '(' . rupiah(abs($bertambah_berkurang_surplus)) . ')'  }}</td>
                    <td>{{ $surplus_ang == 0 ? rupiah(0) : rupiah($surplus_real * 100 / $surplus_ang) }}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif

                <!-- pembiayaan netto -->
                @if(substr($value->kode, 0, 1) == '6' && ($key + 1 == count($data) || ($key + 1 < count($data) && substr($data[$key + 1]->kode, 0, 1) != '6')))
                    @php
                        $netto_ang = $penerimaan_pembiayaan_ang - $pengeluaran_pembiayaan_ang;
                        $netto_real = $penerimaan_pembiayaan_real - $pengeluaran_pembiayaan_real;
                        $bertambah_berkurang_netto = $netto_ang - $netto_real;
                    @endphp
                <tr>
                    <td></td>
                    <td>Pembiayaan Netto</td>
                    <td>{{ $netto_ang >= 0 ? rupiah($netto_ang) : '(' . rupiah(abs($netto_ang)) . ')'  }}</td>
                    <td>{{ $netto_real >= 0 ? rupiah($netto_real) : '(' . rupiah(abs($netto_real)) . ')'  }}</td>
                    <td>{{ $bertambah_berkurang_netto >= 0 ? rupiah($bertambah_berkurang_netto) : '(' . rupiah(abs($bertambah_berkurang_netto)) . ')'  }}</td>
                    <td>{{ $netto_ang == 0 ? rupiah(0) : rupiah($netto_real * 100 / $netto_ang) }}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                @endif
            @endforeach
            <!-- silpa -->
            @php
                $silpa_ang = $surplus_ang + $netto_ang;
                $silpa_real = $surplus_real + $netto_real;
                $bertambah_berkurang_silpa = $silpa_ang - $silpa_real;
            @endphp
            <tr>

                <td></td>
                <td>Sisa lebih pembiayaan anggaran tahun berkenaan (SILPA)</td>
                <td>{{ $silpa_ang >= 0 ? rupiah($silpa_ang) : '(' . rupiah(abs($silpa_ang)) . ')'  }}</td>
                <td>{{ $silpa_real >= 0 ? rupiah($silpa_real) : '(' . rupiah(abs($silpa_real)) . ')'  }}</td>
                <td>{{ $bertambah_berkurang_silpa >= 0 ? rupiah($bertambah_berkurang_silpa) : '(' . rupiah(abs($bertambah_berkurang_silpa)) . ')'  }}</td>
                <td>{{ $silpa_ang == 0 ? rupiah(0) : rupiah($silpa_real * 100 / $silpa_ang) }}</td>
            </tr>
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
