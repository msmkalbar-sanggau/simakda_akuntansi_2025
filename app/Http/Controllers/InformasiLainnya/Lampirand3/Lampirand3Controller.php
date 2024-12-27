<?php

namespace App\Http\Controllers\InformasiLainnya\Lampirand3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class Lampirand3Controller extends Controller
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
        return view('informasi_lainnya.lampiran_d_3.index')->with($data);
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
                m.kd_bidang_urusan, mbu.nm_bidang_urusan, m.nm_spm, m.kd_sub_kegiatan, msk.nm_sub_kegiatan, lra.anggaran, lra.realisasi
            FROM map_lampiran_c_2 m
            JOIN ms_bidang_urusan mbu ON m.kd_bidang_urusan = mbu.kd_bidang_urusan
            JOIN ms_sub_kegiatan msk ON m.kd_sub_kegiatan = msk.kd_sub_kegiatan
            LEFT JOIN (
                SELECT
                a.kd_sub_kegiatan, SUM(a.nilai) anggaran, SUM(r.debet) - SUM(r.kredit) realisasi
                FROM trdrka a
                LEFT JOIN jurnal_rekap r ON a.kd_skpd = r.kd_skpd AND a.kd_sub_kegiatan = r.kd_sub_kegiatan AND a.kd_rek6 = r.kd_rek6
                WHERE LEFT(a.kd_rek6, 1) = '5' and jns_ang = '$jns_ang->jns_ang'
                GROUP BY a.kd_sub_kegiatan
            ) lra ON m.kd_sub_kegiatan = lra.kd_sub_kegiatan
            ORDER BY m.kd_bidang_urusan, m.nm_spm, m.kd_sub_kegiatan"
        );

        $mapped_lamp = [];

        foreach ($data as $value) {
            $kd_bidang_urusan = $value->kd_bidang_urusan;
            $nm_spm = $value->nm_spm;
            if (!isset($mapped_lamp[$kd_bidang_urusan])) $mapped_lamp[$kd_bidang_urusan] = [];
            $mapped_lamp[$kd_bidang_urusan]['nm_bidang_urusan'] = $value->nm_bidang_urusan;

            if (!isset($mapped_lamp[$kd_bidang_urusan]['spm'])) $mapped_lamp[$kd_bidang_urusan]['spm'] = [];
            if (!isset($mapped_lamp[$kd_bidang_urusan]['spm'][$nm_spm])) $mapped_lamp[$kd_bidang_urusan]['spm'][$nm_spm] = [];

            $mapped_lamp[$kd_bidang_urusan]['spm'][$nm_spm][] = [
                'kd_sub_kegiatan' => $value->kd_sub_kegiatan,
                'nm_sub_kegiatan' => $value->nm_sub_kegiatan,
                'anggaran' => $value->anggaran,
                'realisasi' => $value->realisasi,
            ];
        }

        $view = view('informasi_lainnya.lampiran_d_3.print', array(
            'jenis' => $jenis,
            'mapped_lamp' => $mapped_lamp,
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
