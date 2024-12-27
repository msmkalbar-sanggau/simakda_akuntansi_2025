<?php

namespace App\Http\Controllers\LaporanPemda\LRAKESELARASAN;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class LRAKESELARASANController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'jenis_anggaran' => DB::table('trhrka as a')->select('a.jns_ang', 'a.tgl_dpa', 'b.nama')
                ->join('tb_status_anggaran as b', function ($join) {
                    $join->on('a.jns_ang', '=', 'b.kode');
                })->distinct()->get(),
        ];
        return view('laporan_pemda.lra_keselarasan.index')->with($data);
    }

    public function penandatangan(Request $request)
    {
        $request = $request->jns;
        $data = DB::table('ms_ttd')
            ->select('nip', 'nama')
            ->where(['kode' => 'BUP'])
            ->get();

        return response()->json($data);
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
        setlocale(LC_ALL, 'Indonesian');
        $penandatangan = $request->penandatangan;
        $tgl_ttd = $request->tgl_ttd;
        $bulan = $request->bulan;
        $jns_ang = $request->jns_ang;
        $current_year = tahun_anggaran();
        $jenis = $request->jenis;

        $ttd = DB::table('ms_ttd')->where(['nip' => $penandatangan])->first();

        $data = DB::select(
            "SELECT
                a.kode,
                a.nama,
                ISNULL(a.anggaran, 0) AS anggaran,
                ISNULL(b.realisasi, 0) AS realisasi,
                ISNULL(realisasi - anggaran, 0) AS selisih
            FROM
                (
                    SELECT
                        RTRIM(a.kd_fungsi) + '.' + a.kd_urusan AS kode,
                        a.nm_fungsi AS nama,
                        SUM (ISNULL(anggaran, 0)) AS anggaran
                    FROM
                        ms_sub_fungsi a
                    LEFT JOIN
                        (
                            SELECT
                                LEFT (kd_sub_kegiatan, 4) AS kd_urusan,
                                SUM (nilai) AS anggaran
                            FROM
                                trdrka a
                            WHERE
                                LEFT (a.kd_rek6, 1) IN ('5') AND a.jns_ang = ?
                            GROUP BY LEFT (kd_sub_kegiatan, 4)
                        ) b ON a.kd_urusan = b.kd_urusan
                    GROUP BY a.kd_fungsi, a.kd_urusan, a.nm_fungsi
                ) a LEFT JOIN
                (
                    SELECT
                        RTRIM(a.kd_fungsi) + '.' + a.kd_urusan AS kode,
                        a.nm_fungsi AS nama,
                        SUM (ISNULL(realisasi, 0)) AS realisasi
                    FROM
                        ms_sub_fungsi a
                    LEFT JOIN
                        (
                            SELECT
                                LEFT (kd_sub_kegiatan, 4) AS kd_urusan,
                                SUM (debet - kredit) AS realisasi
                            FROM
                                trdju_pkd a
                            INNER JOIN trhju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_unit = b.kd_skpd
                            WHERE LEFT (a.kd_rek6, 1) IN ('5') AND MONTH (tgl_voucher) <= ?
                            AND YEAR (tgl_voucher) = ?
                            GROUP BY LEFT (kd_sub_kegiatan, 4)
                        ) b ON a.kd_urusan = b.kd_urusan
                    GROUP BY a.kd_fungsi, a.kd_urusan, a.nm_fungsi
                ) b ON a.kode = b.kode
            UNION ALL

            SELECT
                a.kode AS kode,
                a.nama,
                ISNULL(a.anggaran, 0) AS anggaran,
                ISNULL(b.realisasi, 0) AS realisasi,
                ISNULL(anggaran - realisasi, 0) AS selisih
            FROM
                (
                    SELECT
                        a.kd_fungsi AS kode,
                        a.nm_fungsi AS nama,
                        SUM (ISNULL(anggaran, 0)) AS anggaran
                    FROM
                        ms_fungsi a
                    LEFT JOIN
                        (
                            SELECT
                                RTRIM(a.kd_fungsi) AS kode,
                                SUM (ISNULL(anggaran, 0)) AS anggaran
                            FROM
                                ms_sub_fungsi a
                            LEFT JOIN
                                (
                                    SELECT
                                        LEFT (kd_sub_kegiatan, 4) AS kd_urusan,
                                        SUM (nilai) AS anggaran
                                    FROM
                                        trdrka a
                                    WHERE LEFT (a.kd_rek6, 1) IN ('5') AND a.jns_ang = ?
                                    GROUP BY LEFT (kd_sub_kegiatan, 4)
                                ) b ON a.kd_urusan = b.kd_urusan
                            GROUP BY a.kd_fungsi
                        ) b ON a.kd_fungsi = LEFT (b.kode, 1)
                    GROUP BY a.kd_fungsi, nm_fungsi
                ) a LEFT JOIN
                (
                    SELECT
                        a.kd_fungsi AS kode,
                        a.nm_fungsi AS nama,
                        SUM (ISNULL(realisasi, 0)) AS realisasi
                    FROM
                        ms_fungsi a
                    LEFT JOIN
                        (
                            SELECT
                                RTRIM(a.kd_fungsi) AS kode,
                                SUM (ISNULL(realisasi, 0)) AS realisasi
                            FROM
                                ms_sub_fungsi a
                            LEFT JOIN
                                (
                                    SELECT
                                        LEFT (kd_sub_kegiatan, 4) AS kd_urusan,
                                        SUM (debet - kredit) AS realisasi
                                    FROM
                                        trdju_pkd a
                                    INNER JOIN trhju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_unit = b.kd_skpd
                                    WHERE LEFT (a.kd_rek6, 1) IN ('5') AND MONTH (tgl_voucher) <= ?
                                    AND YEAR (tgl_voucher) = ?
                                    GROUP BY LEFT (kd_sub_kegiatan, 4)
                                ) b ON a.kd_urusan = b.kd_urusan
                                GROUP BY a.kd_fungsi
                        ) b ON a.kd_fungsi = LEFT (b.kode, 1)
                    GROUP BY a.kd_fungsi, nm_fungsi
                ) b ON a.kode = b.kode
            ORDER BY kode", [$jns_ang, $bulan, $current_year, $jns_ang, $bulan, $current_year]
        );


        $view = view('laporan_pemda.lra_keselarasan.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream("Laporan_keselarasan.pdf");
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
