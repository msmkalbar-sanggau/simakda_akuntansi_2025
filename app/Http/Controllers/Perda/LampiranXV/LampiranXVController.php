<?php

namespace App\Http\Controllers\Perda\LampiranXV;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranXVController extends Controller
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
        return view('perda.lampiran_XV.index')->with($data);
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
                    a.kd_skpd,
                    a.nm_skpd,
                    SUM ( debet_lalu ) AS debet_lalu,
                    SUM ( kredit_lalu ) AS kredit_lalu,
                    SUM ( debet ) AS debet,
                    SUM ( kredit ) AS kredit
                FROM ms_skpd a
                    LEFT JOIN
                        (
                            SELECT
                                a.kd_unit AS kd_skpd,
                                SUM ( a.debet ) AS debet_lalu,
                                SUM ( a.kredit ) AS kredit_lalu,
                                0 AS debet,
                                0 AS kredit
                            FROM
                                trdju_pkd_lalu a
                                JOIN trhju_pkd_lalu b ON a.no_voucher = b.no_voucher
                                AND a.kd_unit = b.kd_skpd
                            WHERE
                                LEFT ( a.kd_rek6, 2 ) = '15'
                                AND year(b.tgl_voucher) <= '$thn_lalu'
                            GROUP BY
                                a.kd_unit

                            UNION ALL

                            SELECT
                                a.kd_unit AS kd_skpd,
                                0 AS debet_lalu,
                                0 AS kredit_lalu,
                                SUM ( a.debet ) AS debet,
                                SUM ( a.kredit ) AS kredit
                            FROM
                                trdju_pkd a
                                JOIN trhju_pkd b ON a.no_voucher = b.no_voucher
                                AND a.kd_unit = b.kd_skpd
                            WHERE
                                LEFT ( a.kd_rek6, 2 ) = '15'
                                AND year(b.tgl_voucher) = '$thn'
                            GROUP BY
                                a.kd_unit
                        ) z on z.kd_skpd = a.kd_skpd
                GROUP BY a.nm_skpd, a.kd_skpd ORDER BY a.kd_skpd"
        );

        $view = view('perda.lampiran_XV.print', array(
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
