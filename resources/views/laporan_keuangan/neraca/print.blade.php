<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NERACA</title>
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
        <h4 style="margin: 8px 0px;">LAPORAN NERACA</h4>
        <h4 style="margin: 8px 0px;">PER {{ $arraybulan[$bulan] }}
            {{ tahun_anggaran() }} DAN {{ tahun_anggarans() }}</h4>
        <div style="clear: both;"></div>
    </div>
    <table class="bordered">
        <thead>
            <tr>
                <th>NO</th>
                <th>URAIAN</th>
                <th>{{ tahun_anggaran() }}</th>
                <th>{{ tahun_anggarans() }}</th>
            </tr>
        </thead>

        <body>
            @foreach ($data as $value)
                @php
                    if ($value->group_name == 'KEWAJIBAN') {
                        $nilaiKewajiban = $value->nilai_berjalan - $eku;
                        $nilaiKewajiban_lalu = $value->nilai_lalu - $eku_lalu;
                    }
                @endphp
                @if (is_null($value->group_name) || empty($value->group_name))
                    <tr>
                        <td class="right">{{ $value->group_id }}</td>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                    </tr>
                @elseif($value->group_name == 'ASET')
                    @php
                        $aset = $value->nilai_berjalan - $rk_skpd->rk_skpd_24;
                        $aset_lalu = $value->nilai_lalu - $rk_skpd->rk_skpd_23;
                    @endphp
                    <tr>
                        <td class="right">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $aset >= 0 ? rupiah($aset) : '(' . rupiah(abs($aset)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $aset_lalu >= 0 ? rupiah($aset_lalu) : '(' . rupiah(abs($aset_lalu)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_name == 'ASET LANCAR')
                    @php
                        $aset_lancar = $value->nilai_berjalan - $rk_skpd->rk_skpd_24;
                        $aset_lancar_lalu = $value->nilai_lalu - $rk_skpd->rk_skpd_23;
                    @endphp
                    <tr>
                        <td class="right">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $aset_lancar >= 0 ? rupiah($aset_lancar) : '(' . rupiah(abs($aset_lancar)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $aset_lancar_lalu >= 0 ? rupiah($aset_lancar_lalu) : '(' . rupiah(abs($aset_lancar_lalu)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_name == 'KEWAJIBAN DAN EKUITAS' || $value->group_name == 'JUMLAH KEWAJIBAN DAN EKUITAS')
                    <tr>
                        <td class="right">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $kewajiban_ekuitas >= 0 ? rupiah($kewajiban_ekuitas) : '(' . rupiah(abs($kewajiban_ekuitas)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $kewajiban_ekuitas_lalu >= 0 ? rupiah($kewajiban_ekuitas_lalu) : '(' . rupiah(abs($kewajiban_ekuitas_lalu)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_name == 'EKUITAS' || $value->group_name == 'JUMLAH EKUITAS')
                    <tr>
                        <td class="right">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $eku >= 0 ? rupiah($eku) : '(' . rupiah(abs($eku)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $eku_lalu >= 0 ? rupiah($eku_lalu) : '(' . rupiah(abs($eku_lalu)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '318')
                    <tr>
                        <td class="right">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $eku_objek >= 0 ? rupiah($eku_objek) : '(' . rupiah(abs($eku_objek)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $eku_lalu_objek >= 0 ? rupiah($eku_lalu_objek) : '(' . rupiah(abs($eku_lalu_objek)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_name == 'RK PPKD')
                    <tr>
                        <td class="right">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $rk_ppkd->rk_ppkd_24 >= 0 ? rupiah($rk_ppkd->rk_ppkd_24) : '(' . rupiah(abs($rk_ppkd->rk_ppkd_24)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $rk_ppkd->rk_ppkd_23 >= 0 ? rupiah($rk_ppkd->rk_ppkd_23) : '(' . rupiah(abs($rk_ppkd->rk_ppkd_23)) . ')' }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="right">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $value->nilai_berjalan >= 0 ? rupiah($value->nilai_berjalan) : '(' . rupiah(abs($value->nilai_berjalan)) . ')' }}
                        </td>
                        <td class="kanan {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $value->nilai_lalu >= 0 ? rupiah($value->nilai_lalu) : '(' . rupiah(abs($value->nilai_lalu)) . ')' }}
                        </td>
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
