<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran D.3</title>
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

        .bordered td:nth-child(n+6) {
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
        <h4 style="margin: 2px 0px;">REKAPITULASI REALISASI BELANJA UNTUK PEMENUHAN STANDAR PELAYANAN MINIMAL (SPM)</h4>
        <h4 style="margin: 2px 0px;">TA {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th bgcolor="#CCCCCC">No</th>
                <th bgcolor="#CCCCCC">Jenis Pelayanan Dasar</th>
                <th bgcolor="#CCCCCC">Kode Sub Kegiatan</th>
                <th bgcolor="#CCCCCC">Kegiatan</th>
                <th bgcolor="#CCCCCC">Anggaran (Rp)</th>
                <th bgcolor="#CCCCCC">Realisasi (Rp)</th>
            </tr>
            <tr>
                <th bgcolor="#CCCCCC">1</th>
                <th bgcolor="#CCCCCC">2</th>
                <th bgcolor="#CCCCCC">3</th>
                <th bgcolor="#CCCCCC">4</th>
                <th bgcolor="#CCCCCC">5</th>
                <th bgcolor="#CCCCCC">6</th>
            </tr>
        </thead>

        <tbody>
            @php
                $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                $alphabet_index = 0;
            @endphp
            @foreach ($mapped_lamp as $value)
                <tr>
                    <td bgcolor="#CCCCCC" style="font-weight: bold;" class="center">{{ $alphabet[$alphabet_index] }}</td>
                    <td bgcolor="#CCCCCC" style="font-weight: bold;" class="center">{{ $value['nm_bidang_urusan'] }}</td>
                    <td bgcolor="#CCCCCC"></td>
                    <td bgcolor="#CCCCCC"></td>
                    <td bgcolor="#CCCCCC"></td>
                    <td bgcolor="#CCCCCC"></td>
                </tr>
                @php
                    $spm_index = 1;
                    $total_anggaran_spm = 0;
                    $total_realisasi_spm = 0;
                @endphp
                @foreach ($value['spm'] as $nm_spm => $spm)
                    @php
                        $total_anggaran = 0;
                        $total_realisasi = 0;
                    @endphp
                    @foreach ($spm as $key => $sub_kegiatan)
                        @php
                            $total_anggaran += $sub_kegiatan['anggaran'];
                            $total_realisasi += $sub_kegiatan['realisasi'];
                            $class = $key + 1 == count($spm) ? 'last-row' : '';
                            $class .= $key == 0 ? ' first-row' : '';
                        @endphp
                        <tr>
                            @if($key == 0)
                                <td class="center" style="vertical-align: top;" rowspan="<?= count($spm) ?>">{{ $spm_index }}.</td>
                                <td style="vertical-align: top;" rowspan="<?= count($spm) ?>">{{ $nm_spm }}.</td>
                            @endif
                            <td class="no-vertical-border {{ $class }}">{{ $sub_kegiatan['kd_sub_kegiatan'] }}</td>
                            <td class="no-vertical-border {{ $class }}">{{ $key + 1 }}. {{ $sub_kegiatan['nm_sub_kegiatan'] }}</td>
                            <td class="no-wrap no-vertical-border {{ $class }}>
                                <div style="display: -webkit-box; -webkit-box-direction: row; display: flex; flex-direction: row; width: 100%;">
                                    <div style="-webkit-box-flex: 1; flex: 1 1 auto;"></div>
                                    <div style="margin-left: 8px;" class="kanan">{{ rupiah($sub_kegiatan['anggaran']) }}</div>
                                </div>
                            </td>
                            <td class="no-wrap no-vertical-border {{ $class }}>
                                <div style="display: -webkit-box; -webkit-box-direction: row; display: flex; flex-direction: row; width: 100%;">
                                    <div style="-webkit-box-flex: 1; flex: 1 1 auto;"></div>
                                    <div style="margin-left: 8px;" class="kanan">{{ rupiah($sub_kegiatan['realisasi']) }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td bgcolor="#CCCCCC" style="padding-right: 8px; font-weight:bold;" class="kanan" colspan="4">Total</td>
                        <td bgcolor="#CCCCCC" style="text-align: right; font-weight:bold;">{{ rupiah($total_anggaran) }}</td>
                        <td bgcolor="#CCCCCC" style="text-align: right; font-weight:bold;">{{ rupiah($total_realisasi) }}</td>
                    </tr>
                    @php
                        $spm_index++;
                        $total_anggaran_spm += $total_anggaran;
                        $total_realisasi_spm += $total_realisasi;
                    @endphp
                @endforeach
                <tr>
                    <td bgcolor="#CCCCCC" style="padding-right: 8px; font-weight:bold;" class="kanan" colspan="4">Jumlah Anggaran dan Realisasi {{ $value['nm_bidang_urusan'] }}</td>
                    <td bgcolor="#CCCCCC" style="text-align: right; font-weight:bold;">{{ rupiah($total_anggaran_spm) }}</td>
                    <td bgcolor="#CCCCCC" style="text-align: right; font-weight:bold;">{{ rupiah($total_realisasi_spm) }}</td>
                </tr>
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                @php
                    $alphabet_index++;
                @endphp
            @endforeach
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
