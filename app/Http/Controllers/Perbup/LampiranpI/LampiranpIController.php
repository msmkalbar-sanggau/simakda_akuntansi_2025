<?php

namespace App\Http\Controllers\Perbup\LampiranpI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranpIController extends Controller
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
        return view('perbup.lampiran_I.index')->with($data);
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
            "SELECT * FROM (
                SELECT
                  kd_rek1 AS kode, nm_rek1 AS nama, SUM(a.nilai) AS nilai_ag,
                  SUM(
                    CASE
                      WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN r.kredit - r.debet
                      WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN r.debet - r.kredit
                    END
                  ) AS nilai_real
                FROM ms_rek1 m
                JOIN trdrka a ON m.kd_rek1 = LEFT(a.kd_rek6, 1)
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                where jns_ang = '$jns_ang->jns_ang'
                GROUP BY kd_rek1, nm_rek1

                UNION ALL

                SELECT
                  kd_rek2 AS kode, nm_rek2 AS nama, SUM(a.nilai) AS nilai_ag,
                  SUM(
                    CASE
                      WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN r.kredit - r.debet
                      WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN r.debet - r.kredit
                    END
                  ) AS nilai_real
                FROM ms_rek2 m
                JOIN trdrka a ON m.kd_rek2 = LEFT(a.kd_rek6, 2)
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                where jns_ang = '$jns_ang->jns_ang'
                GROUP BY kd_rek2, nm_rek2

                UNION ALL

                SELECT
                  kd_rek3 AS kode, nm_rek3 AS nama, SUM(a.nilai) AS nilai_ag,
                  SUM(
                    CASE
                      WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN r.kredit - r.debet
                      WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN r.debet - r.kredit
                    END
                  ) AS nilai_real
                FROM ms_rek3 m
                JOIN trdrka a ON m.kd_rek3 = LEFT(a.kd_rek6, 4)
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                where jns_ang = '$jns_ang->jns_ang'
                GROUP BY kd_rek3, nm_rek3
              ) lra
              ORDER BY kode"
        );

        $view = view('perbup.lampiran_I.print', array(
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
