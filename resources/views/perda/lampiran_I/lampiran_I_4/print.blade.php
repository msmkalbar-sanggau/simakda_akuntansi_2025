<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran I.4</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
        }

        .bordered {
            width: 100%;
            border-collapse: collapse;
        }

        .bordered th,
        .bordered td {
            border: 1px solid black;
            padding: 4px;
        }

        .bordered td:nth-child(n+8) {
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
            <td>LAMPIRAN I.4<br>
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
        <h4 style="margin: 2px 0px;">REKAPITULASI REALISASI BELANJA MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PROGRAM, KEGIATAN, DAN SUB KEGIATAN</h4>
        <h4 style="margin: 2px 0px;">TAHUN ANGGARAN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th rowspan="3" colspan="6">Kode</th>
                <th rowspan="3">Uraian Urusan, Organisasi,<br />Program, Kegiatan<br />dan Sub Kegiatan</th>
                <th colspan="8">Kelompok Belanja</th>
            </tr>
            <tr>
                <th colspan="2">Operasi</th>
                <th colspan="2">Modal</th>
                <th colspan="2">Tidak Terduga</th>
                <th colspan="2">Transfer</th>
            </tr>
            <tr>
                <th>Anggaran</th>
                <th>Realisasi</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
                <th>Anggaran</th>
                <th>Realisasi</th>
            </tr>
        </thead>

        <tbody>
            @php
                $total_ag_operasi = 0;
                $total_r_operasi = 0;
                $total_ag_modal = 0;
                $total_r_modal = 0;
                $total_ag_btt = 0;
                $total_r_btt = 0;
                $total_ag_transfer = 0;
                $total_r_transfer = 0;
            @endphp
            @foreach($data as $value)
                @php
                    if (strlen($value->kode) == 1) {
                        $total_ag_operasi += $value->ag_operasi;
                        $total_r_operasi += $value->r_operasi;
                        $total_ag_modal += $value->ag_modal;
                        $total_r_modal += $value->r_modal;
                        $total_ag_btt += $value->ag_btt;
                        $total_r_btt += $value->r_btt;
                        $total_ag_transfer += $value->ag_transfer;
                        $total_r_transfer += $value->r_transfer;
                    }
                @endphp
            <tr>
                <td>{{ substr($value->kode, 0, 1) }}</td>
                <td>{{ substr($value->kode, 1, 2) }}</td>
                <td>{{ substr($value->kode, 3, 22) }}</td>
                <td>{{ substr($value->kode, 25, 2) }}</td>
                <td>{{ substr($value->kode, 27, 4) }}</td>
                <td>{{ substr($value->kode, 31, 2) }}</td>
                <td>{{ $value->nama }}</td>
                <td>{{ rupiah($value->ag_operasi) }}</td>
                <td>{{ rupiah($value->r_operasi) }}</td>
                <td>{{ rupiah($value->ag_modal) }}</td>
                <td>{{ rupiah($value->r_modal) }}</td>
                <td>{{ rupiah($value->ag_btt) }}</td>
                <td>{{ rupiah($value->r_btt) }}</td>
                <td>{{ rupiah($value->ag_transfer) }}</td>
                <td>{{ rupiah($value->r_transfer) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="text-align:right" colspan="7">Jumlah</td>
                <td>{{ rupiah($total_ag_operasi) }}</td>
                <td>{{ rupiah($total_r_operasi) }}</td>
                <td>{{ rupiah($total_ag_modal) }}</td>
                <td>{{ rupiah($total_r_modal) }}</td>
                <td>{{ rupiah($total_ag_btt) }}</td>
                <td>{{ rupiah($total_r_btt) }}</td>
                <td>{{ rupiah($total_ag_transfer) }}</td>
                <td>{{ rupiah($total_r_transfer) }}</td>
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
