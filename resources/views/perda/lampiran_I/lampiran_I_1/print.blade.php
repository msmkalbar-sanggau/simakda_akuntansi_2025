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
            margin-top: 10px;
        }

        .bordered th,
        .bordered td {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered td:nth-child(n+5) {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        #header {
            width: 100%;
            padding-left: 820px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <table id="header" style="font-family: Bookman Old Style Roman">
        <tr>
            <td>LAMPIRAN I.1<br>
            PERATURAN DAERAH {{ strtoupper($header1->nm_pemda) }} <br>
            NOMOR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAHUN {{ tahun_anggarant() }}<br>
            TENTANG PERTANGGUNGJAWABAN PELAKSANAAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH <br>
            {{ strtoupper($header1->nm_pemda) }} TAHUN ANGGARAN {{ tahun_anggaran() }}</td>
            </td>
        </tr>
    </table>
    <div style="clear: both;"></div> <br>
    <div style="text-align: center; font-size:14px;">
        <h4 style="margin: 2px 0px;">PEMERINTAH {{ strtoupper($header1->nm_pemda) }}</h4>
        <h4 style="margin: 2px 0px;">RINGKASAN LAPORAN REALISASI ANGGARAN MENURUT URUSAN PEMERINTAHAN DAERAH DAN
            ORGANISASI</h4>
        <h4 style="margin: 2px 0px;">TAHUN ANGGARAN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th colspan="3" rowspan="2">Kode</th>
                <th rowspan="2">Urusan Pemerintahan Daerah </th>
                <th colspan="2">Jumlah (Rp)</th>
                <th colspan="2">Bertambah/(Berkurang)</th>
            </tr>
            <tr>
                <th>Anggaran Setelah Perubahan</th>
                <th>Realisasi</th>
                <th>(Rp)</th>
                <th>%</th>
            </tr>
        </thead>

        <tbody>
            @php
                $show_belanja = true;
                $show_pendapatan = true;
                $urusan = null;
            @endphp
            @foreach ($data as $value)
                @php
                    $bertambah_berkurang = $value->nilai_ag - $value->nilai_real;
                    $bertambah_berkurang_pendapatan = $pendapatan->nilai_ag - $pendapatan->nilai_real;
                    $bertambah_berkurang_belanja = $belanja->nilai_ag - $belanja->nilai_real;
                @endphp

                <!-- nilai dasar -->
                @if ($value->jenis == 'urusan' && $value->urutan != '41')
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @php
                        $show_pendapatan = false;
                    @endphp
                    <tr class="{{ $value->is_bold ? 'bold' : '' }} ">
                        <td>
                            @if ($value->jenis == 'urusan' || ($value->jenis == 'bidang_urusan' && $urusan != substr($value->kode, 0, 1)))
                                {{ substr($value->kode, 0, 1) }}
                            @endif
                        </td>
                        <td>{{ $value->jenis == 'bidang_urusan' ? substr($value->kode, -2) : '' }}</td>
                        <td>{{ $value->jenis == 'skpd' ? $value->kode : '' }}</td>
                        <td>{{ $value->nama }}</td>
                        @if ($value->jenis == 'urusan')
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
                    </tr>
                @else
                    <tr class="{{ $value->is_bold ? 'bold' : '' }} ">
                        <td>
                            @if ($value->jenis == 'urusan' || ($value->jenis == 'bidang_urusan' && $urusan != substr($value->kode, 0, 1)))
                                {{ substr($value->kode, 0, 1) }}
                            @endif
                        </td>
                        <td>{{ $value->jenis == 'bidang_urusan' ? substr($value->kode, -2) : '' }}</td>
                        <td>{{ $value->jenis == 'skpd' ? $value->kode : '' }}</td>
                        <td>{{ $value->nama }}</td>
                        @if ($value->jenis == 'urusan')
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
                    </tr>
                @endif

                <!-- dapatkan nilai pendapatan -->
                @if ($show_pendapatan && substr($value->urutan, 0, 1) == '4')
                    @php
                        $show_pendapatan = false;
                    @endphp
                    <tr class="bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>PENDAPATAN</td>
                        <td>{{ rupiah($pendapatan->nilai_ag) }}</td>
                        <td>{{ rupiah($pendapatan->nilai_real) }}</td>
                        <td>{{ $bertambah_berkurang_pendapatan >= 0 ? rupiah($bertambah_berkurang_pendapatan) : '(' . rupiah(abs($bertambah_berkurang_pendapatan)) . ')' }}
                        </td>
                        <td>{{ $pendapatan->nilai_ag == 0 ? rupiah(0) : rupiah(($pendapatan->nilai_real * 100) / $pendapatan->nilai_ag) }}
                        </td>
                    </tr>
                @endif

                <!-- dapatkan nilai belanja -->
                @if ($show_belanja && substr($value->urutan, 0, 1) == '5')
                    @php
                        $show_belanja = false;
                    @endphp
                    <tr class="bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Belanja</td>
                        <td>{{ rupiah($belanja->nilai_ag) }}</td>
                        <td>{{ rupiah($belanja->nilai_real) }}</td>
                        <td>{{ $bertambah_berkurang_belanja >= 0 ? rupiah($bertambah_berkurang_belanja) : '(' . rupiah(abs($bertambah_berkurang_belanja)) . ')' }}
                        </td>
                        <td>{{ $belanja->nilai_ag == 0 ? rupiah(0) : rupiah(($belanja->nilai_real * 100) / $belanja->nilai_ag) }}
                        </td>
                    </tr>
                @endif

                <!-- untuk urutan kode bidang urusan-->
                @if ($value->jenis == 'bidang_urusan')
                    @php
                        $urusan = substr($value->kode, 0, 1);
                    @endphp
                @endif
            @endforeach
            <tr class="bold">
                <td colspan="4" style="text-align: right;">(SURPLUS/DEFISIT)</td>
                @php
                    $surplus_anggaran = $pendapatan->nilai_ag - $belanja->nilai_ag;
                    $surplus_realisasi = $pendapatan->nilai_real - $belanja->nilai_real;
                    $sisa_anggaran = $surplus_anggaran - $surplus_realisasi;
                @endphp
                <td style="text-align: right">
                    {{ $surplus_anggaran >= 0 ? rupiah($surplus_anggaran) : '(' . rupiah(abs($surplus_anggaran)) . ')' }}
                </td>
                <td style="text-align: right">
                    {{ $surplus_realisasi >= 0 ? rupiah($surplus_realisasi) : '(' . rupiah(abs($surplus_realisasi)) . ')' }}
                </td>
                <td style="text-align: right">
                    {{ $sisa_anggaran >= 0 ? rupiah($sisa_anggaran) : '(' . rupiah(abs($sisa_anggaran)) . ')' }}</td>
                <td>{{ $surplus_anggaran == 0 ? rupiah(0) : rupiah(($surplus_realisasi * 100) / $surplus_anggaran) }}
                </td>
            </tr>
        </tbody>
    </table>
    {{-- <div style="height: 100px"></div> --}}
    <div style="padding: 16px; font-size: 14px;">
        <div style="float: right; text-align: center;">
            {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}<br />
            {{-- Sungai Raya, ........<br /> --}}
            {{ $ttd->jabatan }}
            <div style="height: 44px;"></div>
            <p style="text-transform:uppercase">{{ $ttd_nm->nama }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>
