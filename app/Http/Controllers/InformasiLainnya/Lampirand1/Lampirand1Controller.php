<?php

namespace App\Http\Controllers\InformasiLainnya\Lampirand1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class Lampirand1Controller extends Controller
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
        return view('informasi_lainnya.lampiran_d_1.index')->with($data);
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
                  mf.kd_fungsi AS urutan, mf.kd_fungsi AS kode, '' AS kode2, mf.nm_fungsi AS nama,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN a.nilai ELSE 0 END) AS ag_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN a.nilai ELSE 0 END) AS ag_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN a.nilai ELSE 0 END) AS ag_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN a.nilai ELSE 0 END) AS ag_transfer,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN r.debet - r.kredit ELSE 0 END) AS r_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN r.debet - r.kredit ELSE 0 END) AS r_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN r.debet - r.kredit ELSE 0 END) AS r_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN r.debet - r.kredit ELSE 0 END) AS r_transfer
                FROM trdrka a
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_sub_fungsi msf ON LEFT(a.kd_sub_kegiatan, 4) = msf.kd_urusan
                JOIN ms_fungsi mf ON LEFT(msf.kd_fungsi, 1) = mf.kd_fungsi
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang='$jns_ang->jns_ang'
                GROUP BY mf.kd_fungsi, mf.nm_fungsi
        
                UNION ALL
        
                SELECT
                  msf.kd_fungsi AS urutan, msf.kd_fungsi AS kode, msf.kd_urusan AS kode2, msf.nm_fungsi AS nama,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN a.nilai ELSE 0 END) AS ag_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN a.nilai ELSE 0 END) AS ag_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN a.nilai ELSE 0 END) AS ag_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN a.nilai ELSE 0 END) AS ag_transfer,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN r.debet - r.kredit ELSE 0 END) AS r_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN r.debet - r.kredit ELSE 0 END) AS r_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN r.debet - r.kredit ELSE 0 END) AS r_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN r.debet - r.kredit ELSE 0 END) AS r_transfer
                FROM trdrka a
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_sub_fungsi msf ON LEFT(a.kd_sub_kegiatan, 4) = msf.kd_urusan
                JOIN ms_fungsi mf ON LEFT(msf.kd_fungsi, 1) = mf.kd_fungsi
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang='$jns_ang->jns_ang'
                GROUP BY msf.kd_fungsi, msf.nm_fungsi, msf.kd_urusan
              ) lra
              ORDER BY urutan"
        );

        $view = view('informasi_lainnya.lampiran_d_1.print', array(
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
