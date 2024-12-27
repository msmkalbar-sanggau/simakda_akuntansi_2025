<?php

namespace App\Http\Controllers\Perbup\LampiranpI1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranpI1Controller extends Controller
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
            'skpd' => DB::table('ms_skpd')->get(),
        ];
        return view('perbup.lampiran_I_1.index')->with($data);
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
        $tgl_ttd = $request->tgl_ttd;
        $ttd = $request->ttd;
        $jenis = $request->jenis;
        $ttd_nm = DB::table('ms_ttd_perda')->where(['nama' => $ttd])->first();

        $jns_ang = collect(\DB::select("SELECT max(jns_ang) as jns_ang from trdrka"))->first();
        $skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();
        $urutan_skpd = collect(\DB::select("SELECT urutan FROM (SELECT ROW_NUMBER() OVER (ORDER BY kd_skpd ASC) AS urutan, kd_skpd FROM ms_skpd) z where kd_skpd = '$kd_skpd'"))->first();
        $bidang_urusan = DB::table('ms_bidang_urusan')->where(['kd_bidang_urusan' => $skpd->kd_urusan])->first();
        $urusan = DB::table('ms_urusan')->where(['kd_urusan' => $bidang_urusan->kd_urusan])->first();

        $data = DB::select(
            "SELECT * FROM (
              SELECT
                mr.kd_rek1 AS urutan, CONCAT(
                  LEFT(a.kd_skpd, 1), SUBSTRING(a.kd_skpd, 3, 2), a.kd_skpd, '000.0000', mr.kd_rek1
                ) AS kode, mr.nm_rek1 AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              JOIN ms_rek1 mr ON LEFT(a.kd_rek6, 1) = mr.kd_rek1
              WHERE a.kd_skpd = '$skpd->kd_skpd' and jns_ang = '$jns_ang->jns_ang'
              GROUP BY mr.kd_rek1, a.kd_skpd, mr.nm_rek1

              UNION ALL

              SELECT
                CONCAT(LEFT(a.kd_rek6, 1), mp.kd_program) AS urutan, CONCAT(
                  LEFT(mp.kd_program, 1), SUBSTRING(mp.kd_program, 3, 2), a.kd_skpd, RIGHT(mp.kd_program, 2)
                ) AS kode, mp.nm_program AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              JOIN ms_program mp ON LEFT(a.kd_sub_kegiatan, 7) = mp.kd_program
              WHERE a.kd_skpd = '$skpd->kd_skpd' AND LEFT(a.kd_rek6, 1) IN ('5') and jns_ang = '$jns_ang->jns_ang'
              GROUP BY LEFT(a.kd_rek6, 1), a.kd_skpd, mp.kd_program, mp.nm_program

              UNION ALL

              SELECT
                CONCAT(LEFT(a.kd_rek6, 1), mk.kd_kegiatan) AS urutan, CONCAT(
                  LEFT(mk.kd_kegiatan, 1), SUBSTRING(mk.kd_kegiatan, 3, 2), a.kd_skpd, SUBSTRING(mk.kd_kegiatan, 6, 2), RIGHT(mk.kd_kegiatan, 4)
                ) AS kode, mk.nm_kegiatan AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              JOIN ms_kegiatan mk ON LEFT(a.kd_sub_kegiatan, 12) = mk.kd_kegiatan
              WHERE a.kd_skpd = '$skpd->kd_skpd' AND LEFT(a.kd_rek6, 1) IN ('5') and jns_ang = '$jns_ang->jns_ang'
              GROUP BY LEFT(a.kd_rek6, 1), a.kd_skpd, mk.kd_kegiatan, mk.nm_kegiatan

              UNION ALL

              SELECT
                CONCAT(LEFT(a.kd_rek6, 1), a.kd_sub_kegiatan) AS urutan, CONCAT(
                  LEFT(a.kd_sub_kegiatan, 1), SUBSTRING(a.kd_sub_kegiatan, 3, 2), a.kd_skpd, SUBSTRING(a.kd_sub_kegiatan, 6, 2), SUBSTRING(a.kd_sub_kegiatan, 9, 4),
                  RIGHT(a.kd_sub_kegiatan, 2)
                ) AS kode, a.nm_sub_kegiatan AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              WHERE a.kd_skpd = '$skpd->kd_skpd' AND LEFT(a.kd_rek6, 1) IN ('5') and jns_ang = '$jns_ang->jns_ang'
              GROUP BY LEFT(a.kd_rek6, 1), a.kd_skpd, a.kd_sub_kegiatan, a.nm_sub_kegiatan

              UNION ALL

              SELECT
                CONCAT(LEFT(a.kd_rek6, 1), a.kd_sub_kegiatan, mr.kd_rek2) AS urutan, CONCAT(
                  LEFT(a.kd_sub_kegiatan, 1), SUBSTRING(a.kd_sub_kegiatan, 3, 2), a.kd_skpd, SUBSTRING(a.kd_sub_kegiatan, 6, 2), SUBSTRING(a.kd_sub_kegiatan, 9, 4),
                  RIGHT(a.kd_sub_kegiatan, 2), mr.kd_rek2
                ) AS kode, mr.nm_rek2 AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              JOIN ms_rek2 mr ON LEFT(a.kd_rek6, 2) = mr.kd_rek2
              WHERE a.kd_skpd = '$skpd->kd_skpd' and jns_ang = '$jns_ang->jns_ang'
              GROUP BY LEFT(a.kd_rek6, 1), a.kd_skpd, a.kd_sub_kegiatan, mr.kd_rek2, mr.nm_rek2

              UNION ALL

              SELECT
                CONCAT(LEFT(a.kd_rek6, 1), a.kd_sub_kegiatan, mr.kd_rek3) AS urutan, CONCAT(
                  LEFT(a.kd_sub_kegiatan, 1), SUBSTRING(a.kd_sub_kegiatan, 3, 2), a.kd_skpd, SUBSTRING(a.kd_sub_kegiatan, 6, 2), SUBSTRING(a.kd_sub_kegiatan, 9, 4),
                  RIGHT(a.kd_sub_kegiatan, 2), mr.kd_rek3
                ) AS kode, mr.nm_rek3 AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              JOIN ms_rek3 mr ON LEFT(a.kd_rek6, 4) = mr.kd_rek3
              WHERE a.kd_skpd = '$skpd->kd_skpd' and jns_ang = '$jns_ang->jns_ang'
              GROUP BY LEFT(a.kd_rek6, 1), a.kd_skpd, a.kd_sub_kegiatan, mr.kd_rek3, mr.nm_rek3

              UNION ALL

              SELECT
                CONCAT(LEFT(a.kd_rek6, 1), a.kd_sub_kegiatan, mr.kd_rek4) AS urutan, CONCAT(
                  LEFT(a.kd_sub_kegiatan, 1), SUBSTRING(a.kd_sub_kegiatan, 3, 2), a.kd_skpd, SUBSTRING(a.kd_sub_kegiatan, 6, 2), SUBSTRING(a.kd_sub_kegiatan, 9, 4),
                  RIGHT(a.kd_sub_kegiatan, 2), mr.kd_rek4
                ) AS kode, mr.nm_rek4 AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              JOIN ms_rek4 mr ON LEFT(a.kd_rek6, 6) = mr.kd_rek4
              WHERE a.kd_skpd = '$skpd->kd_skpd' and jns_ang = '$jns_ang->jns_ang'
              GROUP BY LEFT(a.kd_rek6, 1), a.kd_skpd, a.kd_sub_kegiatan, mr.kd_rek4, mr.nm_rek4

              UNION ALL

              SELECT
                CONCAT(LEFT(a.kd_rek6, 1), a.kd_sub_kegiatan, mr.kd_rek5) AS urutan, CONCAT(
                  LEFT(a.kd_sub_kegiatan, 1), SUBSTRING(a.kd_sub_kegiatan, 3, 2), a.kd_skpd, SUBSTRING(a.kd_sub_kegiatan, 6, 2), SUBSTRING(a.kd_sub_kegiatan, 9, 4),
                  RIGHT(a.kd_sub_kegiatan, 2), mr.kd_rek5
                ) AS kode, mr.nm_rek5 AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              JOIN ms_rek5 mr ON LEFT(a.kd_rek6, 8) = mr.kd_rek5
              WHERE a.kd_skpd = '$skpd->kd_skpd' and jns_ang = '$jns_ang->jns_ang'
              GROUP BY LEFT(a.kd_rek6, 1), a.kd_skpd, a.kd_sub_kegiatan, mr.kd_rek5, mr.nm_rek5

              UNION ALL

              SELECT
                CONCAT(LEFT(a.kd_rek6, 1), a.kd_sub_kegiatan, a.kd_rek6) AS urutan, CONCAT(
                  LEFT(a.kd_sub_kegiatan, 1), SUBSTRING(a.kd_sub_kegiatan, 3, 2), a.kd_skpd, SUBSTRING(a.kd_sub_kegiatan, 6, 2), SUBSTRING(a.kd_sub_kegiatan, 9, 4),
                  RIGHT(a.kd_sub_kegiatan, 2), a.kd_rek6
                ) AS kode, a.nm_rek6 AS nama, SUM(a.nilai) AS nilai_ag,
                SUM(
                  CASE
                    WHEN LEFT(a.kd_rek6, 1) = '4' OR LEFT(a.kd_rek6, 2) = '61' THEN kredit - debet
                    WHEN LEFT(a.kd_rek6, 1) = '5' OR LEFT(a.kd_rek6, 2) = '62' THEN debet - kredit
                  END
                ) AS nilai_real
              FROM trdrka a
              LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
              WHERE a.kd_skpd = '$skpd->kd_skpd' and jns_ang = '$jns_ang->jns_ang'
              GROUP BY LEFT(a.kd_rek6, 1), a.kd_skpd, a.kd_sub_kegiatan, a.kd_rek6, a.nm_rek6
            ) lra
            ORDER BY urutan",
        );

        $view = view('perbup.lampiran_I_1.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'skpd' => $skpd,
            'urusan' => $urusan,
            'ttd' => $ttd,
            'tgl_ttd' => $tgl_ttd,
            'ttd_nm' => $ttd_nm,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)
              ->setOption('footer-right', "Halaman [page] dari [topage]")
              ->setOption('footer-font-size', 9)
              ->setOrientation('landscape')->setPaper('a4');
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
