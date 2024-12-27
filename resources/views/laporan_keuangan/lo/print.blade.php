<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LO</title>
    <style>
        body {
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

        .bordered td:nth-child(n+3) {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="header" style="border: 1px solid black; text-align: center;">
        <img style="width: 84px; float: left; margin: 8px;"
            src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75" height="100" />
        <h4 style="margin: 8px 0px;">PEMERINTAH {{ strtoupper($header->nm_pemda) }}</h4>
        @if ($nm_skpd)
            <h4 style="margin: 8px 0px;">{{ TextUpperCase($nm_skpd->nm_skpd) }}</h4>
        @endif
        <h4 style="margin: 8px 0px;">LAPORAN OPERASIONAL</h4>
        <h4 style="margin: 8px 0px;">UNTUK TAHUN YANG BERAKHIR SAMPAI DENGAN {{ $arraybulan[$bulan] }}
            {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div>
    <table class="bordered">
        <thead>
            <tr>
                <th>NO</th>
                <th>URAIAN</th>
                <th>{{ tahun_anggaran() }}</th>
                <th>{{ tahun_anggarans() }}</th>
                <th>Kenaikan <br> (Penurunan)</th>
                <th>%</th>
            </tr>
        </thead>

        <body>
            @foreach ($data as $value)
                @php
                    $thn_berjalan = $value->nilai_berjalan;
                    $thn_lalu = $value->nilai_lalu;
                    $bertambah_berkurang = $thn_berjalan - $thn_lalu;
                @endphp
                @if (is_null($value->group_name) || empty($value->group_name))
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @elseif($value->group_name == 'SURPLUS/ DEFISIT DARI OPERASI')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsddo->nilai >= 0 ? rupiah($jumlahsddo->nilai) : '(' . rupiah(abs($jumlahsddo->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsddos->nilai >= 0 ? rupiah($jumlahsddos->nilai) : '(' . rupiah(abs($jumlahsddos->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $bertambah_berkurang_sddos >= 0 ? rupiah($bertambah_berkurang_sddos) : '(' . rupiah(abs($bertambah_berkurang_sddos)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsddos->nilai == 0 ? rupiah(0) : rupiah(($jumlahsddo->nilai * 100) / $jumlahsddos->nilai) }}
                        </td>
                    </tr>
                @elseif($value->group_name == 'SURPLUS/ DEFISIT DARI KEGIATAN NON OPERASIONAL')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsdkno->nilai >= 0 ? rupiah($jumlahsdkno->nilai) : '(' . rupiah(abs($jumlahsdkno->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsdknos->nilai >= 0 ? rupiah($jumlahsdknos->nilai) : '(' . rupiah(abs($jumlahsdknos->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $bertambah_berkurang_sdknos >= 0 ? rupiah($bertambah_berkurang_sdknos) : '(' . rupiah(abs($bertambah_berkurang_sdknos)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsdknos->nilai == 0 ? rupiah(0) : rupiah(($jumlahsdkno->nilai * 100) / $jumlahsdknos->nilai) }}
                        </td>
                    </tr>
                @elseif($value->group_name == 'SURPLUS/ DEFISIT SEBELUM POS LUAR BIASA')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsdsplb->nilai >= 0 ? rupiah($jumlahsdsplb->nilai) : '(' . rupiah(abs($jumlahsdsplb->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsdsplbs->nilai >= 0 ? rupiah($jumlahsdsplbs->nilai) : '(' . rupiah(abs($jumlahsdsplbs->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $bertambah_berkurang_sdsplb >= 0 ? rupiah($bertambah_berkurang_sdsplb) : '(' . rupiah(abs($bertambah_berkurang_sdsplb)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsdsplbs->nilai == 0 ? rupiah(0) : rupiah(($jumlahsdsplb->nilai * 100) / $jumlahsdsplbs->nilai) }}
                        </td>
                    </tr>
                @elseif($value->group_name == 'JUMLAH SURPLUS/ DEFISIT DARI KEGIATAN NON OPERASIONAL')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahjsdkno->nilai >= 0 ? rupiah($jumlahjsdkno->nilai) : '(' . rupiah(abs($jumlahjsdkno->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahjsdknos->nilai >= 0 ? rupiah($jumlahjsdknos->nilai) : '(' . rupiah(abs($jumlahjsdknos->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $bertambah_berkurang_jsdknos >= 0 ? rupiah($bertambah_berkurang_jsdknos) : '(' . rupiah(abs($bertambah_berkurang_jsdknos)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahjsdknos->nilai == 0 ? rupiah(0) : rupiah(($jumlahjsdkno->nilai * 100) / $jumlahjsdknos->nilai) }}
                        </td>
                    </tr>
                @elseif($value->group_name == 'SURPLUS/ DEFISIT - LO')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsdl->nilai >= 0 ? rupiah($jumlahsdl->nilai) : '(' . rupiah(abs($jumlahsdl->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $jumlahsdls->nilai >= 0 ? rupiah($jumlahsdls->nilai) : '(' . rupiah(abs($jumlahsdls->nilai)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $bertambah_berkurang_sdl >= 0 ? rupiah($bertambah_berkurang_sdl) : '(' . rupiah(abs($bertambah_berkurang_sdl)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}">{{ rupiah(0) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $value->nilai_berjalan >= 0 ? rupiah($value->nilai_berjalan) : '(' . rupiah(abs($value->nilai_berjalan)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $value->nilai_lalu >= 0 ? rupiah($value->nilai_lalu) : '(' . rupiah(abs($value->nilai_lalu)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}">
                            {{ $thn_lalu == 0 ? rupiah(0) : rupiah(($thn_berjalan * 100) / $thn_lalu) }}</td>
                    </tr>
                @endif
            @endforeach
        </body>
    </table>
    <div style="padding: 16px; font-size: 14px;">
        <div style="float: right; text-align: center;">
            {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}<br />
            {{ $ttd->jabatan }}
            <div style="height: 64px;"></div>
            <b><u><?= $ttd->nama ?></u></b>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>

</html>
