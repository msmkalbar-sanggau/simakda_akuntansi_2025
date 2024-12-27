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
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Urusan</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Bidang</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Unit</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Sub</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nama OPD</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Rek_1</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nama Rek 1</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Rek_2</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nama Rek 2</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Rek_3</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nama Rek 3</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Rek_4</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nama Rek 4</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Rek_5</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nama Rek 5</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Rek_6</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nama Rek 6</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Prog</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Kd_Keg</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>No_SPM</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Tgl_SPM</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>No_SP2D</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Tgl_SP2D</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nilai SP2D</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Jenis_SP2D</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Uraian SP2D</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Jenis_Belanja</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Objek_Belanja</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Norek Penerima</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nama Pemilik Norek Penerima</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Nm_perusahaan</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>No_Kontrak</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Tgl_Kontrak</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Waktu_Kontrak</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>No_Addendum</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Tgl_Addendum</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>waktu_Addendum</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>nilai_Addendum</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>keperluan_kontrak</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>nilai_kontrak</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>IWP</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>IWP1</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>IWP8</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Taperum</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>PPH21</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Lain2</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>JKK ASN</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>JKK P3K</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>JKM ASN</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>JKM P3K</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>BPJS</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>BPJS1</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>Sewa Rumah</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>LBHTUNJ</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>IUJK</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>BPJS4</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>IWP3</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>PPN</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>PPH21PJK</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>PPH22</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>PPH23</b></td>
                <td class="td" bgcolor="#CCCCCC"><b>PPHFINAL</b></td>
            </tr>
        </thead>

        <tbody>
            @foreach($data as $key => $value)
                <tr class="tr">
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_urusan }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_bidang }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_unit }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_sub_kegiatan }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_opd }}</td>
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
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_program }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->kd_kegiatan }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->no_spm }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->tgl_spm }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->no_sp2d }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->tgl_sp2d }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->nilai_sp2d) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->jns_sp2d }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->uraian_sp2d }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->jenis_belanja }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->objek_belanja }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->norek_penerima }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nama_pemilik_norek }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nm_perusahaan }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->no_kontrak }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->tgl_kontrak }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->waktu_kontrak }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->no_addendum }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->tgl_addendum }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->waktu_addendum }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nilai_addendum }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->keperluaan_kontrak }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ $value->nilai_kontrak }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->iwp) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->iwp1) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->iwp8) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->taperum) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->pph21) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->lain2) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->jkk_asn) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->jkk_p3k) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->jkm_asn) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->jkm_p3k) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->BPJS) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->BPJS1) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->sewarumah) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->lbhtunj) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->iujk) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->BPJS4) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->iwp3) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->ppn) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->pph21pjk) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->pph22) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->pph23) }}</td>
                    <td class="td" style="vertical-align:top;border-top: solid 1px black;border-bottom: none;">{{ rupiah($value->pphfinal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>


</html>
