<?php

namespace App\Http\Controllers\LaporanMonitoring\LaporanSisaBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LAPSISABANKController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('laporan_monitoring.laporan_sisa_bank.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        setlocale(LC_ALL, 'Indonesian');
        $bulan = $request->bulan;
        $jenis = $request->jenis;
        $thn = tahun_anggaran();
        $modtahun = $thn % 4;
        if ($modtahun == 0) {
            $bulanx = ".31 JANUARI.29 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
        } else {
            $bulanx = ".31 JANUARI.28 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
        }
        $arraybulan = explode(".", $bulanx);
        $data = DB::select(
            "SELECT
                trans.kd_skpd,
                (SELECT nm_skpd FROM ms_skpd WHERE kd_skpd = trans.kd_skpd) as nm_skpd,
                SUM(terima) - SUM(keluar) AS saldo_bank
            FROM
            (
				SELECT kd_skpd, tgl_panjar AS tgl, 0 AS terima, nilai AS keluar FROM tr_panjar WHERE jns = '1' UNION ALL
				SELECT kd_skpd, tgl_kas AS tgl, nilai AS terima, 0 AS keluar FROM tr_jpanjar WHERE jns = '1' UNION ALL
				SELECT kd_skpd, tgl_terima AS tgl, nilai AS terima, 0 AS keluar FROM trhsp2d WHERE (no_terima <> '' AND no_terima IS NOT NULL) UNION ALL
				SELECT kd_skpd, tgl_bukti AS tgl, nilai AS terima, 0 AS keluar FROM trhtrmpot UNION ALL
				SELECT kd_skpd, tgl_bukti AS tgl, nilai AS terima, 0 AS keluar FROM trhINlain WHERE pay = 'BANK' UNION ALL
				SELECT kd_skpd, tgl_kas AS tgl, 0 AS terima, nilai AS keluar FROM tr_setorpelimpahan_bank UNION ALL
				SELECT a.kd_skpd, tgl_bukti AS tgl, 0 AS terima, total AS keluar FROM trhtransout a JOIN trhsp2d b ON a.no_sp2d = b.no_sp2d WHERE (panjar <> '3' OR panjar IS NULL) UNION ALL
				SELECT kd_skpd, tgl_kas AS tgl, 0 AS terima, nilai AS keluar FROM tr_ambilsimpanan WHERE (status_drop != '1' OR status_drop IS NULL) UNION ALL
				SELECT kd_skpd, tgl_bukti AS tgl, 0 AS terima, nilai AS keluar FROM trhoutlain WHERE pay = 'BANK' UNION ALL
				SELECT kd_skpd, tgl_bukti AS tgl, 0 AS terima, nilai AS keluar FROM trhstrpot UNION ALL
				SELECT a.kd_skpd, a.tgl_sts AS tgl, 0 AS terima, SUM(b.rupiah) AS keluar FROM trhkasin_pkd a INNER JOIN trdkasin_pkd b ON a.no_sts = b.no_sts AND a.kd_skpd = b.kd_skpd WHERE jns_trans NOT IN ('4', '2', '5') AND pot_khusus in ('0', '2') GROUP BY a.kd_skpd, a.tgl_sts
			) trans
            WHERE MONTH(tgl) <= ? AND YEAR(tgl) = ? GROUP BY trans.kd_skpd ORDER BY kd_skpd",
            [
                $bulan, $thn
            ]
        );

        $view = view('laporan_monitoring.laporan_sisa_bank.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'jenis' => $jenis,
            'bulan' => $bulan,
            'arraybulan' => $arraybulan,
            'data' => $data,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream('laporan.pdf');
        } else if ($request->jenis == 'excel') {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=laporan.xls");
            return $view;
        } else {
            return $view;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
