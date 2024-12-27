<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LRA SAP</title>
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
    </style>
</head>

<body>
    <div id="header" style="border: 1px solid black; text-align: center;">
        <img style="width: 84px; float: left; margin: 8px;"
            src="{{ asset('template/assets/images/' . $header1->logo_pemda_hp) }}" width="75" height="100" />
        <h4 style="margin: 8px 0px;">PEMERINTAH {{ strtoupper($header1->nm_pemda) }}</h4>
        @if ($nm_skpd)
            <h4 style="margin: 8px 0px;">{{ TextUpperCase($nm_skpd->nm_skpd) }}</h4>
        @endif
        <h4 style="margin: 8px 0px;">LAPORAN REALISASI ANGGARAN PENDAPATAN DAN BELANJA</h4>
        <h4 style="margin: 8px 0px;">UNTUK TAHUN YANG BERAKHIR SAMPAI DENGAN {{ $periode }} TAHUN
            {{ tahun_anggaran() }}</h4>
        <h4 style="margin: 8px 0px;">{{ TextUpperCase($header) }}</h4>
        <div style="clear: both;"></div>
    </div>
    <table class="bordered">
        <thead>
            <tr>
                <th rowspan="2">KD REK</th>
                <th rowspan="2">URAIAN</th>
                <th colspan="2">JUMLAH (Rp.)</th>
                <th colspan="2">BERTAMBAH/KURANG</th>
            </tr>
            <tr>
                <th>ANGGARAN</th>
                <th>REALISASI</th>
                <th>(Rp)</th>
                <th>%</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
            </tr>
        </thead>

        <body>
            @php
                $anggaran_pendapatan = 0;
                $realisasi_pendapatan = 0;
                $anggaran_belanja = 0;
                $realisasi_belanja = 0;
                $anggaran_penerimaan_pembiayaan = 0;
                $realisasi_penerimaan_pembiayaan = 0;
                $anggaran_pengeluaran_pembiayaan = 0;
                $realisasi_pengeluaran_pembiayaan = 0;
            @endphp
            @foreach ($data as $key => $value)
                @php
                    if ($value->kd_rek == '4') {
                        $anggaran_pendapatan = $value->anggaran;
                        $realisasi_pendapatan = $value->realisasi;
                    }
                    if ($value->kd_rek == '5') {
                        $anggaran_belanja = $value->anggaran;
                        $realisasi_belanja = $value->realisasi;
                    }
                    if ($value->kd_rek == '61') {
                        $anggaran_penerimaan_pembiayaan = $value->anggaran;
                        $realisasi_penerimaan_pembiayaan = $value->realisasi;
                    }
                    if ($value->kd_rek == '62') {
                        $anggaran_pengeluaran_pembiayaan = $value->anggaran;
                        $realisasi_pengeluaran_pembiayaan = $value->realisasi;
                    }
                @endphp
                @if ($value->kd_rek == 'SURPLUS')
                    @php
                        $surplus_anggaran = $anggaran_pendapatan - $anggaran_belanja;
                        $surplus_realisasi = $realisasi_pendapatan - $realisasi_belanja;
                        $bertambah_berkurang_surplus = $surplus_anggaran - $surplus_realisasi;
                    @endphp
                    <tr class="{{ $value->is_bold ? 'bold' : '' }} tr">
                        <td class="td"></td>
                        <td class="td"
                            style="padding-left: <?= $value->padding ?>px; <?= $value->right_align ? 'text-align: right' : '' ?>;">
                            {{ $value->group_name }}</td>
                        <td class="td" style="text-align: right">
                            {{ $surplus_anggaran >= 0 ? rupiah($surplus_anggaran) : '(' . rupiah(abs($surplus_anggaran)) . ')' }}
                        </td>
                        <td class="td" style="text-align: right">
                            {{ $surplus_realisasi >= 0 ? rupiah($surplus_realisasi) : '(' . rupiah(abs($surplus_realisasi)) . ')' }}
                        </td>
                        <td class="td" style="text-align: right">
                            {{ $bertambah_berkurang_surplus >= 0 ? rupiah($bertambah_berkurang_surplus) : '(' . rupiah(abs($bertambah_berkurang_surplus)) . ')' }}
                        </td>
                        <td>
                            @if ($surplus_anggaran == 0)
                                {{ rupiah($surplus_realisasi == 0 ? 0 : 100) }}
                            @else
                                @if (($surplus_realisasi * 100) / $surplus_anggaran < 0)
                                    ({{ rupiah(($surplus_realisasi * -100) / $surplus_anggaran) }})
                                @else
                                    {{ rupiah(($surplus_realisasi * 100) / $surplus_anggaran) }}
                                @endif
                            @endif
                        </td>
                    </tr>
                @elseif($value->kd_rek == 'NETTO')
                    @php
                        $netto_anggaran = $anggaran_penerimaan_pembiayaan - $anggaran_pengeluaran_pembiayaan;
                        $netto_realisasi = $realisasi_penerimaan_pembiayaan - $realisasi_pengeluaran_pembiayaan;
                        $bertambah_berkurang_netto = $netto_anggaran - $netto_realisasi;
                    @endphp
                    <tr class="{{ $value->is_bold ? 'bold' : '' }} tr">
                        <td class="td"></td>
                        <td class="td"
                            style="padding-left: <?= $value->padding ?>px; <?= $value->right_align ? 'text-align: right' : '' ?>;">
                            {{ $value->group_name }}</td>
                        <td class="td" style="text-align: right">
                            {{ $netto_anggaran >= 0 ? rupiah($netto_anggaran) : '(' . rupiah(abs($netto_anggaran)) . ')' }}
                        </td>
                        <td class="td" style="text-align: right">
                            {{ $netto_realisasi >= 0 ? rupiah($netto_realisasi) : '(' . rupiah(abs($netto_realisasi)) . ')' }}
                        </td>
                        <td class="td" style="text-align: right">
                            {{ $bertambah_berkurang_netto >= 0 ? rupiah($bertambah_berkurang_netto) : '(' . rupiah(abs($bertambah_berkurang_netto)) . ')' }}
                        </td>
                        <td>
                            @if ($netto_anggaran == 0)
                                {{ rupiah($netto_realisasi == 0 ? 0 : 100) }}
                            @else
                                @if (($netto_realisasi * 100) / $netto_anggaran < 0)
                                    ({{ rupiah(($netto_realisasi * -100) / $netto_anggaran) }})
                                @else
                                    {{ rupiah(($netto_realisasi * 100) / $netto_anggaran) }}
                                @endif
                            @endif
                        </td>
                    </tr>
                @elseif($value->kd_rek == 'SILPA')
                    @php
                        $silpa_anggaran = $surplus_anggaran + $netto_anggaran;
                        $silpa_realisasi = $surplus_realisasi + $netto_realisasi;
                        $bertambah_berkurang_silpa = $silpa_anggaran - $silpa_realisasi;
                    @endphp
                    <tr class="{{ $value->is_bold ? 'bold' : '' }} tr">
                        <td class="td"></td>
                        <td class="td"
                            style="padding-left: <?= $value->padding ?>px; <?= $value->right_align ? 'text-align: right' : '' ?>;">
                            {{ $value->group_name }}</td>
                        <td class="td" style="text-align: right">
                            {{ $silpa_anggaran >= 0 ? rupiah($silpa_anggaran) : '(' . rupiah(abs($silpa_anggaran)) . ')' }}
                        </td>
                        <td class="td" style="text-align: right">
                            {{ $silpa_realisasi >= 0 ? rupiah($silpa_realisasi) : '(' . rupiah(abs($silpa_realisasi)) . ')' }}
                        </td>
                        <td class="td" style="text-align: right">
                            {{ $bertambah_berkurang_silpa >= 0 ? rupiah($bertambah_berkurang_silpa) : '(' . rupiah(abs($bertambah_berkurang_silpa)) . ')' }}
                        </td>
                        <td>
                            @if ($silpa_anggaran == 0)
                                {{ rupiah($silpa_realisasi == 0 ? 0 : 100) }}
                            @else
                                @if (($silpa_realisasi * 100) / $silpa_anggaran < 0)
                                    ({{ rupiah(($silpa_realisasi * -100) / $silpa_anggaran) }})
                                @else
                                    {{ rupiah(($silpa_realisasi * 100) / $silpa_anggaran) }}
                                @endif
                            @endif
                        </td>
                    </tr>
                @elseif(
                    $value->group_name == 'PENDAPATAN DAERAH' ||
                        $value->group_name == 'BELANJA DAERAH' ||
                        $value->group_name == 'PEMBIAYAAN DAERAH' ||
                        $value->group_name == '')
                    <tr class="{{ $value->is_bold ? 'bold' : '' }} tr">
                        <td class="td">{{ $value->show_kd_rek ? $kd_rek_separator($value->kd_rek) : '' }}</td>
                        <td class="td"
                            style="padding-left: <?= $value->padding ?>px; <?= $value->right_align ? 'text-align: right' : '' ?>;">
                            {{ $value->group_name }}</td>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    @php
                        $bertambah_berkurang = $value->anggaran - $value->realisasi;
                    @endphp
                    <tr class="{{ $value->is_bold ? 'bold' : '' }} tr">
                        <td class="td">{{ $value->show_kd_rek ? $kd_rek_separator($value->kd_rek) : '' }}</td>
                        <td class="td"
                            style="padding-left: <?= $value->padding ?>px; <?= $value->right_align ? 'text-align: right' : '' ?>;">
                            {{ $value->group_name }}</td>
                        <td class="td" style="text-align: right">
                            {{ $value->anggaran >= 0 ? rupiah($value->anggaran) : '(' . rupiah(abs($value->anggaran)) . ')' }}
                        </td>
                        <td class="td" style="text-align: right">
                            {{ $value->realisasi >= 0 ? rupiah($value->realisasi) : '(' . rupiah(abs($value->realisasi)) . ')' }}
                        </td>
                        <td class="td" style="text-align: right">
                            {{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                        </td>
                        <td>
                            @if ($value->anggaran == 0)
                                {{ rupiah($value->realisasi == 0 ? 0 : 100) }}
                            @else
                                @if (($value->realisasi * 100) / $value->anggaran < 0)
                                    ({{ rupiah(($value->realisasi * -100) / $value->anggaran) }})
                                @else
                                    {{ rupiah(($value->realisasi * 100) / $value->anggaran) }}
                                @endif
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </body>
    </table>
    @if ($ttdyt == 1)
        <div style="padding: 16px; font-size: 14px;">
            <div style="float: right; text-align: center;">
                {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}<br />
                {{ $ttd->jabatan }}
                <div style="height: 64px;"></div>
                <b><u><?= $ttd->nama ?></u></b></br>
                <?= $ttd->pangkat ?><br>
                <?= $ttd->nip ?>
            </div>
            <div style="clear: both;"></div>
        </div>
    @endif
</body>

</html>
