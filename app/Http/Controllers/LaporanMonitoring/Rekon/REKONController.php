<?php

namespace App\Http\Controllers\LaporanMonitoring\Rekon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class REKONController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'skpd' => DB::table('ms_skpd')->get(),
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama')->where(['kode' => 'BUD'])->get(),
        ];
        return view('laporan_monitoring.rekon.index')->with($data);
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
        $kd_skpd = $request->kd_skpd;
        $periode = $request->periode;
        $bulan = $request->bulan;
        $jns_rekon = $request->jns_rekon;
        $tahun = tahun_anggaran();
        $jenis = $request->jenis;

        $urutan_skpd = collect(\DB::select("SELECT urutan FROM (SELECT ROW_NUMBER() OVER (ORDER BY kd_skpd ASC) AS urutan, kd_skpd FROM ms_skpd) z where kd_skpd = '$kd_skpd'"))->first();

        if ($periode) {
            $bln_awal = 3 * $periode - 2;
            $bln_akhir = 3 * $periode;

            if ($periode == '1') {
                $thn_periode = 'TRIWULAN 1';
            } else if ($periode == '2') {
                $thn_periode = 'TRIWULAN 2';
            } else if ($periode == '3') {
                $thn_periode = 'TRIWULAN 3';
            } else {
                $thn_periode = 'TRIWULAN 4';
            }
        } else {
            $pbulan = $bulan;
            $thn_periode = "BULAN '$bulan'";
        }

        $periode_clause = $bulan ? "bulan = '$pbulan'"
        : "bulan BETWEEN '$bln_awal' and '$bln_akhir'";

        if ($jns_rekon == '1') {
            $jnsRekon = "SELECT
                a.kd_skpd, MONTH ( tgl_voucher ) AS bulan, 0 AS up_skpd,
                0 AS gu_skpd, 0 AS tu_skpd, 0 AS gaji_skpd, 0 AS ls_skpd, 0 AS ppkd_skpd,
                0 AS up_kasda, 0 AS gu_kasda, 0 AS tu_kasda,
                0 AS gaji_kasda, 0 AS ls_kasda, 0 AS ph3_kasda,
                sum(CASE WHEN left(b.kd_rek6, 4) = '5101' THEN ( debet ) - ( kredit ) ELSE 0 END) bape,
                sum(CASE WHEN left(b.kd_rek6, 4) = '5102' THEN ( debet ) - ( kredit ) ELSE 0 END) berjas,
                sum(CASE WHEN left(b.kd_rek6, 2) = '52' THEN ( debet ) - ( kredit ) ELSE 0 END) bemo,
                sum(CASE WHEN left(b.kd_rek6, 4) = '5105' THEN ( debet ) - ( kredit ) ELSE 0 END) behi,
                sum(CASE WHEN left(b.kd_rek6, 4) = '5301' THEN ( debet ) - ( kredit ) ELSE 0 END) btt,
                sum(CASE WHEN left(b.kd_rek6, 2) = '54' THEN ( debet ) - ( kredit ) ELSE 0 END) bt
            FROM
                trhju_pkd a
                JOIN trdju_pkd b ON a.no_voucher = b.no_voucher
                AND a.kd_skpd = b.kd_unit
            WHERE YEAR ( a.tgl_voucher ) = '$tahun'
            GROUP BY a.kd_skpd, MONTH ( tgl_voucher)";
        } else {
            $jnsRekon = "SELECT
                a.kd_skpd, MONTH ( tgl_sp2d ) AS bulan, 0 AS up_skpd,
                0 AS gu_skpd, 0 AS tu_skpd, 0 AS gaji_skpd, 0 AS ls_skpd, 0 AS ppkd_skpd,
                0 AS up_kasda, 0 AS gu_kasda, 0 AS tu_kasda,
                0 AS gaji_kasda, 0 AS ls_kasda, 0 AS ph3_kasda,
                sum(CASE WHEN left(b.kd_rek6, 4) = '5101' THEN b.nilai ELSE 0 END) bape,
                sum(CASE WHEN left(b.kd_rek6, 4) = '5102' THEN b.nilai ELSE 0 END) berjas,
                sum(CASE WHEN left(b.kd_rek6, 2) = '52' THEN b.nilai ELSE 0 END) bemo,
                sum(CASE WHEN left(b.kd_rek6, 4) = '5105' THEN b.nilai ELSE 0 END) behi,
                sum(CASE WHEN left(b.kd_rek6, 4) = '5301' THEN b.nilai ELSE 0 END) btt,
                sum(CASE WHEN left(b.kd_rek6, 2) = '54' THEN b.nilai ELSE 0 END) bt
            FROM
                trhsp2d a
                JOIN trdspp b ON a.no_spp = b.no_spp
            WHERE a.status_bud = '1' and YEAR ( a.tgl_sp2d ) = '$tahun'
            GROUP BY a.kd_skpd, MONTH ( tgl_sp2d)";
            }

        $skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();

        $data = DB::select(
            "SELECT
                z.kd_skpd, bulan, sum(up_skpd) as up_skpd,
                (select nm_skpd from ms_skpd where kd_skpd = z.kd_skpd) as nm_skpd,
                sum(gu_skpd) as gu_skpd, sum(tu_skpd) as tu_skpd,
                sum(gaji_skpd) as gaji_skpd, sum(ls_skpd) as ls_skpd,
                sum(ppkd_skpd) as ppkd_skpd, sum(up_kasda) as up_kasda,
                sum(gu_kasda) as gu_kasda, sum(tu_kasda) as tu_kasda,
                sum(gaji_kasda) as gaji_kasda, sum(ls_kasda) as ls_kasda,
                sum(ph3_kasda) as ph3_kasda, sum(bape) as bape,
                sum(berjas) as berjas, sum(bemo) as bemo,
                sum(behi) as behi, sum(btt) as btt, sum(bt) as bt
            FROM
                (
                    SELECT
                        a.kd_skpd, MONTH ( tgl_kas_bud ) AS bulan,
                        SUM ( CASE WHEN c.jns_spp= '1' THEN a.nilai ELSE 0 END ) up_kasda,
                        SUM ( CASE WHEN c.jns_spp= '2' THEN a.nilai ELSE 0 END ) gu_kasda,
                        SUM ( CASE WHEN c.jns_spp= '3' THEN a.nilai ELSE 0 END ) tu_kasda,
                        SUM ( CASE WHEN c.jns_spp= '4' THEN a.nilai ELSE 0 END ) gaji_kasda,
                        SUM ( CASE WHEN c.jns_spp= '6' THEN a.nilai ELSE 0 END ) ls_kasda,
                        SUM ( CASE WHEN c.jns_spp= '5' THEN a.nilai ELSE 0 END ) ph3_kasda,
                        0 AS up_skpd, 0 AS gu_skpd, 0 AS tu_skpd, 0 AS gaji_skpd,
                        0 AS ls_skpd, 0 AS ppkd_skpd, 0 AS bape, 0 AS berjas, 0 AS bemo,
                        0 AS behi, 0 AS btt, 0 AS bt
                    FROM
                        trdspp a
                        INNER JOIN trhspp b ON a.no_spp = b.no_spp
                        AND a.kd_skpd = b.kd_skpd
                        INNER JOIN trhsp2d c ON a.no_spp = c.no_spp
                        AND a.kd_skpd = c.kd_skpd
                    WHERE
                        ( b.sp2d_batal <> 1 OR b.sp2d_batal IS NULL )
                        AND status_bud = 1
                        GROUP BY a.kd_skpd, MONTH ( tgl_kas_bud) UNION

                    SELECT
                        a.kd_skpd, MONTH ( tgl_sp2d ) AS bulan, 0 AS up_kasda,
                        0 AS gu_kasda, 0 AS tu_kasda, 0 AS gaji_kasda, 0 AS ls_kasda, 0 AS ph3_kasda,
                        SUM ( CASE WHEN a.jns_spp= '1' THEN c.nilai ELSE 0 END ) AS up_skpd,
                        SUM ( CASE WHEN a.jns_spp= '2' THEN c.nilai ELSE 0 END ) AS gu_skpd,
                        SUM ( CASE WHEN a.jns_spp= '3' THEN c.nilai ELSE 0 END ) AS tu_skpd,
                        SUM ( CASE WHEN a.jns_spp= '4' THEN c.nilai ELSE 0 END ) AS gaji_skpd,
                        SUM ( CASE WHEN a.jns_spp= '6' THEN c.nilai ELSE 0 END ) AS ls_skpd,
                        SUM ( CASE WHEN a.jns_spp= '5' THEN c.nilai ELSE 0 END ) AS ppkd_skpd,
                        0 AS bape, 0 AS berjas, 0 AS bemo, 0 AS behi, 0 AS btt, 0 AS bt
                    FROM
                        trhsp2d a
                        INNER JOIN trhspp b ON a.no_spp= b.no_spp
                        AND a.kd_skpd= b.kd_skpd
                        INNER JOIN trdspp c ON a.no_spp= c.no_spp
                        AND a.kd_skpd= c.kd_skpd
                    WHERE
                        ( b.sp2d_batal <> 1 OR b.sp2d_batal IS NULL )
                        GROUP BY a.kd_skpd, MONTH ( tgl_sp2d ) UNION
                    $jnsRekon
            ) z where $periode_clause and kd_skpd = '$kd_skpd' GROUP BY kd_skpd, bulan ORDER BY kd_skpd, bulan"
        );

        $view = view('laporan_monitoring.rekon.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'jenis' => $jenis,
            'skpd' => $skpd,
            'thn_periode' => $thn_periode,
            'data' => $data
        ));

        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream("$urutan_skpd->urutan. $skpd->nm_skpd.pdf");
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
