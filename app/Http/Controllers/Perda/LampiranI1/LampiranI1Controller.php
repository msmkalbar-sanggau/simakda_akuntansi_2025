<?php

namespace App\Http\Controllers\Perda\LampiranI1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranI1Controller extends Controller
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
        return view('perda.lampiran_I.lampiran_I_1.index')->with($data);
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

        $pendapatan = collect(\DB::select(
            "SELECT
                SUM(a.nilai) AS nilai_ag,
                SUM(r.kredit - r.debet) AS nilai_real
            FROM trdrka a LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
            WHERE LEFT(a.kd_rek6, 1) = '4' AND jns_ang = '$jns_ang->jns_ang'"
        ))->first();

        $belanja =  collect(\DB::select(
            "SELECT
                SUM(a.nilai) AS nilai_ag,
                SUM(r.debet - r.kredit) AS nilai_real
            FROM trdrka a LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
            WHERE LEFT(a.kd_rek6, 1) = '5' AND jns_ang = '$jns_ang->jns_ang'"
        ))->first();

        $data = DB::select(
            "SELECT * FROM (
                SELECT
                    CONCAT(kd_rek1, kd_urusan) AS urutan, kd_urusan AS kode, nm_urusan AS nama,
                    0 AS nilai_ag, 0 AS nilai_real, 0 is_bold, 'urusan' AS jenis
                FROM ms_urusan mu
                JOIN trdrka a ON mu.kd_urusan = LEFT(a.kd_sub_kegiatan, 1)
                JOIN ms_rek1 mrek1 ON mrek1.kd_rek1 = LEFT(a.kd_rek6, 1)
                WHERE LEFT(a.kd_rek6, 1) IN ('4', '5') and a.jns_ang = '$jns_ang->jns_ang' and a.nilai <> '0'
                GROUP BY mrek1.kd_rek1, mu.kd_urusan, mu.nm_urusan
                UNION ALL

                SELECT
                    CONCAT(LEFT(a.kd_rek6, 1), kd_bidang_urusan) AS urutan, kd_bidang_urusan AS kode, nm_bidang_urusan AS nama,
                    SUM(a.nilai), SUM(CASE LEFT(r.kd_rek6, 1) WHEN '4' THEN r.kredit - r.debet WHEN '5' THEN r.debet - r.kredit END),
                    1 is_bold, 'bidang_urusan' AS jenis
                FROM ms_urusan mu
                JOIN ms_bidang_urusan mbu ON mu.kd_urusan = mbu.kd_urusan
                JOIN trdrka a ON mbu.kd_bidang_urusan = LEFT(a.kd_sub_kegiatan, 4)
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                WHERE LEFT(a.kd_rek6, 1) IN ('4', '5') and a.jns_ang = '$jns_ang->jns_ang' and a.nilai <> '0'
                GROUP BY kd_bidang_urusan, nm_bidang_urusan, LEFT(a.kd_rek6, 1)
                UNION ALL

                SELECT
                    CONCAT(LEFT(a.kd_rek6, 1), kd_bidang_urusan, a.kd_skpd) AS urutan, a.kd_skpd AS kode, a.nm_skpd AS nama,
                    SUM(a.nilai), SUM(CASE LEFT(r.kd_rek6, 1) WHEN '4' THEN r.kredit - r.debet WHEN '5' THEN r.debet - r.kredit END),
                    0 is_bold, 'skpd' AS jenis
                FROM ms_urusan mu
                JOIN ms_bidang_urusan mbu ON mu.kd_urusan = mbu.kd_urusan
                JOIN trdrka a ON mbu.kd_bidang_urusan = LEFT(a.kd_sub_kegiatan, 4)
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                WHERE LEFT(a.kd_rek6, 1) IN ('4', '5') and a.jns_ang = '$jns_ang->jns_ang' and a.nilai <> '0'
                GROUP BY kd_bidang_urusan, nm_bidang_urusan, LEFT(a.kd_rek6, 1), a.kd_skpd, a.nm_skpd
                UNION ALL

                SELECT
                    CONCAT(LEFT(a.kd_rek6, 1), kd_bidang_urusan, a.kd_skpd, mrek2.kd_rek2) AS urutan,
                    mrek2.kd_rek2 AS kode, mrek2.nm_rek2 AS nama,
                    SUM(a.nilai), SUM(CASE LEFT(r.kd_rek6, 1) WHEN '4' THEN r.kredit - r.debet WHEN '5' THEN r.debet - r.kredit END),
                    1 is_bold, 'rek2' AS jenis
                FROM ms_urusan mu
                JOIN ms_bidang_urusan mbu ON mu.kd_urusan = mbu.kd_urusan
                JOIN trdrka a ON mbu.kd_bidang_urusan = LEFT(a.kd_sub_kegiatan, 4)
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_rek2 mrek2 ON LEFT(a.kd_rek6, 2) = mrek2.kd_rek2
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang = '$jns_ang->jns_ang' and a.nilai <> '0'
                GROUP BY kd_bidang_urusan, nm_bidang_urusan, LEFT(a.kd_rek6, 1), a.kd_skpd, a.nm_skpd, mrek2.kd_rek2, mrek2.nm_rek2
                UNION ALL

                SELECT
                    CONCAT(LEFT(a.kd_rek6, 1), kd_bidang_urusan, a.kd_skpd, mrek3.kd_rek3) AS urutan,
                    mrek3.kd_rek3 AS kode, mrek3.nm_rek3 AS nama,
                    SUM(a.nilai), SUM(CASE LEFT(r.kd_rek6, 1) WHEN '4' THEN r.kredit - r.debet WHEN '5' THEN r.debet - r.kredit END),
                    0 is_bold, 'rek3' AS jenis
                FROM ms_urusan mu
                JOIN ms_bidang_urusan mbu ON mu.kd_urusan = mbu.kd_urusan
                JOIN trdrka a ON mbu.kd_bidang_urusan = LEFT(a.kd_sub_kegiatan, 4)
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_rek3 mrek3 ON LEFT(a.kd_rek6, 4) = mrek3.kd_rek3
                WHERE LEFT(a.kd_rek6, 2) = '51' and a.jns_ang = '$jns_ang->jns_ang' and a.nilai <> '0'
                GROUP BY kd_bidang_urusan, nm_bidang_urusan, LEFT(a.kd_rek6, 1), a.kd_skpd, a.nm_skpd, mrek3.kd_rek3, mrek3.nm_rek3
            ) x
            ORDER BY urutan"
        );

        $view = view('perda.lampiran_I.lampiran_I_1.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'pendapatan' => $pendapatan,
            'belanja' => $belanja,
            'ttd' => $ttd_nm,
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
