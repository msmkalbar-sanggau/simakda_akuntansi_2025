<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan REKON</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0 0;
            width: 100%;

        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        <h4 style="margin: 2px 0px;">PEMERINTAH {{ strtoupper($header->nm_pemda) }}</h4>
        <h4 style="margin: 2px 0px;">REKONSILIASI {{ $thn_periode }}</h4>
        <h4 style="margin: 2px 0px; text-transform: uppercase;">{{ $skpd->nm_skpd }}</h4>
        <h4 style="margin: 2px 0px;">TAHUN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>

    <!-- register skpd -->
    <div style="text-align: left;">
        <h4 style="margin: 2px 0px; color:red;" class="unborder">JENIS : REGISTER SKPD</h4>
    </div>
    <table width="100%" border="1">
        <thead align="center">
            <tr>
                <th bgcolor="#CCCCCC" rowspan="2">Nama SKPD</th>
                <th bgcolor="#CCCCCC" rowspan="2">Bulan</th>
                <th bgcolor="#CCCCCC" colspan="6">Register SKPD</th>
            </tr>
            <tr>
                <th bgcolor="#CCCCCC" rowspan="2">GU</th>
                <th bgcolor="#CCCCCC" rowspan="2">TU</th>
                <th bgcolor="#CCCCCC" rowspan="2">Gaji</th>
                <th bgcolor="#CCCCCC" rowspan="2">Barang Jasa</th>
                <th bgcolor="#CCCCCC" rowspan="2">Pihak Ke 3 (PPKD)</th>
                <th bgcolor="#CCCCCC" rowspan="2"><b>Total</b></th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAkhir = 0;
                $totalgu = 0;
                $totaltu = 0;
                $totalgaji = 0;
                $totalls = 0;
                $totalppkd = 0;
                $no = 0;
            @endphp
            @foreach ($data as $value)
                @php
                    $no = $no + 1;
                @endphp
            @endforeach
            <tr style="text-align: center;">
                <td rowspan="{{ $no + 1 }}">{{ $skpd->nm_skpd }}</td>
            </tr>
            @foreach ($data as  $value)
            @php
                $dataTotal = $value->gu_skpd + $value->tu_skpd + $value->gaji_skpd + $value->ls_skpd + $value->ppkd_skpd;
                $totalAkhir += $dataTotal;
                $totalgu += $value->gu_skpd;
                $totaltu += $value->tu_skpd;
                $totalgaji += $value->gaji_skpd;
                $totalls += $value->ls_skpd;
                $totalppkd += $value->ppkd_skpd;
            @endphp
            <tr>
                <td>{{ MSBulan($value->bulan) }}</td>
                <td style="text-align:right;">{{ rupiah($value->gu_skpd) }}</td>
                <td style="text-align:right;">{{ rupiah($value->tu_skpd) }}</td>
                <td style="text-align:right;">{{ rupiah($value->gaji_skpd) }}</td>
                <td style="text-align:right;">{{ rupiah($value->ls_skpd) }}</td>
                <td style="text-align:right;">{{ rupiah($value->ppkd_skpd) }}</td>
                <td style="text-align:right;">{{ rupiah($dataTotal) }}</td>
            </tr>
            @endforeach
            <tr>
                <td bgcolor="#CCCCCC" style="text-align:center;" colspan="2"><b>TOTAL</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalgu) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totaltu) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalgaji) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalls) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalppkd) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalAkhir) }}</b></td>
            </tr>
        </tbody>
    </table> <br>

    <!-- register kasda -->
    <div style="text-align: left;">
        <h4 style="margin: 2px 0px; color:red;" class="unborder">JENIS : REGISTER KASDA</h4>
    </div>
    <table width="100%" border="1">
        <thead align="center">
            <tr>
                <th bgcolor="#CCCCCC" rowspan="2">Nama SKPD</th>
                <th bgcolor="#CCCCCC" rowspan="2">Bulan</th>
                <th bgcolor="#CCCCCC" colspan="6">Register KASDA</th>
            </tr>
            <tr>
                <th bgcolor="#CCCCCC" rowspan="2">GU</th>
                <th bgcolor="#CCCCCC" rowspan="2">TU</th>
                <th bgcolor="#CCCCCC" rowspan="2">Gaji</th>
                <th bgcolor="#CCCCCC" rowspan="2">Barang Jasa</th>
                <th bgcolor="#CCCCCC" rowspan="2">Pihak Ke 3 (PPKD)</th>
                <th bgcolor="#CCCCCC" rowspan="2"><b>Total</b></th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAkhir = 0;
                $totalgu = 0;
                $totaltu = 0;
                $totalgaji = 0;
                $totalls = 0;
                $totalppkd = 0;
                $no = 0;
            @endphp
            @foreach ($data as $value)
                @php
                    $no = $no + 1;
                @endphp
            @endforeach
            <tr style="text-align: center;">
                <td rowspan="{{ $no + 1 }}">{{ $skpd->nm_skpd }}</td>
            </tr>
            @foreach ($data as $value)
            @php
                $dataTotal = $value->gu_kasda + $value->tu_kasda + $value->gaji_kasda + $value->ls_kasda + $value->ph3_kasda;
                $totalAkhir += $dataTotal;
                $totalgu += $value->gu_kasda;
                $totaltu += $value->tu_kasda;
                $totalgaji += $value->gaji_kasda;
                $totalls += $value->ls_kasda;
                $totalppkd += $value->ph3_kasda;
            @endphp
            <tr>
                <td>{{ MSBulan($value->bulan) }}</td>
                <td style="text-align:right;">{{ rupiah($value->gu_kasda) }}</td>
                <td style="text-align:right;">{{ rupiah($value->tu_kasda) }}</td>
                <td style="text-align:right;">{{ rupiah($value->gaji_kasda) }}</td>
                <td style="text-align:right;">{{ rupiah($value->ls_kasda) }}</td>
                <td style="text-align:right;">{{ rupiah($value->ph3_kasda) }}</td>
                <td style="text-align:right;">{{ rupiah($dataTotal) }}</td>
            </tr>
            @endforeach
            <tr>
                <td bgcolor="#CCCCCC" style="text-align:center;" colspan="2"><b>TOTAL</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalgu) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totaltu) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalgaji) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalls) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalppkd) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalAkhir) }}</b></td>
            </tr>
        </tbody>
    </table> <br>

    <!-- lra -->
    <div style="text-align: left;">
        <h4 style="margin: 2px 0px; color:red;" class="unborder">JENIS : LRA</h4>
    </div>
    <table width="100%" border="1">
        <thead align="center">
            <tr>
                <th bgcolor="#CCCCCC" rowspan="2">Nama SKPD</th>
                <th bgcolor="#CCCCCC" rowspan="2">Bulan</th>
                <th bgcolor="#CCCCCC" colspan="7">LRA</th>
            </tr>
            <tr>
                <th bgcolor="#CCCCCC" rowspan="2">Belanja Pegawai</th>
                <th bgcolor="#CCCCCC" rowspan="2">Barang Jasa</th>
                <th bgcolor="#CCCCCC" rowspan="2">Belanja Modal</th>
                <th bgcolor="#CCCCCC" rowspan="2">Belanja Hibah</th>
                <th bgcolor="#CCCCCC" rowspan="2">Belanja Tidak Terduga</th>
                <th bgcolor="#CCCCCC" rowspan="2">Belanja Transfer</th>
                <th bgcolor="#CCCCCC" rowspan="2"><b>Total</b></th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAkhir = 0;
                $totalbape = 0;
                $totalberjas = 0;
                $totalbemo = 0;
                $totalbehi = 0;
                $totalbtt = 0;
                $totalbt = 0;
                $no = 0;
            @endphp
            @foreach ($data as $value)
                @php
                    $no = $no + 1;
                @endphp
            @endforeach
            <tr style="text-align: center;">
                <td rowspan="{{ $no + 1 }}">{{ $skpd->nm_skpd }}</td>
            </tr>
            @foreach ($data as $value)
            @php
                $dataTotal = $value->bape + $value->berjas + $value->bemo + $value->behi + $value->btt + $value->bt;
                $totalAkhir += $dataTotal;
                $totalbape += $value->bape;
                $totalberjas += $value->berjas;
                $totalbemo += $value->bemo;
                $totalbehi += $value->behi;
                $totalbtt += $value->btt;
                $totalbt += $value->bt;
            @endphp
            <tr>
                <td>{{ MSBulan($value->bulan) }}</td>
                <td style="text-align:right;">{{ rupiah($value->bape) }}</td>
                <td style="text-align:right;">{{ rupiah($value->berjas) }}</td>
                <td style="text-align:right;">{{ rupiah($value->bemo) }}</td>
                <td style="text-align:right;">{{ rupiah($value->behi) }}</td>
                <td style="text-align:right;">{{ rupiah($value->btt) }}</td>
                <td style="text-align:right;">{{ rupiah($value->bt) }}</td>
                <td style="text-align:right;">{{ rupiah($dataTotal) }}</td>
            </tr>
            @endforeach
            <tr>
                <td bgcolor="#CCCCCC" style="text-align:center;" colspan="2"><b>TOTAL</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalbape) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalberjas) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalbemo) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalbehi) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalbtt) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalbt) }}</b></td>
                <td bgcolor="#CCCCCC" style="text-align:right;"><b>{{ rupiah($totalAkhir) }}</b></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
