<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran I.2</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
        }

        .bordered {
            width: 100%;
            border-collapse: collapse;
        }

        .bordered th {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered td {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .bordered td:nth-child(n+2) {
            text-align: right;
        }

        .border-x {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .bordered tr:last-child td {
            border-bottom: 1px solid black;
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
            <td>LAMPIRAN I.2<br>
            PERATURAN DAERAH {{ strtoupper($header1->nm_pemda) }} <br>
            NOMOR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAHUN {{ tahun_anggarant() }}<br>
            TENTANG PERTANGGUNGJAWABAN PELAKSANAAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH
            {{ strtoupper($header1->nm_pemda) }} TAHUN ANGGARAN {{ tahun_anggaran() }}</td>
            </td>
        </tr>
    </table>
    <div style="clear: both;"></div> <br>
    <div style="text-align: center; font-size:14px;">
        <h4 style="margin: 2px 0px;">PEMERINTAH {{ $header1->nm_pemda }}</h4>
        <h4 style="margin: 2px 0px;">RINGKASAN APBD YANG DIKLASIFIKASI MENURUT KELOMPOK DAN JENIS PENDAPATAN, BELANJA,
            DAN PEMBIAYAAN</h4>
        <h4 style="margin: 2px 0px;">TAHUN ANGGARAN{{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th>Uraian</th>
                <th>Anggaran <br> {{ tahun_anggaran() }}</th>
                <th>Realisasi <br> {{ tahun_anggaran() }}</th>
                <th>%</th>
                <th>Realisasi <br> {{ tahun_anggaran() - 1 }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $value)
                @php
                    if ($value->group_id == '1') {
                        $pendapatan_ang = $value->anggaran;
                        $pendapatan_real = $value->realisasi;
                    }
                    if ($value->group_id == '38') {
                        $belanja_ang = $value->anggaran;
                        $belanja_real = $value->realisasi;
                    }
                    if ($value->group_id == '76') {
                        $penerimaan_pembiayaan_ang = $value->anggaran;
                        $penerimaan_pembiayaan_real = $value->realisasi;
                    }
                    if ($value->group_id == '91') {
                        $pengeluaran_pembiayaan_ang = $value->anggaran;
                        $pengeluaran_pembiayaan_real = $value->realisasi;
                    }
                @endphp
                <tr>
                    <td class="{{ $value->is_bold ? 'bold' : '' }}"
                        style="<?= $value->padding ? 'padding-left:' . $value->padding * 3 . 'px;' : '' ?>">
                        {{ $value->group_name }}</td>
                    @if (is_null($value->group_name))
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @elseif($value->group_name == 'SURPLUS/DEFISIT')
                        @php
                            $surplus_ang = $pendapatan_ang - $belanja_ang;
                            $surplus_real = $pendapatan_real - $belanja_real;
                        @endphp
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $surplus_ang >= 0 ? rupiah($surplus_ang) : '(' . rupiah(abs($surplus_ang)) . ')' }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $surplus_real >= 0 ? rupiah($surplus_real) : '(' . rupiah(abs($surplus_real)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $surplus_ang == 0 ? rupiah(0) : rupiah(($surplus_real * 100) / $surplus_ang) }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $value->realisasi_2021 >= 0 ? rupiah($value->realisasi_2021) : '(' . rupiah(abs($value->realisasi_2021)) . ')' }}
                        </td>
                    @elseif($value->group_name == 'PEMBIAYAAN NETTO')
                        @php
                            $pembiayaan_netto_ang = $penerimaan_pembiayaan_ang - $pengeluaran_pembiayaan_ang;
                            $pembiayaan_netto_real = $penerimaan_pembiayaan_real - $pengeluaran_pembiayaan_real;
                        @endphp
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $pembiayaan_netto_ang >= 0 ? rupiah($pembiayaan_netto_ang) : '(' . rupiah(abs($pembiayaan_netto_ang)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $pembiayaan_netto_real >= 0 ? rupiah($pembiayaan_netto_real) : '(' . rupiah(abs($pembiayaan_netto_real)) . ')' }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $pembiayaan_netto_ang == 0 ? rupiah(0) : rupiah(($pembiayaan_netto_real * 100) / $pembiayaan_netto_ang) }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $value->realisasi_2021 >= 0 ? rupiah($value->realisasi_2021) : '(' . rupiah(abs($value->realisasi_2021)) . ')' }}
                        </td>
                    @elseif($value->group_name == 'Sisa Lebih Pembiayaan Anggaran')
                        @php
                            $sisa_ang = $surplus_ang + $pembiayaan_netto_ang;
                            $sisa_real = $surplus_real + $pembiayaan_netto_real;
                        @endphp
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $sisa_ang >= 0 ? rupiah($sisa_ang) : '(' . rupiah(abs($sisa_ang)) . ')' }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $sisa_real >= 0 ? rupiah($sisa_real) : '(' . rupiah(abs($sisa_real)) . ')' }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $sisa_ang == 0 ? rupiah(0) : rupiah(($sisa_real * 100) / $sisa_ang) }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $value->realisasi_2021 >= 0 ? rupiah($value->realisasi_2021) : '(' . rupiah(abs($value->realisasi_2021)) . ')' }}
                        </td>
                    @else
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ rupiah($value->anggaran) }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ rupiah($value->realisasi) }}</td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->realisasi * 100) / $value->anggaran) }}
                        </td>
                        <td class="{{ $value->is_bold ? 'bold' : '' }} {{ $value->border ? 'border-x' : '' }}">
                            {{ $value->realisasi_2021 >= 0 ? rupiah($value->realisasi_2021) : '(' . rupiah(abs($value->realisasi_2021)) . ')' }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding: 16px; font-size: 14px;">
        <div style="float: right; text-align: center;">
            {{-- Sungai Raya, {{ tanggal($tgl_ttd) }}<br /> --}}
            {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}<br />
            {{ $ttd_nm->jabatan }}
            <div style="height: 44px;"></div>
            <p style="text-transform:uppercase">{{ $ttd_nm->nama }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>
