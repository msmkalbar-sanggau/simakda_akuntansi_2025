<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran VIII</title>
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

        .bordered td:nth-child(n+4) {
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
                <td>Lampiran VIII</td>
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
        <h4 style="margin: 2px 0px;">DAFTAR REKAPITULASI PIUTANG DAERAH</h4>
        <h4 style="margin: 2px 0px;">TA {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th>No</th>
                <th>SKPD</th>
                <th>Jenis Piutang</th>
                <th>Saldo Awal <br>Piutang</th>
                <th>Penambahan <br>Piutang</th>
                <th>Pengurangan <br>Piutang</th>
                <th>Saldo Akhir <br>Piutang</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7=4+5-6</th>
            </tr>
        </thead>

        <tbody>
            @php
                $aset_tetap_lalu = 0;
                $saldo_akhir = 0;
                $debet = 0;
                $debet_lalu = 0;
                $kredit = 0;
                $kredit_lalu = 0;
                $no = 0;
            @endphp
            @foreach($data as $key => $value)
                @php
                    $aset_tetap_lalu = $value->debet_lalu - $value->kredit_lalu;
                    $saldo_akhir = $aset_tetap_lalu + $value->debet - $value->kredit;
                    $debet += $value->debet;
                    $debet_lalu += $value->debet_lalu;
                    $kredit += $value->kredit;
                    $kredit_lalu += $value->kredit_lalu;
                @endphp
                @if($aset_tetap_lalu != 0 || $value->debet != 0 || $value->kredit != 0)
                @php
                    $no = $no + 1;
                @endphp
                <tr>
                    <td style="text-align: center">{{ $no }}.</td>
                    <td>{{ $value->nm_skpd }}</td>
                    <td>{{ $value->nm_rek }}</td>
                    <td>{{ $aset_tetap_lalu >= 0 ? rupiah($aset_tetap_lalu) : '(' . rupiah(abs($aset_tetap_lalu)) . ')' }}</td>
                    <td>{{ $value->debet >= 0 ? rupiah($value->debet) : '(' . rupiah(abs($value->debet)) . ')' }}</td>
                    <td>{{ $value->kredit >= 0 ? rupiah($value->kredit) : '(' . rupiah(abs($value->kredit)) . ')' }}</td>
                    <td>{{ $saldo_akhir >= 0 ? rupiah($saldo_akhir) : '(' . rupiah(abs($saldo_akhir)) . ')' }}</td>
                </tr>
                @endif
            @endforeach
            @php
                $aset_tetap_s = $debet_lalu - $kredit_lalu;
                $saldo_akhir_s = $aset_tetap_s + $debet - $kredit;
            @endphp
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Jumlah</td>
                <td style="text-align: right; font-weight: bold;">{{ $aset_tetap_s >= 0 ? rupiah($aset_tetap_s) : '(' . rupiah(abs($aset_tetap_s)) . ')' }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $debet >= 0 ? rupiah($debet) : '(' . rupiah(abs($debet)) . ')' }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $kredit >= 0 ? rupiah($kredit) : '(' . rupiah(abs($kredit)) . ')' }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $saldo_akhir_s >= 0 ? rupiah($saldo_akhir_s) : '(' . rupiah(abs($saldo_akhir_s)) . ')' }}</td>
            </tr>
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
