<?php

namespace App\Http\Controllers\Perda\LampiranI2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranI2Controller extends Controller
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
        return view('perda.lampiran_I.lampiran_I_2.index')->with($data);
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

        $ttd_nm = DB::table('ms_ttd_perda')->where(['nama' => $ttd])->first();

        $jns_ang = collect(\DB::select("SELECT max(jns_ang) as jns_ang from trdrka"))->first();

        $data = DB::select(
            "SELECT
              group_id, group_name, SUM(a.nilai) anggaran, padding, is_bold, border, realisasi_2021,
              SUM(
                CASE
                  WHEN LEFT(r.kd_rek6, 1) = '4' OR LEFT(r.kd_rek6, 2) = '61' THEN kredit - debet
                  WHEN LEFT(r.kd_rek6, 1) = '5' OR LEFT(r.kd_rek6, 2) = '62' THEN debet - kredit
                  ELSE 0
                END
              ) realisasi
            FROM map_lampiran_perda_i_2 m
            LEFT JOIN trdrka a ON LEFT(a.kd_rek6, LEN(m.kd_rek)) = m.kd_rek and jns_ang = '$jns_ang->jns_ang'
            LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
            GROUP BY group_id, group_name, padding, is_bold, border, realisasi_2021
            ORDER BY group_id"
          );

        $view = view('perda.lampiran_I.lampiran_I_2.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
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
