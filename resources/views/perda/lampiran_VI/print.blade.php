<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lampiran VI</title>
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

        .bordered td:nth-child(n+2) {
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
                <td>Lampiran VI</td>
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
        <h4 style="margin: 2px 0px;">LAPORAN ARUS KAS</h4>
        <h4 style="margin: 2px 0px;">PER 31 DESEMBER {{ tahun_anggaran() }} DAN {{ tahun_anggarans() }}</h4>
        <h4 style="margin: 2px 0px;">UNTUK TAHUN YANG BERAKHIR SAMPAI DENGAN 31 DESEMBER {{ tahun_anggaran() }} DAN {{ tahun_anggarans() }}</h4>
        <h4 style="margin: 2px 0px;">METODE LANGSUNG</h4>
        <div style="clear: both;"></div>
    </div> <br>
    <table class="bordered">
        <thead align="center">
            <tr>
                <th bgcolor="#CCCCCC">Uraian</th>
                <th bgcolor="#CCCCCC">{{ tahun_anggaran() }}</th>
                <th bgcolor="#CCCCCC">{{ tahun_anggarans() }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $value)
            @if($value->uraian == '')
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                </tr>
            @elseif($value->nilai_null == '1')
                <tr class="{{ $value->is_bold ? 'bold' : '' }} ">
                    <td>{{ $value->uraian }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @else
                <tr class="{{ $value->is_bold ? 'bold' : '' }} ">
                    <td>{{ $value->uraian }}</td>
                    <td>
                        {{ $value->nilai >= 0 ? rupiah($value->nilai) : '(' . rupiah(abs($value->nilai)) . ')' }}
                    </td>
                    <td>
                        {{ $value->nilai_lalu >= 0 ? rupiah($value->nilai_lalu) : '(' . rupiah(abs($value->nilai_lalu)) . ')' }}
                    </td>
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
