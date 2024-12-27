<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LAK</title>
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

        .bold {
            font-weight: bold;
        }

        .right {
            text-align: right;
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
        <h4 style="margin: 8px 0px;">LAPORAN ARUS KAS</h4>
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
            </tr>
        </thead>

        <body>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @foreach ($data as $value)
                @if ($value->group_id == '1' || $value->group_id == '37' || $value->group_id == '63' || $value->group_id == '83')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td></td>
                    </tr>
                @elseif($value->group_id == '35')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusOperasi >= 0 ? rupiah($arusOperasi) : '(' . rupiah(abs($arusOperasi)) . ')' }}</td>
                    </tr>
                @elseif($value->group_id == '2' || $value->group_id == '19')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusMasukKas >= 0 ? rupiah($arusMasukKas) : '(' . rupiah(abs($arusMasukKas)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '38' || $value->group_id == '48')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusMasukKas1 >= 0 ? rupiah($arusMasukKas1) : '(' . rupiah(abs($arusMasukKas1)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '21' || $value->group_id == '34')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusKeluarKas >= 0 ? rupiah($arusKeluarKas) : '(' . rupiah(abs($arusKeluarKas)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '50' || $value->group_id == '60')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusKeluarKas1 >= 0 ? rupiah($arusKeluarKas1) : '(' . rupiah(abs($arusKeluarKas1)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '61')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusInvestasi >= 0 ? rupiah($arusInvestasi) : '(' . rupiah(abs($arusInvestasi)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '64' || $value->group_id == '71')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusMasukKas2 >= 0 ? rupiah($arusMasukKas2) : '(' . rupiah(abs($arusMasukKas2)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '73' || $value->group_id == '80')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusKeluarKas2 >= 0 ? rupiah($arusKeluarKas2) : '(' . rupiah(abs($arusKeluarKas2)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '81')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusPendanaan >= 0 ? rupiah($arusPendanaan) : '(' . rupiah(abs($arusPendanaan)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '84' || $value->group_id == '85' || $value->group_id == '86')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $dataPFKMasuk->nilai >= 0 ? rupiah($dataPFKMasuk->nilai) : '(' . rupiah(abs($dataPFKMasuk->nilai)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '88' || $value->group_id == '89' || $value->group_id == '90')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $dataPFKkeluar->nilai >= 0 ? rupiah($dataPFKkeluar->nilai) : '(' . rupiah(abs($dataPFKkeluar->nilai)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '91')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $arusTransitoris >= 0 ? rupiah($arusTransitoris) : '(' . rupiah(abs($arusTransitoris)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '93')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $KenaikanPenurunanKas >= 0 ? rupiah($KenaikanPenurunanKas) : '(' . rupiah(abs($KenaikanPenurunanKas)) . ')' }}
                        </td>
                    </tr>
                @elseif($value->group_id == '95')
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $saldoAkhirKas >= 0 ? rupiah($saldoAkhirKas) : '(' . rupiah(abs($saldoAkhirKas)) . ')' }}
                        </td>
                    </tr>
                @elseif (is_null($value->group_name) || empty($value->group_name))
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td class="center">{{ $value->group_id }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }}"
                            style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                            {{ $value->group_name }}</td>
                        <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                            {{ $value->nilai >= 0 ? rupiah($value->nilai) : '(' . rupiah(abs($value->nilai)) . ')' }}
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
