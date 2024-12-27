<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LRA PROGRAM</title>
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
            src="{{ asset('template/assets/images/' . $header->logo_pemda_hp) }}" width="75" height="100" />
        <h4 style="margin: 8px 0px;">PEMERINTAH {{ strtoupper($header->nm_pemda) }}</h4>
        <h4 style="margin: 8px 0px;">LAPORAN REALISASI {{ $periode }} APBD DAN PROGNOSIS <br /> {{ $sisa_bulan }}
            BULAN BERIKUTNYA</h4>
        <h4 style="margin: 8px 0px;">TAHUN ANGGARAN {{ tahun_anggaran() }}</h4>
        <div style="clear: both;"></div>
    </div>
    @if ($skpd)
        <table style="border-left: 1px solid black; border-right: 1px solid black; width: 100%; font-size: 12px;">
            <tbody>
                <tr>
                    <td>Urusan Pemerintahan</td>
                    <td>:</td>
                    <td>{{ $urusan->kd_urusan }} - {{ $urusan->nm_urusan }}</td>
                </tr>
                <tr>
                    <td>Bidang Pemerintahan</td>
                    <td>:</td>
                    <td>{{ $bidang_urusan->kd_bidang_urusan }} - {{ $bidang_urusan->nm_bidang_urusan }}</td>
                </tr>
                <tr>
                    <td>Unit Organisasi</td>
                    <td>:</td>
                    <td>{{ substr($skpd->kd_skpd, 0, 7) }} - {{ TextUpperCase($skpd->nm_skpd) }}</td>
                </tr>
            </tbody>
        </table>
    @endif
    <table class="bordered">
        <thead>
            <tr>
                <th>KD REK</th>
                <th>URAIAN</th>
                <th>JUMLAH ANGGARAN</th>
                <th>
                    @if ($bulan != null)
                        REALISASI <br /> S/D <br /> {{ $periode }}
                    @else
                        REALISASI
                        <br /> {{ tanggal($tgl_awal) }} <br /> S/D <br />{{ tanggal($tgl_akhir) }}
                    @endif
                </th>
                <th>SISA ANGGARAN</th>
                <th>PROGNOSIS</th>
                <th>%</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
            </tr>
        </thead>

        <body>
            @foreach ($data as $key => $value)
                @php
                    $bertambah_berkurang = $value->anggaran - $value->sd_bulan_ini;
                @endphp
                @switch(strlen($value->kd_rek))
                    @case(2)
                        <tr>
                            <td>{{ $value->kd_sub_kegiatan }}.{{ dotrek($value->kd_rek) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @case(4)
                        <tr>
                            <td>{{ $value->kd_sub_kegiatan }}.{{ dotrek($value->kd_rek) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @case(6)
                        <tr>
                            <td>{{ $value->kd_sub_kegiatan }}.{{ dotrek($value->kd_rek) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @case(8)
                        <tr>
                            <td>{{ $value->kd_sub_kegiatan }}.{{ dotrek($value->kd_rek) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @case(12)
                        <tr>
                            <td>{{ $value->kd_sub_kegiatan }}.{{ dotrek($value->kd_rek) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @default
                        <tr>
                            <td><b>{{ $value->kd_sub_kegiatan }}.{{ dotrek($value->kd_rek) }}</b></td>
                            <td><b>{{ $value->nm_rek }}</b></td>
                            <td><b>{{ rupiah($value->anggaran) }}</b></td>
                            <td><b>{{ rupiah($value->sd_bulan_ini) }}</b></td>
                            <td><b>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}</b>
                            </td>
                            <td><b>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}</b>
                            </td>
                            <td><b>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}</b>
                            </td>
                        </tr>
                @endswitch
            @endforeach
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><b>JUMLAH PENDAPATAN</b></td>
                <td><b>{{ rupiah($nil_ang_pen) }}</b></td>
                <td><b>{{ rupiah($nil_rea_pen) }}</b></td>
                <td><b>{{ rupiah($sisa_rea_pen) }}</b></td>
                <td><b>{{ rupiah($sisa_rea_pen) }}</b></td>
                <td><b>{{ $nil_ang_pen == 0 ? rupiah(0) : rupiah(($nil_rea_pen * 100) / $nil_ang_pen) }}</b></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><b>JUMLAH BELANJA</b></td>
                <td><b>{{ rupiah($nil_ang_bel) }}</b></td>
                <td><b>{{ rupiah($nil_rea_bel) }}</b></td>
                <td><b>{{ rupiah($sisa_rea_bel) }}</b></td>
                <td><b>{{ rupiah($sisa_rea_bel) }}</b></td>
                <td><b>{{ $nil_ang_bel == 0 ? rupiah(0) : rupiah(($nil_rea_bel * 100) / $nil_ang_bel) }}</b></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach ($databelanja as $key => $value)
                @php
                    $bertambah_berkurang = $value->anggaran - $value->sd_bulan_ini;
                @endphp
                @switch(strlen($value->kd_rek))
                    @case(1)
                        <tr>
                            <td><b>{{ $value->kd_sub_kegiatan }}</b></td>
                            <td><b>{{ $value->nm_rek }}</b></td>
                            <td><b>{{ rupiah($value->anggaran) }}</b></td>
                            <td><b>{{ rupiah($value->sd_bulan_ini) }}</b></td>
                            <td><b>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}</b>
                            </td>
                            <td><b>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}</b>
                            </td>
                            <td><b>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}</b>
                            </td>
                        </tr>
                    @break

                    @case(2)
                        <tr>
                            <td><b>{{ $value->kd_sub_kegiatan }}</b></td>
                            <td><b>{{ $value->nm_rek }}</b></td>
                            <td><b>{{ rupiah($value->anggaran) }}</b></td>
                            <td><b>{{ rupiah($value->sd_bulan_ini) }}</b></td>
                            <td><b>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}</b>
                            </td>
                            <td><b>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}</b>
                            </td>
                            <td><b>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}</b>
                            </td>
                        </tr>
                    @break

                    @case(3)
                        <tr>
                            <td>{{ $value->kd_sub_kegiatan }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @case(4)
                        <tr>
                            <td>{{ substr($value->kd_sub_kegiatan, 0, 15) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @case(5)
                        <tr>
                            <td>{{ substr($value->kd_sub_kegiatan, 0, 15) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @case(6)
                        <tr>
                            <td>{{ substr($value->kd_sub_kegiatan, 0, 15) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @case(7)
                        <tr>
                            <td>{{ substr($value->kd_sub_kegiatan, 0, 15) }}</td>
                            <td>{{ $value->nm_rek }}</td>
                            <td>{{ rupiah($value->anggaran) }}</td>
                            <td>{{ rupiah($value->sd_bulan_ini) }}</td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}
                            </td>
                            <td>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}
                            </td>
                        </tr>
                    @break

                    @default
                        <tr>
                            <td><b>{{ $value->kd_sub_kegiatan }}</b></td>
                            <td><b>{{ $value->nm_rek }}</b></td>
                            <td><b>{{ rupiah($value->anggaran) }}</b></td>
                            <td><b>{{ rupiah($value->sd_bulan_ini) }}</b></td>
                            <td><b>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}</b>
                            </td>
                            <td><b>{{ $bertambah_berkurang >= 0 ? rupiah($bertambah_berkurang) : '(' . rupiah(abs($bertambah_berkurang)) . ')' }}</b>
                            </td>
                            <td><b>{{ $value->anggaran == 0 ? rupiah(0) : rupiah(($value->sd_bulan_ini * 100) / $value->anggaran) }}</b>
                            </td>
                        </tr>
                @endswitch
            @endforeach
            <tr>
                <td></td>
                <td><b>JUMLAH BELANJA</b></td>
                <td><b>{{ rupiah($nil_ang_bel) }}</b></td>
                <td><b>{{ rupiah($nil_rea_bel) }}</b></td>
                <td><b>{{ rupiah($sisa_rea_bel) }}</b></td>
                <td><b>{{ rupiah($sisa_rea_bel) }}</b></td>
                <td><b>{{ $nil_ang_bel == 0 ? rupiah(0) : rupiah(($nil_rea_bel * 100) / $nil_ang_bel) }}</b></td>
            </tr>
        </body>
    </table>
    @if ($ttdyt == 1)
        <div style="padding: 16px; font-size: 14px;">
            <div style="float: right; text-align: center;">
                {{ $daerah->daerah }}, {{ tanggal($tgl_ttd) }}<br />
                {{ $ttd->jabatan }}
                <div style="height: 64px;"></div>
                <b><u><?= $ttd->nama ?></u></b>
            </div>
            <div style="clear: both;"></div>
        </div>
    @endif
</body>

</html>
