<?php

namespace App\Http\Controllers\Perda\LampiranV;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranVController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'ttd' => DB::table('ms_ttd_perda')->get(),
        ];
        return view('perda.lampiran_V.index')->with($data);
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
        $tgl_ttd = $request->tgl_ttd;
        $ttd = $request->ttd;
        $jenis = $request->jenis;
        $thn = tahun_anggaran();
        $thn_lalu = $thn - 1;

        $ttd_nm = DB::table('ms_ttd_perda')->where(['nama' => $ttd])->first();

        $data = DB::select(
            "SELECT group_id, group_name, padding, is_bold, right_align,
                        SUM ( jurnal.nilai_berjalan ) AS nilai_berjalan,
                        SUM ( jurnal.nilai_lalu ) AS nilai_lalu
                    FROM map_lampiran_perda_v
                    LEFT JOIN (
                            SELECT
                                kd_rek6,
                                SUM ( nilai_berjalan ) AS nilai_berjalan,
                                SUM ( nilai_lalu ) AS nilai_lalu
                            FROM
                                (
                                    SELECT
                                        trdju_pkd.kd_rek6,
                                    CASE
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '1'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn' THEN
                                            SUM ( debet ) - SUM ( kredit )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '2'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn'  THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        ELSE 0
                                    END AS nilai_berjalan,
                                    CASE
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '1'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn_lalu' THEN
                                            SUM ( debet ) - SUM ( kredit )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '2'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn_lalu' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        ELSE 0
                                    END AS nilai_lalu
                                        FROM
                                            trhju_pkd
                                        JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher
                                            AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                                        WHERE
                                            left(trdju_pkd.kd_rek6,1) IN ( '1', '2' )
                                        GROUP BY
                                        trdju_pkd.kd_rek6, trhju_pkd.tgl_voucher
                                ) jurnal
                                    GROUP BY kd_rek6
                        ) jurnal ON LEFT ( jurnal.kd_rek6, LEN( kd_rek ) ) = map_lampiran_perda_v.kd_rek
                            GROUP BY
                                group_id,
                                group_name,
                                padding,
                                is_bold,
                                show_kd_rek,
                                right_align
                            ORDER BY
                            group_id, group_name"
        );

        // tahun lalu
        $pen_bel_lalu_1 = collect(\DB::select(
            "SELECT pendapatan_lo, belanja_lo
                FROM
                    (
                    SELECT
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' THEN a.kredit- a.debet ELSE 0 END ) pendapatan_lo,
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' THEN a.debet- a.kredit ELSE 0 END ) belanja_lo
                FROM
                    trdju_pkd_lalu a
                    INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) < '$thn_lalu'
            ) nilaiAkhir"
        ))->first();

        $lpe_lalu_1 = collect(\DB::select(
            "SELECT srat, knp, ll
                FROM
                (
                    SELECT
                        SUM ( CASE WHEN reev = '1' THEN a.kredit - a.debet ELSE 0 END ) srat,
                        SUM ( CASE WHEN reev = '2' THEN a.kredit - a.debet ELSE 0 END ) knp,
                        SUM ( CASE WHEN reev = '3' THEN a.kredit - a.debet ELSE 0 END ) ll
                    FROM
                    trdju_pkd_lalu a
                    INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                    WHERE a.kd_rek6 = '310101010001' and year(b.tgl_voucher) < '$thn_lalu'
                ) nilaiAkhir"
        ))->first();

        $query = collect(\DB::select(
            "SELECT dd
                FROM
                (
                    SELECT
                        SUM ( CASE WHEN b.tabel = '1' and reev = '0' THEN a.kredit - a.debet ELSE 0 END ) dd
                    FROM
                    trdju_pkd_lalu a
                    INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                    WHERE a.kd_rek6 = '310101010001' and year(b.tgl_voucher) < '$thn'
                ) nilaiAkhir"
        ))->first();

        $saldo_awal_lalu = $pen_bel_lalu_1->pendapatan_lo - $pen_bel_lalu_1->belanja_lo +  $lpe_lalu_1->srat + $lpe_lalu_1->knp + $lpe_lalu_1->ll + $query->dd;

        $pen_bel_lalu = collect(\DB::select(
            "SELECT pendapatan_lo, belanja_lo
                FROM
                    (
                    SELECT
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' THEN a.kredit- a.debet ELSE 0 END ) pendapatan_lo,
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' THEN a.debet- a.kredit ELSE 0 END ) belanja_lo
                FROM
                    trdju_pkd_lalu a
                    INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn_lalu'
            ) nilaiAkhir"
        ))->first();

        $lpe_lalu = collect(\DB::select(
            "SELECT srat, knp, ll
                FROM
                (
                    SELECT
                        SUM ( CASE WHEN reev = '1' THEN a.kredit - a.debet ELSE 0 END ) srat,
                        SUM ( CASE WHEN reev = '2' THEN a.kredit - a.debet ELSE 0 END ) knp,
                        SUM ( CASE WHEN reev = '3' THEN a.kredit - a.debet ELSE 0 END ) ll
                    FROM
                    trdju_pkd_lalu a
                    INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                    WHERE a.kd_rek6 = '310101010001' and year(b.tgl_voucher) = '$thn_lalu'
                ) nilaiAkhir"
        ))->first();

        $surplus_lalu = $pen_bel_lalu->pendapatan_lo - $pen_bel_lalu->belanja_lo;
        $saldo_akhir_lalu = $saldo_awal_lalu + $surplus_lalu + $lpe_lalu->srat + $lpe_lalu->knp + $lpe_lalu->ll;

        $rk_lalu = collect(\DB::select(
            "SELECT rk_ppkd, rk_skpd, nilai_utang
                FROM
                (
                    SELECT
                        SUM ( CASE WHEN a.kd_rek6 = '310301010001' THEN a.kredit - a.debet ELSE 0 END ) rk_ppkd,
                        SUM ( CASE WHEN a.kd_rek6 = '111301010001' THEN a.debet - a.kredit ELSE 0 END ) rk_skpd,
                        SUM ( CASE WHEN left(a.kd_rek6, 1) = '2' THEN a.kredit - a.debet ELSE 0 END ) nilai_utang
                    FROM
                    trdju_pkd_lalu a
                    INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                    WHERE year(b.tgl_voucher) <= '$thn_lalu'
                ) nilaiAkhir"
        ))->first();

        $eku_lalu = $saldo_akhir_lalu + $rk_lalu->rk_ppkd - $rk_lalu->rk_skpd;
        $eku_tang_lalu = $saldo_akhir_lalu + $rk_lalu->nilai_utang + $rk_lalu->rk_ppkd - $rk_lalu->rk_skpd;

        // tahun berjalan
		$saldo_awal = $saldo_akhir_lalu;

        $pen_bel = collect(\DB::select(
            "SELECT pendapatan_lo, belanja_lo
                FROM
                    (
                    SELECT
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' THEN a.kredit- a.debet ELSE 0 END ) pendapatan_lo,
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' THEN a.debet- a.kredit ELSE 0 END ) belanja_lo
                FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn'
            ) nilaiAkhir"
        ))->first();

        $lpe = collect(\DB::select(
            "SELECT srat, knp, ll
                FROM
                (
                    SELECT
                        SUM ( CASE WHEN reev = '1' THEN a.kredit - a.debet ELSE 0 END ) srat,
                        SUM ( CASE WHEN reev = '2' THEN a.kredit - a.debet ELSE 0 END ) knp,
                        SUM ( CASE WHEN reev = '3' THEN a.kredit - a.debet ELSE 0 END ) ll
                    FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                    WHERE a.kd_rek6 = '310101010001' and year(b.tgl_voucher) = '$thn'
                ) nilaiAkhir"
        ))->first();

        $rk = collect(\DB::select(
            "SELECT rk_ppkd, rk_skpd, nilai_utang
                FROM
                (
                    SELECT
                        SUM ( CASE WHEN a.kd_rek6 = '310301010001' THEN a.kredit - a.debet ELSE 0 END ) rk_ppkd,
                        SUM ( CASE WHEN a.kd_rek6 = '111301010001' THEN a.debet - a.kredit ELSE 0 END ) rk_skpd,
                        SUM ( CASE WHEN left(a.kd_rek6, 1) = '2' THEN a.kredit - a.debet ELSE 0 END ) nilai_utang
                    FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                    WHERE year(b.tgl_voucher) <= '$thn'
                ) nilaiAkhir"
        ))->first();

        $surplus = $pen_bel->pendapatan_lo - $pen_bel->belanja_lo;

        $eku = $saldo_awal + $surplus + $lpe->srat + $lpe->knp + $lpe->ll + $rk->rk_ppkd - $rk->rk_skpd;
        $eku_tang = $eku + $rk->nilai_utang;

        $view = view('perda.lampiran_V.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'eku' => $eku,
            'eku_lalu' => $eku_lalu,
            'eku_tang_lalu' => $eku_tang_lalu,
            'eku_tang' => $eku_tang,
            'tgl_ttd' => $tgl_ttd,
            'ttd_nm' => $ttd_nm,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)
                ->setOption('footer-right', "Halaman [page] dari [topage]")
                ->setOption('footer-font-size', 9)
                ->setOrientation('landscape')->setPaper('a4');
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
