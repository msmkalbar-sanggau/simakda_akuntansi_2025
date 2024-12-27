<?php

namespace App\Http\Controllers\Perda\LampiranVIII;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranVIIIController extends Controller
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
        return view('perda.lampiran_VIII.index')->with($data);
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
            "SELECT
                    kd_skpd,
                    nm_skpd,
                    kd_rek,
                    nm_rek,
                    SUM ( debet_lalu ) AS debet_lalu,
                    SUM ( kredit_lalu ) AS kredit_lalu,
                    SUM ( debet ) AS debet,
                    SUM ( kredit ) AS kredit
                FROM (
                    SELECT LEFT
                        ( a.kd_rek6, 4 ) AS kd_rek,
                        (SELECT nm_rek3 FROM ms_rek3 where kd_rek3 = left(a.kd_rek6, 4)) as nm_rek,
                        a.kd_unit as kd_skpd,
                        (SELECT nm_skpd FROM ms_skpd where kd_skpd = a.kd_unit) as nm_skpd,
                        SUM ( a.debet ) AS debet_lalu,
                        SUM ( a.kredit ) AS kredit_lalu,
                        0 AS debet,
                        0 AS kredit
                    FROM
                        trdju_pkd_lalu a
                        JOIN trhju_pkd_lalu b ON a.no_voucher = b.no_voucher
                        AND a.kd_unit = b.kd_skpd
                    WHERE
                        LEFT ( a.kd_rek6, 4 ) in ('1103', '1104', '1108', '1109')
                        AND year(b.tgl_voucher) <= '$thn_lalu'
                    GROUP BY
                        LEFT ( a.kd_rek6, 4 ), a.kd_unit UNION ALL
                    SELECT LEFT
                        ( a.kd_rek6, 4 ) AS kd_rek,
                        (SELECT nm_rek3 FROM ms_rek3 where kd_rek3 = left(a.kd_rek6, 4)) as nm_rek,
                            a.kd_unit as kd_skpd,
                        (SELECT nm_skpd FROM ms_skpd where kd_skpd = a.kd_unit) as nm_skpd,
                        0 AS debet_lalu,
                        0 AS kredit_lalu,
                        SUM ( a.debet ) AS debet,
                        SUM ( a.kredit ) AS kredit
                    FROM
                        trdju_pkd a
                        JOIN trhju_pkd b ON a.no_voucher = b.no_voucher
                        AND a.kd_unit = b.kd_skpd
                    WHERE
                        LEFT ( a.kd_rek6, 4 ) in ('1103', '1104', '1108', '1109')
                        AND year(b.tgl_voucher) = '$thn'
                    GROUP BY
                        LEFT ( a.kd_rek6, 4 ), a.kd_unit
                    ) z
                GROUP BY
                    z.nm_skpd,
                    z.kd_skpd,
                    z.kd_rek,
                    z.nm_rek
                ORDER BY
                    z.kd_skpd, z.kd_rek"
        );

        $view = view('perda.lampiran_VIII.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
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
