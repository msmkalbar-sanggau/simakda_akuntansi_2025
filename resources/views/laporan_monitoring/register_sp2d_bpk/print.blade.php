<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        body {
            <?= $jenis == 'pdf' ? 'letter-spacing: 1.2px' : '' ?>
        }

        .table {
            border: 1px solid black;
        }

        .tr .td {
            border: 1px solid black;
        }

        #trd {
            border: 1px solid black;
        }

        #tdd {
            border: 1px solid black;
            text-align: right;
        }

        #tdd1 {
            border: 1px solid black;
        }

        .font-size {
            font-size: 12px;
        }

        .margin {
            margin-top: 15px;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body class="font-size">
    <div style="text-align: center; font-size: 14px; font-weight: bold; text-align: center; text-transform: uppercase;">
        <h4 style="margin: 8px 0px;">PEMERINTAH Kabupaten Sanggau</h4>
        <h4 style="margin: 8px 0px;">Laporan Register SP2D BPK </h4>
        <div style="clear: both;"></div>
    </div>


    <table class="table font-size" style='border-collapse:collapse;' align='center' cellspacing='1' cellpadding='1'>
        <thead>
            <tr class="tr" style="overflow-y:auto;">
                <td class="td" bgcolor="#CCCCCC"><b>Kd SKPD</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm SKPD</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Jenis SPP</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>No SPP</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Tgl SPP</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Keperluan</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>No Rek</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd Rek1</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm Rek1</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd Rek2</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm Rek2</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd Rek3</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm Rek3</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd Rek4</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm Rek4</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd Rek5</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm Rek5</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd Rek6</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm Rek6</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nilai</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>No SPM</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Tgl SPM</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nilai</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>No SP2D</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd Sub Kegiatan</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm Sub Kegiatan</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Tgl SP2D</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Tgl Kas Bud</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nilai</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm Rekan</b></td>
            </tr>
        </thead>

        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach($data as $key => $value)
            @php
                $total += $value->nilai;
            @endphp
                <tr class="tr">
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_skpd }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_skpd }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->jns_spp }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->no_spp }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->tgl_spp }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->keperluan }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->no_rek }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_rek1 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_rek1 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_rek2 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_rek2 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_rek3 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_rek3 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_rek4 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_rek4 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_rek5 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_rek5 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_rek6 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_rek6 }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->nilai) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->no_spm }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->tgl_spm }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->nilai) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->no_sp2d }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_sub_kegiatan }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_sub_kegiatan }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->tgl_sp2d }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->tgl_kas_bud }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->nilai_sp2d) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nmrekan }}</td>
                </tr>
            @endforeach
            <tr class="tr">
                <td class="td" style="text-align:right;" colspan="28">Total</td>
                <td class="td" >{{ rupiah($total) }}</td>
                <td class="td" ></td>
            </tr>
        </tbody>
    </table>
</body>


</html>
