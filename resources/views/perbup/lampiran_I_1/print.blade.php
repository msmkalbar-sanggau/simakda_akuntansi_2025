<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran I.1</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
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

        .bordered td:nth-child(n+14) {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        #header {
            width: 100%;
            padding-left: 810px;
            font-size: 14px;
        }

        #skpd {
            width: 100%;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <table id="header" style="font-family: Bookman Old Style Roman">
        <tr>
            <td>LAMPIRAN I.1<br>
            PERATURAN BUPATI {{ strtoupper($header1->nm_pemda) }} <br>
            NOMOR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAHUN {{ tahun_anggarant() }}<br>
            TENTANG PERTANGGUNGJAWABAN PELAKSANAAN ANGGARAN PENDAPATAN DAN BELANJA <br>
            {{ strtoupper($header1->nm_pemda) }} TAHUN ANGGARAN {{ tahun_anggaran() }}</td>
            </td>
        </tr>
    </table>
    <div style="clear: both;"></div> <br>
    <div style="text-align: center; font-size:14px;">
        <h4 style="margin: 2px 0px;">PEMERINTAH {{ strtoupper($header1->nm_pemda) }}</h4>
        <h4 style="margin: 2px 0px;">PENJABARAN LAPORAN REALISASI ANGGARAN</h4>
        <h4 style="margin: 2px 0px;">TAHUN ANGGARAN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table id="skpd">
        <tbody>
            <tr>
                <td>Urusan Pemerintahan</td>
                <td>:</td>
                <td>{{ $urusan->kd_urusan }}</td>
                <td>{{ $urusan->nm_urusan }}</td>
            </tr>
            <tr>
                <td>Organisasi</td>
                <td>:</td>
                <td>{{ $skpd->kd_skpd }}</td>
                <td>{{ $skpd->nm_skpd }}</td>
            </tr>
        </tbody>
    </table>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th rowspan="2" colspan="12">Kode</th>
                <th rowspan="2">Uraian</th>
                <th colspan="2">Jumlah (Rp)</th>
                <th colspan="2">Bertambah/Berkurang</th>
                <th rowspan="2">Dasar Hukum</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>Anggaran Setelah Perubahan</th>
                <th>Realisasi</th>
                <th>Rp</th>
                <th>%</th>
            </tr>
        </thead>

        <tbody>
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
                    if(substr($value->kode, 33) == '4') {
                        $pendapatan_ang = $value->nilai_ag;
                        $pendapatan_real = $value->nilai_real;
                        $bertambah_berkurang_pendapatan = $pendapatan_ang - $pendapatan_real;
                    }

                    if(substr($value->kode, 33) == '5') {
                        $belanja_ang = $value->nilai_ag;
                        $belanja_real = $value->nilai_real;
                        $bertambah_berkurang_belanja = $belanja_ang - $belanja_real;
                    }

                    if(substr($value->kode, 33) == '61') {
                        $penerimaan_pembiayaan_ang = $value->nilai_ag;
                        $penerimaan_pembiayaan_real = $value->nilai_real;
                        $bertambah_berkurang_penp = $penerimaan_pembiayaan_ang - $penerimaan_pembiayaan_real;
                    }

                    if(substr($value->kode, 33) == '62') {
                        $pengeluaran_pembiayaan_ang = $value->nilai_ag;
                        $pengeluaran_pembiayaan_real = $value->nilai_real;
                        $bertambah_berkurang_pengp = $pengeluaran_pembiayaan_ang - $pengeluaran_pembiayaan_real;
                    }
                @endphp
                <tr>
                    <td>{{ substr($value->kode, 0, 1) }}</td>
                    <td>{{ substr($value->kode, 1, 2) }}</td>
                    <td>{{ substr($value->kode, 3, 22) }}</td>
                    <td>{{ substr($value->kode, 25, 2) }}</td>
                    <td>{{ substr($value->kode, 27, 4) }}</td>
                    <td>{{ substr($value->kode, 31, 2) }}</td>
                    <td>{{ substr($value->kode, 33, 1) }}</td>
                    <td>{{ substr($value->kode, 34, 1) }}</td>
                    <td>{{ substr($value->kode, 35, 2) }}</td>
                    <td>{{ substr($value->kode, 37, 2) }}</td>
                    <td>{{ substr($value->kode, 39, 2) }}</td>
                    <td>{{ substr($value->kode, 41) }}</td>
                    <td>{{ $value->nama }}</td>
                    @if (substr($value->kode, 33) == '6')
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @else
                        <td>{{ rupiah($value->nilai_ag) }}</td>
                        <td>{{ rupiah($value->nilai_real) }}</td>
                        <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                        </td>
                        <td>{{ $value->nilai_ag == 0 ? rupiah(0) : rupiah(($value->nilai_real * 100) / $value->nilai_ag) }}
                        </td>
                    @endif
                    <td></td>
                    <td></td>
                </tr>

                <!-- jumlah pendapatan -->
                @if(in_array(substr($value->kode, 33, 1), ['4']) && ($key + 1 == count($data) || ($key + 1 < count($data) && substr($data[$key + 1]->kode, 33, 1) > '4')))
                    <tr>
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
                        <td>Jumlah Pendapatan</td>
                        <td>{{ rupiah($pendapatan_ang) }}</td>
                        <td>{{ rupiah($pendapatan_real) }}</td>
                        <td>{{ $bertambah_berkurang_pendapatan >= 0 ? rupiah($bertambah_berkurang_pendapatan) : '(' . rupiah(abs($bertambah_berkurang_pendapatan)) . ')' }}
                        </td>
                        <td>{{ $pendapatan_ang == 0 ? rupiah(0) : rupiah(($pendapatan_real * 100) / $pendapatan_ang) }}
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif

                <!-- jumlah belanja -->
                @if(in_array(substr($value->kode, 33, 1), array('5')) && ($key + 1 == count($data) || ($key + 1 < count($data) && substr($data[$key + 1]->kode, 33, 1) > '5') ))
                <tr>
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
                    <td>Jumlah Belanja</td>
                    <td>{{ rupiah($belanja_ang) }}</td>
                    <td>{{ rupiah($belanja_real) }}</td>
                    <td>{{ $bertambah_berkurang_belanja >= 0 ? rupiah($bertambah_berkurang_belanja) : '(' . rupiah(abs($bertambah_berkurang_belanja)) . ')'  }}</td>
                    <td>{{ $belanja_ang == 0 ? rupiah(0) : rupiah($belanja_real * 100 / $belanja_ang) }}</td>
                    <td></td>
                    <td></td>
                </tr>
                @endif

                <!-- Jumlah Penerimaan Pembiayaan -->
                @if(in_array(substr($value->kode, 33, 2), array('61')) && ($key + 1 == count($data) || ($key + 1 < count($data) && substr($data[$key + 1]->kode, 33, 2) > '61') ))
                <tr>
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
                    <td>Jumlah Penerimaan Pembiayaan</td>
                    <td>{{ rupiah($penerimaan_pembiayaan_ang) }}</td>
                    <td>{{ rupiah($penerimaan_pembiayaan_real) }}</td>
                    <td>{{ $bertambah_berkurang_penp >= 0 ? rupiah($bertambah_berkurang_penp) : '(' . rupiah(abs($bertambah_berkurang_penp)) . ')'  }}</td>
                    <td>{{ $penerimaan_pembiayaan_ang == 0 ? rupiah(0) : rupiah($penerimaan_pembiayaan_real * 100 / $penerimaan_pembiayaan_ang) }}</td>
                    <td></td>
                    <td></td>
                </tr>
                @endif

                <!-- Jumlah pengeluaran Pembiayaan -->
                @if(in_array(substr($value->kode, 33, 2), array('62')) && ($key + 1 == count($data) || ($key + 1 < count($data) && substr($data[$key + 1]->kode, 33, 2) > '62') ))
                <tr>
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
                    <td>Jumlah Pengeluaran Pembiayaan</td>
                    <td>{{ rupiah($pengeluaran_pembiayaan_ang) }}</td>
                    <td>{{ rupiah($pengeluaran_pembiayaan_real) }}</td>
                    <td>{{ $bertambah_berkurang_pengp >= 0 ? rupiah($bertambah_berkurang_pengp) : '(' . rupiah(abs($bertambah_berkurang_pengp)) . ')'  }}</td>
                    <td>{{ $pengeluaran_pembiayaan_ang == 0 ? rupiah(0) : rupiah($pengeluaran_pembiayaan_real * 100 / $pengeluaran_pembiayaan_ang) }}</td>
                    <td></td>
                    <td></td>
                </tr>
                @endif

                <!-- tabel kosong -->
                @if(strlen($value->kode) == 34 || (strlen($value->kode) == 45 && ($key + 1 == count($data) || ($key + 1 < count($data) && strlen($data[$key + 1]->kode) != 45) )))
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif

                <!-- surplus defisit -->
                @if(in_array(substr($value->kode, 33, 1), array('4', '5')) && ($key + 1 == count($data) || ($key + 1 < count($data) && substr($data[$key + 1]->kode, 33, 1) == '6') ))
                    @php
                        $surplus_ang = $pendapatan_ang - $belanja_ang;
                        $surplus_real = $pendapatan_real - $belanja_real;
                        $bertambah_berkurang_surplus = $surplus_ang - $surplus_real;
                    @endphp
                <tr>
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
                    <td>Surplus/Defisit</td>
                    <td>{{ $surplus_ang >= 0 ? rupiah($surplus_ang) : '(' . rupiah(abs($surplus_ang)) . ')'  }}</td>
                    <td>{{ $surplus_real >= 0 ? rupiah($surplus_real) : '(' . rupiah(abs($surplus_real)) . ')'  }}</td>
                    <td>{{ $bertambah_berkurang_surplus >= 0 ? rupiah($bertambah_berkurang_surplus) : '(' . rupiah(abs($bertambah_berkurang_surplus)) . ')'  }}</td>
                    <td>{{ $surplus_ang == 0 ? rupiah(0) : rupiah($surplus_real * 100 / $surplus_ang) }}</td>
                    <td></td>
                    <td></td>
                </tr>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif

                <!-- pembiayaan netto -->
                @if(substr($value->kode, 33, 1) == '6' && $key + 1 == count($data))
                    @php
                        $netto_ang = $penerimaan_pembiayaan_ang - $pengeluaran_pembiayaan_ang;
                        $netto_real = $penerimaan_pembiayaan_real - $pengeluaran_pembiayaan_real;
                        $bertambah_berkurang_netto = $netto_ang - $netto_real;
                    @endphp
                <tr>
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
                    <td>Pembiayaan Netto</td>
                    <td>{{ $netto_ang >= 0 ? rupiah($netto_ang) : '(' . rupiah(abs($netto_ang)) . ')'  }}</td>
                    <td>{{ $netto_real >= 0 ? rupiah($netto_real) : '(' . rupiah(abs($netto_real)) . ')'  }}</td>
                    <td>{{ $bertambah_berkurang_netto >= 0 ? rupiah($bertambah_berkurang_netto) : '(' . rupiah(abs($bertambah_berkurang_netto)) . ')'  }}</td>
                    <td>{{ $netto_ang == 0 ? rupiah(0) : rupiah($netto_real * 100 / $netto_ang) }}</td>
                    <td></td>
                    <td></td>
                </tr>
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
                    <td></td>
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
                <td>Sisa lebih pembiayaan anggaran tahun berkenaan (SILPA)</td>
                <td>{{ $silpa_ang >= 0 ? rupiah($silpa_ang) : '(' . rupiah(abs($silpa_ang)) . ')'  }}</td>
                <td>{{ $silpa_real >= 0 ? rupiah($silpa_real) : '(' . rupiah(abs($silpa_real)) . ')'  }}</td>
                <td>{{ $bertambah_berkurang_silpa >= 0 ? rupiah($bertambah_berkurang_silpa) : '(' . rupiah(abs($bertambah_berkurang_silpa)) . ')'  }}</td>
                <td>{{ $silpa_ang == 0 ? rupiah(0) : rupiah($silpa_real * 100 / $silpa_ang) }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    @if($skpd->kd_skpd == '7.01.0.00.0.00.07.0000' || $skpd->kd_skpd == '7.01.0.00.0.00.10.0000')
    <div style="height: 100px"></div>
    @endif
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
