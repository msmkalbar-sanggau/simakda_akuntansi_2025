<?php

namespace App\Http\Controllers\Perda\LampiranII;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranIIController extends Controller
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
        return view('perda.lampiran_II.index')->with($data);
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

        $data = DB::select("SELECT * FROM map_lpsal_permen_77 ORDER BY nor");

        // tahun 2020 sampai 2021 kas
        $kas_lalu_thns = collect(\DB::select(
            "SELECT isnull(thn_m1,0) as nilai FROM map_lpsal_permen_77_lalu where seq='40'"
        ))->first();

        $psal_thns = collect(\DB::select(
            "SELECT isnull(thn_m1*-1,0) as nilai FROM map_lpsal_permen_77_lalu where seq='40'"
        ))->first();

        $silpa_s = collect(\DB::select(
            "SELECT
                ((pendapatan+penerimaan)-(belanja+pengeluaran)) as nilai
            FROM
            (
                SELECT
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '4' THEN a.kredit-a.debet ELSE 0 END ) pendapatan,
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '5' THEN a.debet-a.kredit ELSE 0 END ) belanja,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '61' THEN a.kredit-a.debet ELSE 0 END ) penerimaan,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '62' THEN a.debet-a.kredit ELSE 0 END ) pengeluaran
                FROM trdju_pkd_lalu a
                INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn_lalu'
            ) saldoAkhirKas"
        ))->first();

        $lain_s = collect(\DB::select(
            "SELECT
                SUM ( CASE WHEN left(a.kd_rek6, 4) = '6101' THEN a.kredit-a.debet ELSE 0 END ) as nilai
            FROM trdju_pkd_lalu a
            INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
            AND a.no_voucher= b.no_voucher
            WHERE year(b.tgl_voucher) = '$thn_lalu'"
        ))->first();

        $lain_s1 = collect(\DB::select(
            "SELECT
                SUM ( CASE WHEN left(a.kd_rek6, 4) = '6101' THEN a.kredit-a.debet ELSE 0 END )*-1 as nilai
            FROM trdju_pkd_lalu a
            INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
            AND a.no_voucher= b.no_voucher
            WHERE year(b.tgl_voucher) = '$thn_lalu'"
        ))->first();

        $lainAkhirS = $lain_s->nilai - $kas_lalu_thns->nilai;
        $silpaAkhirS = $silpa_s->nilai - $lainAkhirS;
        $saldoAngS = $kas_lalu_thns->nilai + $psal_thns->nilai;
        $saldoAngAkhirS = $saldoAngS + $silpaAkhirS;
        $koreksiS = 0;
        $saldoAKhirS = $saldoAngAkhirS + $koreksiS + $lainAkhirS;

        // tahun 2021 sampai 2022 kas
        $kas_lalu_thn = collect(\DB::select(
            "SELECT
                ((pendapatan+penerimaan)-(belanja+pengeluaran)) as nilai
            FROM
            (
                SELECT
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '4' THEN a.kredit-a.debet ELSE 0 END ) pendapatan,
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '5' THEN a.debet-a.kredit ELSE 0 END ) belanja,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '61' THEN a.kredit-a.debet ELSE 0 END ) penerimaan,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '62' THEN a.debet-a.kredit ELSE 0 END ) pengeluaran
                FROM trdju_pkd_lalu a
                INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn_lalu'
            ) saldoAkhirKas"
        ))->first();

        $psal_thn = collect(\DB::select(
            "SELECT
                ((pendapatan+penerimaan)-(belanja+pengeluaran))*-1 as nilai
            FROM
            (
                SELECT
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '4' THEN a.kredit-a.debet ELSE 0 END ) pendapatan,
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '5' THEN a.debet-a.kredit ELSE 0 END ) belanja,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '61' THEN a.kredit-a.debet ELSE 0 END ) penerimaan,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '62' THEN a.debet-a.kredit ELSE 0 END ) pengeluaran
                FROM trdju_pkd_lalu a
                INNER JOIN trhju_pkd_lalu b ON a.kd_unit= b.kd_skpd
                AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn_lalu'
            ) saldoAkhirKas"
        ))->first();

        $silpa = collect(\DB::select(
            "SELECT
                ((pendapatan+penerimaan)-(belanja+pengeluaran)) as nilai
            FROM
            (
                SELECT
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '4' THEN a.kredit-a.debet ELSE 0 END ) pendapatan,
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '5' THEN a.debet-a.kredit ELSE 0 END ) belanja,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '61' THEN a.kredit-a.debet ELSE 0 END ) penerimaan,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '62' THEN a.debet-a.kredit ELSE 0 END ) pengeluaran
                FROM trdju_pkd a
                INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn'
            ) saldoAkhirKas"
        ))->first();

        $lain = collect(\DB::select(
            "SELECT
                SUM ( CASE WHEN left(a.kd_rek6, 4) = '6101' THEN a.kredit-a.debet ELSE 0 END ) as nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
            AND a.no_voucher= b.no_voucher
            WHERE year(b.tgl_voucher) = '$thn'"
        ))->first();

        $lain1 = collect(\DB::select(
            "SELECT
                SUM ( CASE WHEN left(a.kd_rek6, 4) = '6101' THEN a.kredit-a.debet ELSE 0 END )*-1 as nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
            AND a.no_voucher= b.no_voucher
            WHERE year(b.tgl_voucher) = '$thn'"
        ))->first();

        $lainAkhir = $lain->nilai - $kas_lalu_thn->nilai;
        $silpaAkhir = $silpa->nilai - $lainAkhir;
        $saldoAng = $kas_lalu_thn->nilai + $psal_thn->nilai;
        $saldoAngAkhir = $saldoAng + $silpaAkhir;
        $koreksi = 0;
        $saldoAKhir = $saldoAngAkhir + $koreksi + $lainAkhir;

        $view = view('perda.lampiran_II.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'lain1' => $lain1,
            'lain_s1' => $lain_s1,
            'kas_lalu_thns' => $kas_lalu_thns,
            'kas_lalu_thn' => $kas_lalu_thn,
            'silpaAkhir' => $silpaAkhir,
            'lainAkhir' => $lainAkhir,
            'koreksi' => $koreksi,
            'saldoAKhir' => $saldoAKhir,
            'silpaAkhirS' => $silpaAkhirS,
            'lainAkhirS' => $lainAkhirS,
            'koreksiS' => $koreksiS,
            'saldoAKhirS' => $saldoAKhirS,
            'ttd' => $ttd,
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
