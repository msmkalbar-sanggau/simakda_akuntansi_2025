<?php

namespace App\Http\Controllers\Perda\LampiranIII;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LampiranIIIController extends Controller
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
        return view('perda.lampiran_III.index')->with($data);
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
            "SELECT group_id, group_name, padding, is_bold, right_align,
                        SUM ( jurnal.nilai_berjalan ) AS nilai_berjalan,
                        SUM ( jurnal.nilai_lalu ) AS nilai_lalu
                    FROM map_lo_perda_lampiran_iii
                    LEFT JOIN (
                            SELECT
                                kd_rek6,
                                SUM ( nilai_berjalan ) AS nilai_berjalan,
                                SUM ( nilai_lalu ) AS nilai_lalu
                            FROM
                                (
                                    SELECT
                                        trdju_pkd.kd_rek6,
                                    CASE
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '7'
                                            AND year(trhju_pkd.tgl_voucher) = '$thn' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '8'
                                            AND year(trhju_pkd.tgl_voucher) = '$thn'  THEN
                                            SUM ( debet ) - SUM ( kredit ) ELSE 0
                                    END AS nilai_berjalan,
                                    CASE
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '7'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn_lalu' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '8'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn_lalu' THEN
                                            SUM ( debet ) - SUM ( kredit ) ELSE 0
                                    END AS nilai_lalu
                                        FROM
                                            trhju_pkd
                                        JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher
                                            AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                                        WHERE
                                            left(trdju_pkd.kd_rek6,1) IN ( '7', '8' )
                                        GROUP BY
                                        trdju_pkd.kd_rek6,trhju_pkd.tgl_voucher
                                ) jurnal
                                    GROUP BY kd_rek6
                        ) jurnal ON LEFT ( jurnal.kd_rek6, LEN( kd_rek ) ) = map_lo_perda_lampiran_iii.kd_rek
                            GROUP BY
                                group_id,
                                group_name,
                                padding,
                                is_bold,
                                show_kd_rek,
                                right_align
                            ORDER BY
                            group_id, group_name"
        );

        //Surplus/ Defisit dari Operasi
        $jumlahsddo = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 1 ) = '7' AND year(b.tgl_voucher) = '$thn' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 1 ) = '8' AND year(b.tgl_voucher) = '$thn' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 2 ) IN ('71', '72', '73', '81', '82')"
        ))->first();

        $jumlahsddos = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 1 ) = '7' AND year(b.tgl_voucher) = '$thn_lalu' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 1 ) = '8' AND year(b.tgl_voucher) = '$thn_lalu' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd_lalu a
            INNER JOIN trhju_pkd_lalu b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 2 ) IN ('71', '72', '73', '81', '82')"
        ))->first();
        $bertambah_berkurang_sddos = $jumlahsddo->nilai - $jumlahsddos->nilai;

        // Surplus Defisit Sebelum Pos Luar Biasa
        $jumlahsdsplb = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 1 ) = '7' AND year(b.tgl_voucher) = '$thn' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 1 ) = '8' AND year(b.tgl_voucher) = '$thn' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 2 ) IN ('71', '72', '73', '74', '81', '82', '83')"
        ))->first();

        $jumlahsdsplbs = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 1 ) = '7' AND year(b.tgl_voucher) = '$thn_lalu' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 1 ) = '8' AND year(b.tgl_voucher) = '$thn_lalu' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd_lalu a
            INNER JOIN trhju_pkd_lalu b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 2 ) IN ('71', '72', '73', '74', '81', '82', '83')"
        ))->first();
        $bertambah_berkurang_sdsplb = $jumlahsdsplb->nilai - $jumlahsdsplbs->nilai;

        // Surplus/Defisit dari Kegiatan Non Operasional
        $jumlahsdkno = collect(\DB::select(
            "SELECT
                SUM(kredit-debet) as nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 4) IN ('7401','7402', '7403','8501', '8502', '8503') and year(b.tgl_voucher) = '$thn'"
        ))->first();

        $jumlahsdknos = collect(\DB::select(
            "SELECT
                SUM(kredit-debet) as nilai
            FROM trdju_pkd_lalu a
            INNER JOIN trhju_pkd_lalu b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 4) IN ('7401','7402', '7403','8501', '8502', '8503') and year(b.tgl_voucher) = '$thn_lalu'"
        ))->first();
        $bertambah_berkurang_sdknos = $jumlahsdkno->nilai - $jumlahsdknos->nilai;

        // surplus defisit lo
        $jumlahsdl = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 1 ) = '7' AND year(b.tgl_voucher) = '$thn' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 1 ) = '8' AND year(b.tgl_voucher) = '$thn' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 1) IN ('7', '8')"
        ))->first();

        $jumlahsdls = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 1 ) = '7' AND year(b.tgl_voucher) = '$thn_lalu' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 1 ) = '8' AND year(b.tgl_voucher) = '$thn_lalu' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd_lalu a
            INNER JOIN trhju_pkd_lalu b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 1 ) IN ('7', '8')"
        ))->first();
        $bertambah_berkurang_sdl = $jumlahsdl->nilai - $jumlahsdls->nilai;

        $view = view('perda.lampiran_III.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'jumlahsdsplb' => $jumlahsdsplb,
            'jumlahsdsplbs' => $jumlahsdsplbs,
            'bertambah_berkurang_sdsplb' => $bertambah_berkurang_sdsplb,
            'jumlahsddo' => $jumlahsddo,
            'jumlahsddos' => $jumlahsddos,
            'bertambah_berkurang_sddos' => $bertambah_berkurang_sddos,
            'jumlahsdl' => $jumlahsdl,
            'jumlahsdls' => $jumlahsdls,
            'bertambah_berkurang_sdl' => $bertambah_berkurang_sdl,
            'jumlahsdkno' => $jumlahsdkno,
            'jumlahsdknos' => $jumlahsdknos,
            'bertambah_berkurang_sdknos' => $bertambah_berkurang_sdknos,
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
