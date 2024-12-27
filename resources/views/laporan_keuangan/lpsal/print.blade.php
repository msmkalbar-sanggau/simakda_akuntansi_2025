<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LPSAL</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }

        .bordered {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            line-height: 1.5;
        }

        .bordered th {
            border: 1px solid black;
            background-color: #cccccc;
            padding: 4px;
        }

        .bordered td {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .bordered tr:last-child td {
            border-bottom: 1px solid black;
        }

        .bordered td:nth-child(n+3) {
            text-align: right;
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
        <h4 style="margin: 8px 0px;">LAPORAN PERUBAHAN SALDO ANGGARAN LEBIH</h4>
        <h4 style="margin: 8px 0px;">PER {{ $arraybulan[$bulan] }} {{ tahun_anggaran() }}</h4>
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
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @foreach ($data as $value)
                @switch($value->nor)
                    @case('1')
                        <tr>
                            <td class="center">{{ $value->nor }}</td>
                            <td class="{{ $value->is_bold ? 'bold' : '' }}"
                                style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                                {{ $value->uraian }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $kas_lalu_thn->saldo_awal >= 0 ? rupiah($kas_lalu_thn->saldo_awal) : '(' . rupiah(abs($kas_lalu_thn->saldo_awal)) . ')' }}
                            </td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $kas_lalu_thn_s >= 0 ? rupiah($kas_lalu_thn_s) : '(' . rupiah(abs($kas_lalu_thn_s)) . ')' }}
                            </td>
                        </tr>
                    @break

                    @case('2')
                        <tr>
                            <td class="center">{{ $value->nor }}</td>
                            <td class="{{ $value->is_bold ? 'bold' : '' }}"
                                style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                                {{ $value->uraian }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $pengguna_sal->nilai >= 0 ? rupiah($pengguna_sal->nilai) : '(' . rupiah(abs($pengguna_sal->nilai)) . ')' }}
                            </td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $pengguna_sal_s->nilai >= 0 ? rupiah($pengguna_sal_s->nilai) : '(' . rupiah(abs($pengguna_sal_s->nilai)) . ')' }}
                            </td>
                        </tr>
                    @break

                    @case('3')
                        <tr>
                            <td class="center">{{ $value->nor }}</td>
                            <td class="{{ $value->is_bold ? 'bold' : '' }}"
                                style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                                {{ $value->uraian }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $sub_total_3 >= 0 ? rupiah($sub_total_3) : '(' . rupiah(abs($sub_total_3)) . ')' }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $sub_total_3_s >= 0 ? rupiah($sub_total_3_s) : '(' . rupiah(abs($sub_total_3_s)) . ')' }}
                            </td>
                        </tr>
                    @break

                    @case('4')
                        <tr>
                            <td class="center">{{ $value->nor }}</td>
                            <td class="{{ $value->is_bold ? 'bold' : '' }}"
                                style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                                {{ $value->uraian }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $silpa >= 0 ? rupiah($silpa) : '(' . rupiah(abs($silpa)) . ')' }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $silpa_s->nilai >= 0 ? rupiah($silpa_s->nilai) : '(' . rupiah(abs($silpa_s->nilai)) . ')' }}
                            </td>
                        </tr>
                    @break

                    @case('5')
                        <tr>
                            <td class="center">{{ $value->nor }}</td>
                            <td class="{{ $value->is_bold ? 'bold' : '' }}"
                                style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                                {{ $value->uraian }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $sub_total_4 >= 0 ? rupiah($sub_total_4) : '(' . rupiah(abs($sub_total_4)) . ')' }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $sub_total_4_s >= 0 ? rupiah($sub_total_4_s) : '(' . rupiah(abs($sub_total_4_s)) . ')' }}
                            </td>
                        </tr>
                    @break

                    @case('6')
                        <tr>
                            <td class="center">{{ $value->nor }}</td>
                            <td class="{{ $value->is_bold ? 'bold' : '' }}"
                                style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                                {{ $value->uraian }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $koreksi >= 0 ? rupiah($koreksi) : '(' . rupiah(abs($koreksi)) . ')' }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $koreksi_s >= 0 ? rupiah($koreksi_s) : '(' . rupiah(abs($koreksi_s)) . ')' }}</td>
                        </tr>
                    @break

                    @case('7')
                        <tr>
                            <td class="center">{{ $value->nor }}</td>
                            <td class="{{ $value->is_bold ? 'bold' : '' }}"
                                style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                                {{ $value->uraian }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">{{ rupiah(0) }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">{{ rupiah(0) }}</td>
                        </tr>
                    @break

                    @default
                        <tr class="{{ $value->is_bold ? 'bold' : '' }} tr">
                            <td class="center">{{ $value->nor }}</td>
                            <td class="{{ $value->is_bold ? 'bold' : '' }}"
                                style="<?= $value->padding ? 'padding-left:' . $value->padding . 'px;' : '' ?>">
                                {{ $value->uraian }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $saldoAkhir >= 0 ? rupiah($saldoAkhir) : '(' . rupiah(abs($saldoAkhir)) . ')' }}</td>
                            <td class="right {{ $value->is_bold ? 'bold' : '' }}">
                                {{ $saldoAkhir_s >= 0 ? rupiah($saldoAkhir_s) : '(' . rupiah(abs($saldoAkhir_s)) . ')' }}</td>
                        </tr>
                    @break
                @endswitch
            @endforeach
        </body>
    </table>
    <div style="padding: 16px; font-size: 14px;">
        <div style="float: right; text-align: center;">
            {{ $daerah->daerah }},{{ tanggal($tgl_ttd) }}<br />
            {{ $ttd->jabatan }}
            <div style="height: 64px;"></div>
            <b><u><?= $ttd->nama ?></u></b>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>

</html>
