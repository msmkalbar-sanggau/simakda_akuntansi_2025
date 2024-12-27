<?php

namespace App\Http\Controllers\LaporanMonitoring\LaporanKONSNeraca;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LAPKONSOLNERACAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('laporan_monitoring.laporan_konsolidasi_neraca.index');
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
        $thn_lalu = $thn - 1;
        $thn_lalu_1 = $thn_lalu - 1;
        $bulan < 10 ? $xbulan = "0$bulan" : $xbulan = $bulan;

        $modtahun = $thn % 4;
        if ($modtahun == 0) {
            $bulanx = ".31 JANUARI.29 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
        } else {
            $bulanx = ".31 JANUARI.28 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
        }
        $arraybulan = explode(".", $bulanx);

        $data = DB::select(
            "SELECT
                x.kd_skpd,
                (SELECT nm_skpd from ms_skpd where kd_skpd = x.kd_skpd) as nm_skpd,
                SUM (aset) as nilai_aset,
                SUM (nilai + nilai1 + nilai2) as nilai_kew_eku
            FROM (
                SELECT
                    x.kd_skpd,
                    SUM(pendapatan_ll - belanja_ll + pendapatan_l - belanja_l + srat + knp + ll) as nilai,
                    '0' as nilai1,
                    '0' as aset,
                    '0' as nilai2
                    FROM (
                        SELECT
                            b.kd_skpd as kd_skpd,
                            SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' and year(b.tgl_voucher) < ? THEN a.kredit- a.debet ELSE 0 END ) pendapatan_ll,
                            SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' and year(b.tgl_voucher) < ? THEN a.debet- a.kredit ELSE 0 END ) belanja_ll,
                            SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' and year(b.tgl_voucher) = ? THEN a.kredit- a.debet ELSE 0 END ) pendapatan_l,
                            SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' and year(b.tgl_voucher) = ? THEN a.debet- a.kredit ELSE 0 END ) belanja_l,
                            SUM ( CASE WHEN reev = '1' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) <= ?  THEN a.kredit - a.debet ELSE 0 END ) srat,
                            SUM ( CASE WHEN reev = '2' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) <= ? THEN a.kredit - a.debet ELSE 0 END ) knp,
                            SUM ( CASE WHEN reev = '3' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) <= ? THEN a.kredit - a.debet ELSE 0 END ) ll
                        FROM
                            trdju_pkd_lalu a
                        INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                        AND a.no_voucher= b.no_voucher
                        Group by b.kd_skpd
                    ) x group by x.kd_skpd UNION

                SELECT
                    x.kd_skpd,
                    '0' as nilai,
                    SUM (pendapatan_lo - belanja_lo + srat + knp + ll) as nilai1,
                    '0' as aset,
                    '0' as nilai2
                    FROM (
                        SELECT
                            b.kd_skpd as kd_skpd,
                            SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' and year(b.tgl_voucher) = ? THEN a.kredit- a.debet ELSE 0 END ) pendapatan_lo,
                            SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' and year(b.tgl_voucher) = ? THEN a.debet- a.kredit ELSE 0 END ) belanja_lo,
                            SUM ( CASE WHEN reev = '1' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) = ? THEN a.kredit - a.debet ELSE 0 END ) srat,
                            SUM ( CASE WHEN reev = '2' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) = ? THEN a.kredit - a.debet ELSE 0 END ) knp,
                            SUM ( CASE WHEN reev = '3' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) = ? THEN a.kredit - a.debet ELSE 0 END ) ll
                        FROM
                            trdju_pkd_lalu a
                            INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                            AND a.no_voucher= b.no_voucher
                            Group by b.kd_skpd
                    ) x group by x.kd_skpd UNION

                SELECT
                    x.kd_skpd,
                    '0' as nilai,
                    '0' as nilai1,
                    SUM (aset - rk_skpd_l) as aset,
                    SUM (pendapatan_lo - belanja_lo + srat + knp + ll + rk_ppkd_l - rk_skpd_l + kewajiban_l) as nilai2
                    FROM (
                        SELECT
                            b.kd_skpd as kd_skpd,
                            SUM ( CASE WHEN a.kd_rek6 = '111301010001' AND left(b.tgl_voucher,7) <= '$thn-$xbulan' THEN debet - kredit ELSE 0 END ) rk_skpd_l,
                            SUM ( CASE WHEN a.kd_rek6 = '310301010001' AND left(b.tgl_voucher,7) <= '$thn-$xbulan' THEN kredit - debet ELSE 0 END ) rk_ppkd_l,
                            SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' and year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN a.kredit- a.debet ELSE 0 END ) pendapatan_lo,
                            SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' and year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN a.debet- a.kredit ELSE 0 END ) belanja_lo,
                            SUM ( CASE WHEN reev = '1' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN a.kredit - a.debet ELSE 0 END ) srat,
                            SUM ( CASE WHEN reev = '2' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN a.kredit - a.debet ELSE 0 END ) knp,
                            SUM ( CASE WHEN reev = '3' and a.kd_rek6 = '310101010001' and year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN a.kredit - a.debet ELSE 0 END ) ll,
                            SUM ( CASE WHEN left( a.kd_rek6, 1) = '2' AND left(b.tgl_voucher,7) <= '$thn-$xbulan' THEN kredit - debet ELSE 0 END ) kewajiban_l,
                            SUM ( CASE WHEN left( a.kd_rek6, 1) = '1' AND left(b.tgl_voucher,7) <= '$thn-$xbulan' THEN debet - kredit ELSE 0 END ) aset
                        FROM
                            trdju_pkd a
                        INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher
                        AND a.kd_unit= b.kd_skpd
                        Group by b.kd_skpd
                    ) x group by x.kd_skpd

            ) x GROUP BY x.kd_skpd Order by x.kd_skpd",
            [
                $thn_lalu_1, $thn_lalu_1, $thn_lalu_1, $thn_lalu_1, $thn_lalu_1, $thn_lalu_1, $thn_lalu_1,
                $thn_lalu, $thn_lalu, $thn_lalu, $thn_lalu, $thn_lalu
            ]
        );

        $view = view('laporan_monitoring.laporan_konsolidasi_neraca.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
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
