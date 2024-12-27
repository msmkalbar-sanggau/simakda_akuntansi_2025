<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran III</title>
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
                <td>Lampiran III</td>
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
        <h4 style="margin: 2px 0px;">LAPORAN OPERASIONAL</h4>
        <h4 style="margin: 2px 0px;">UNTUK TAHUN YANG BERAKHIR SAMPAI DENGAN 31 DESEMBER {{ tahun_anggaran() }} DAN {{ tahun_anggarans() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th bgcolor="#CCCCCC">Uraian</th>
                <th bgcolor="#CCCCCC">{{ tahun_anggaran() }}</th>
                <th bgcolor="#CCCCCC">{{ tahun_anggarans() }}</th>
                <th bgcolor="#CCCCCC">Kenaikan <br> (Penurunan)</th>
                <th bgcolor="#CCCCCC">%</th>
            </tr>
        </thead>

        <tbody>
            @foreach($data as $value)
                @php
                    $thn_berjalan = $value->nilai_berjalan;
                    $thn_lalu = $value->nilai_lalu;
                    $bertambah_berkurang = $thn_berjalan - $thn_lalu;
                @endphp
                @if(is_null($value->group_name) || empty($value->group_name))
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @elseif($value->group_name == 'Surplus/ Defisit dari Operasi')
                <tr>
                    <td>{{ $value->group_name }}</td>
                    <td class="kanan">{{ $jumlahsddo->nilai >= 0 ? rupiah($jumlahsddo->nilai) : '(' . rupiah(abs($jumlahsddo->nilai)) . ')'  }}</td>
                    <td class="kanan">{{ $jumlahsddos->nilai >= 0 ? rupiah($jumlahsddos->nilai) : '(' . rupiah(abs($jumlahsddos->nilai)) . ')'  }}</td>
                    <td class="kanan">{{ $bertambah_berkurang_sddos >= 0 ? rupiah($bertambah_berkurang_sddos) : '(' . rupiah(abs($bertambah_berkurang_sddos)) . ')'  }}</td>
                    <td>{{ $jumlahsddos->nilai == 0 ? rupiah(0) : rupiah($jumlahsddo->nilai * 100 / $jumlahsddos->nilai) }}</td>
                </tr>
                @elseif($value->group_name == 'Surplus/Defisit dari Kegiatan Non Operasional')
                <tr>
                    <td>{{ $value->group_name }}</td>
                    <td class="kanan">{{ $jumlahsdkno->nilai >= 0 ? rupiah($jumlahsdkno->nilai) : '(' . rupiah(abs($jumlahsdkno->nilai)) . ')'  }}</td>
                    <td class="kanan">{{ $jumlahsdknos->nilai >= 0 ? rupiah($jumlahsdknos->nilai) : '(' . rupiah(abs($jumlahsdknos->nilai)) . ')'  }}</td>
                    <td class="kanan">{{ $bertambah_berkurang_sdknos >= 0 ? rupiah($bertambah_berkurang_sdknos) : '(' . rupiah(abs($bertambah_berkurang_sdknos)) . ')'  }}</td>
                    <td>{{ $jumlahsdknos->nilai == 0 ? rupiah(0) : rupiah($jumlahsdkno->nilai * 100 / $jumlahsdknos->nilai) }}</td>
                </tr>
                @elseif($value->group_name == 'Surplus Defisit Sebelum Pos Luar Biasa')
                <tr>
                    <td>{{ $value->group_name }}</td>
                    <td class="kanan">{{ $jumlahsdsplb->nilai >= 0 ? rupiah($jumlahsdsplb->nilai) : '(' . rupiah(abs($jumlahsdsplb->nilai)) . ')'  }}</td>
                    <td class="kanan">{{ $jumlahsdsplbs->nilai >= 0 ? rupiah($jumlahsdsplbs->nilai) : '(' . rupiah(abs($jumlahsdsplbs->nilai)) . ')'  }}</td>
                    <td class="kanan">{{ $bertambah_berkurang_sdsplb >= 0 ? rupiah($bertambah_berkurang_sdsplb) : '(' . rupiah(abs($bertambah_berkurang_sdsplb)) . ')'  }}</td>
                    <td>{{ $jumlahsdsplbs->nilai == 0 ? rupiah(0) : rupiah($jumlahsdsplb->nilai * 100 / $jumlahsdsplbs->nilai) }}</td>
                </tr>
                @elseif($value->group_name == 'Surplus Defisit/LO')
                <tr>
                    <td>{{ $value->group_name }}</td>
                    <td class="kanan">{{ $jumlahsdl->nilai >= 0 ? rupiah($jumlahsdl->nilai) : '(' . rupiah(abs($jumlahsdl->nilai)) . ')'  }}</td>
                    <td class="kanan">{{ $jumlahsdls->nilai >= 0 ? rupiah($jumlahsdls->nilai) : '(' . rupiah(abs($jumlahsdls->nilai)) . ')'  }}</td>
                    <td class="kanan">{{ $bertambah_berkurang_sdl >= 0 ? rupiah($bertambah_berkurang_sdl) : '(' . rupiah(abs($bertambah_berkurang_sdl)) . ')'  }}</td>
                    <td>{{ rupiah(0) }}</td>
                </tr>
                @else
                <tr>
                    <td>{{ $value->group_name }}</td>
                    <td class="kanan">{{ $value->nilai_berjalan >= 0 ? rupiah($value->nilai_berjalan) : '(' . rupiah(abs($value->nilai_berjalan)) . ')'  }}</td>
                    <td class="kanan">{{ $value->nilai_lalu >= 0 ? rupiah($value->nilai_lalu) : '(' . rupiah(abs($value->nilai_lalu)) . ')'  }}</td>
                    <td class="kanan">{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')'  }}</td>
                    <td>{{ $thn_lalu == 0 ? rupiah(0) : rupiah($thn_berjalan * 100 / $thn_lalu) }}</td>
                </tr>
                @endif
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
