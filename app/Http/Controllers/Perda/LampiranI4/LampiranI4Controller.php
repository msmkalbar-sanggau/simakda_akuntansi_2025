<?php

namespace App\Http\Controllers\Perda\LampiranI4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranI4Controller extends Controller
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
            'jenis_anggaran' => DB::table('trhrka as a')->select('a.jns_ang', 'a.tgl_dpa', 'b.nama')
                ->join('tb_status_anggaran as b', function ($join) {
                    $join->on('a.jns_ang', '=', 'b.kode');
                })->distinct()->get(),
        ];
        return view('perda.lampiran_I.lampiran_I_4.index')->with($data);
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
        $bulan = $request->bulan;
        $jns_ang = $request->jns_ang;

        $ttd_nm = DB::table('ms_ttd_perda')->where(['nama' => $ttd])->first();

        // $jns_ang = collect(\DB::select("SELECT max(jns_ang) as jns_ang from trdrka"))->first();

        $daftar_periode = ["", "JANUARI", "FEBRUARI", "TRIWULAN I", "APRIL", "MEI", "SEMESTER PERTAMA", "JULI", "AGUSTUS", "TRIWULAN III", "OKTOBER", "NOVEMBER", "SEMESTER KEDUA"];
        $periode = $daftar_periode[$bulan];

        $data = DB::select(
            "SELECT * FROM (
                SELECT
                  mu.kd_urusan AS urutan, mu.kd_urusan AS kode, mu.nm_urusan AS nama,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN a.nilai ELSE 0 END) AS ag_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN a.nilai ELSE 0 END) AS ag_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN a.nilai ELSE 0 END) AS ag_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN a.nilai ELSE 0 END) AS ag_transfer,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN r.debet - r.kredit ELSE 0 END) AS r_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN r.debet - r.kredit ELSE 0 END) AS r_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN r.debet - r.kredit ELSE 0 END) AS r_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN r.debet - r.kredit ELSE 0 END) AS r_transfer
                FROM trdrka a
                LEFT JOIN (
        SELECT
            kd_skpd,
            kd_sub_kegiatan,
            kd_rek6,
            SUM(debet) AS debet,
            SUM(kredit) AS kredit
        FROM jurnal_rekap2
        WHERE MONTH(tgl_voucher) <= $bulan
        GROUP BY kd_skpd, kd_sub_kegiatan, kd_rek6
    ) r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_urusan mu ON LEFT(a.kd_sub_kegiatan, 1) = mu.kd_urusan
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang = '$jns_ang'
                GROUP BY mu.kd_urusan, mu.nm_urusan
                UNION ALL

                SELECT
                  mbu.kd_bidang_urusan AS urutan, CONCAT(mbu.kd_urusan, RIGHT(mbu.kd_bidang_urusan, 2)) AS kode, mbu.nm_bidang_urusan AS nama,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN a.nilai ELSE 0 END) AS ag_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN a.nilai ELSE 0 END) AS ag_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN a.nilai ELSE 0 END) AS ag_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN a.nilai ELSE 0 END) AS ag_transfer,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN r.debet - r.kredit ELSE 0 END) AS r_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN r.debet - r.kredit ELSE 0 END) AS r_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN r.debet - r.kredit ELSE 0 END) AS r_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN r.debet - r.kredit ELSE 0 END) AS r_transfer
                FROM trdrka a
                LEFT JOIN (
        SELECT
            kd_skpd,
            kd_sub_kegiatan,
            kd_rek6,
            SUM(debet) AS debet,
            SUM(kredit) AS kredit
        FROM jurnal_rekap2
        WHERE MONTH(tgl_voucher) <= $bulan
        GROUP BY kd_skpd, kd_sub_kegiatan, kd_rek6
    ) r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_bidang_urusan mbu ON LEFT(a.kd_sub_kegiatan, 4) = mbu.kd_bidang_urusan
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang = '$jns_ang'
                GROUP BY mbu.kd_urusan, mbu.kd_bidang_urusan, mbu.nm_bidang_urusan
                UNION ALL

                SELECT
                  CONCAT(mbu.kd_bidang_urusan, a.kd_skpd) AS urutan, CONCAT(mbu.kd_urusan, RIGHT(mbu.kd_bidang_urusan, 2), a.kd_skpd) AS kode, a.nm_skpd AS nama,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN a.nilai ELSE 0 END) AS ag_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN a.nilai ELSE 0 END) AS ag_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN a.nilai ELSE 0 END) AS ag_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN a.nilai ELSE 0 END) AS ag_transfer,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN r.debet - r.kredit ELSE 0 END) AS r_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN r.debet - r.kredit ELSE 0 END) AS r_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN r.debet - r.kredit ELSE 0 END) AS r_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN r.debet - r.kredit ELSE 0 END) AS r_transfer
                FROM trdrka a
                LEFT JOIN (
        SELECT
            kd_skpd,
            kd_sub_kegiatan,
            kd_rek6,
            SUM(debet) AS debet,
            SUM(kredit) AS kredit
        FROM jurnal_rekap2
        WHERE MONTH(tgl_voucher) <= $bulan
        GROUP BY kd_skpd, kd_sub_kegiatan, kd_rek6
    ) r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_bidang_urusan mbu ON LEFT(a.kd_sub_kegiatan, 4) = mbu.kd_bidang_urusan
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang = '$jns_ang'
                GROUP BY mbu.kd_urusan, mbu.kd_bidang_urusan, mbu.nm_bidang_urusan, a.kd_skpd, a.nm_skpd

                UNION ALL

                SELECT
                  CONCAT(mbu.kd_bidang_urusan, a.kd_skpd, mp.kd_program) AS urutan, CONCAT(mbu.kd_urusan, RIGHT(mbu.kd_bidang_urusan, 2), a.kd_skpd, RIGHT(mp.kd_program, 2)) AS kode, mp.nm_program AS nama,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN a.nilai ELSE 0 END) AS ag_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN a.nilai ELSE 0 END) AS ag_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN a.nilai ELSE 0 END) AS ag_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN a.nilai ELSE 0 END) AS ag_transfer,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN r.debet - r.kredit ELSE 0 END) AS r_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN r.debet - r.kredit ELSE 0 END) AS r_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN r.debet - r.kredit ELSE 0 END) AS r_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN r.debet - r.kredit ELSE 0 END) AS r_transfer
                FROM trdrka a
                LEFT JOIN (
        SELECT
            kd_skpd,
            kd_sub_kegiatan,
            kd_rek6,
            SUM(debet) AS debet,
            SUM(kredit) AS kredit
        FROM jurnal_rekap2
        WHERE MONTH(tgl_voucher) <= $bulan
        GROUP BY kd_skpd, kd_sub_kegiatan, kd_rek6
    ) r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_bidang_urusan mbu ON LEFT(a.kd_sub_kegiatan, 4) = mbu.kd_bidang_urusan
                JOIN ms_program mp ON LEFT(a.kd_sub_kegiatan, 7) = mp.kd_program
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang = '$jns_ang'
                GROUP BY mbu.kd_urusan, mbu.kd_bidang_urusan, mbu.nm_bidang_urusan, a.kd_skpd, mp.kd_program, mp.nm_program
                UNION ALL

                SELECT
                  CONCAT(mbu.kd_bidang_urusan, a.kd_skpd, mk.kd_kegiatan) AS urutan, CONCAT(
                    mbu.kd_urusan, RIGHT(mbu.kd_bidang_urusan, 2), a.kd_skpd, SUBSTRING(mk.kd_kegiatan, 6, 2), RIGHT(mk.kd_kegiatan, 4)
                  ) AS kode, mk.nm_kegiatan AS nama,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN a.nilai ELSE 0 END) AS ag_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN a.nilai ELSE 0 END) AS ag_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN a.nilai ELSE 0 END) AS ag_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN a.nilai ELSE 0 END) AS ag_transfer,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN r.debet - r.kredit ELSE 0 END) AS r_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN r.debet - r.kredit ELSE 0 END) AS r_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN r.debet - r.kredit ELSE 0 END) AS r_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN r.debet - r.kredit ELSE 0 END) AS r_transfer
                FROM trdrka a
                LEFT JOIN (
        SELECT
            kd_skpd,
            kd_sub_kegiatan,
            kd_rek6,
            SUM(debet) AS debet,
            SUM(kredit) AS kredit
        FROM jurnal_rekap2
        WHERE MONTH(tgl_voucher) <= $bulan
        GROUP BY kd_skpd, kd_sub_kegiatan, kd_rek6
    ) r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_bidang_urusan mbu ON LEFT(a.kd_sub_kegiatan, 4) = mbu.kd_bidang_urusan
                JOIN ms_kegiatan mk ON LEFT(a.kd_sub_kegiatan, 12) = mk.kd_kegiatan
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang = '$jns_ang'
                GROUP BY mbu.kd_urusan, mbu.kd_bidang_urusan, mbu.nm_bidang_urusan, a.kd_skpd, mk.kd_kegiatan, mk.nm_kegiatan
                UNION ALL

                SELECT
                  CONCAT(mbu.kd_bidang_urusan, a.kd_skpd, a.kd_sub_kegiatan) AS urutan, CONCAT(
                    mbu.kd_urusan, RIGHT(mbu.kd_bidang_urusan, 2), a.kd_skpd, SUBSTRING(a.kd_sub_kegiatan, 6, 2), SUBSTRING(a.kd_sub_kegiatan, 9, 4), RIGHT(a.kd_sub_kegiatan, 2)
                  ) AS kode, a.nm_sub_kegiatan AS nama,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN a.nilai ELSE 0 END) AS ag_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN a.nilai ELSE 0 END) AS ag_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN a.nilai ELSE 0 END) AS ag_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN a.nilai ELSE 0 END) AS ag_transfer,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '51' THEN r.debet - r.kredit ELSE 0 END) AS r_operasi,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '52' THEN r.debet - r.kredit ELSE 0 END) AS r_modal,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '53' THEN r.debet - r.kredit ELSE 0 END) AS r_btt,
                  SUM(CASE WHEN LEFT(a.kd_rek6, 2) = '54' THEN r.debet - r.kredit ELSE 0 END) AS r_transfer
                FROM trdrka a
                LEFT JOIN (
        SELECT
            kd_skpd,
            kd_sub_kegiatan,
            kd_rek6,
            SUM(debet) AS debet,
            SUM(kredit) AS kredit
        FROM jurnal_rekap2
        WHERE MONTH(tgl_voucher) <= $bulan
        GROUP BY kd_skpd, kd_sub_kegiatan, kd_rek6
    ) r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                JOIN ms_bidang_urusan mbu ON LEFT(a.kd_sub_kegiatan, 4) = mbu.kd_bidang_urusan
                WHERE LEFT(a.kd_rek6, 1) = '5' and a.jns_ang = '$jns_ang'
                GROUP BY mbu.kd_urusan, mbu.kd_bidang_urusan, mbu.nm_bidang_urusan, a.kd_skpd, a.kd_sub_kegiatan, a.nm_sub_kegiatan
              ) lampiran
              ORDER BY urutan"
        );
        // dd ($data);

        $view = view('perda.lampiran_I.lampiran_I_4.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'ttd' => $ttd,
            'tgl_ttd' => $tgl_ttd,
            'ttd_nm' => $ttd_nm,
            'periode' => $periode,
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
