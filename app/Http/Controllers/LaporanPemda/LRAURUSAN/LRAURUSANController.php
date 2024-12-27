<?php

namespace App\Http\Controllers\LaporanPemda\LRAURUSAN;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class LRAURUSANController extends Controller
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
        return view('laporan_pemda.lra_urusan.index')->with($data);
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
        $bulan = $request->bulan;
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $jns_ang = $request->jns_ang;
        $penandatangan = $request->penandatangan;
        $tgl_ttd = $request->tgl_ttd;
        $jenis_cetak = $request->jenis_cetak;
        $btt = $request->btt;
        $current_year = tahun_anggaran();
        $jenis = $request->jenis;

        $ttd = DB::table('ms_ttd')->where(['nip' => $penandatangan])->first();

        $periode_clause = $bulan ? "AND MONTH (a.tgl_voucher) <='$bulan'"
                    : "AND a.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";

        $periode_clause1 = $bulan ? "AND MONTH (tgl_sp2d) <='$bulan'"
                    : "AND tgl_sp2d BETWEEN '$tgl_awal' AND '$tgl_akhir'";

        if ($jenis_cetak == '1') {
            if ($btt == 1) {
                $data = DB::select(
                    "SELECT
                        kd_skpd, kd_sub_kegiatan AS kode, nm_rek, ang_peg,
                        ang_brng, ang_bng, ang_mod, ang_hibah, ang_bansos,
                        ang_bghasil, ang_bankeu, ang_btt, real_peg, real_brng,
                        real_bng, real_mod, real_hibah, real_bansos, real_bghasil, real_bankeu, real_btt
                    FROM
                        (
                            SELECT
                                a.kd_skpd, a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg, 0) AS ang_peg,
                                ISNULL(ang_brng, 0) AS ang_brng,
                                ISNULL(ang_bng, 0) AS ang_bng,
                                ISNULL(ang_mod, 0) AS ang_mod,
                                ISNULL(ang_hibah, 0) AS ang_hibah,
                                ISNULL(ang_bansos, 0) AS ang_bansos,
                                ISNULL(ang_bghasil, 0) AS ang_bghasil,
                                ISNULL(ang_bankeu, 0) AS ang_bankeu,
                                ISNULL(ang_btt, 0) AS ang_btt,
                                ISNULL(real_peg, 0) AS real_peg,
                                ISNULL(real_brng, 0) AS real_brng,
                                ISNULL(real_bng, 0) AS real_bng,
                                ISNULL(real_mod, 0) AS real_mod,
                                ISNULL(real_hibah, 0) AS real_hibah,
                                ISNULL(real_bansos, 0) AS real_bansos,
                                ISNULL(real_bghasil, 0) AS real_bghasil,
                                ISNULL(real_bankeu, 0) AS real_bankeu,
                                ISNULL(real_btt, 0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT (a.kd_skpd, 1) AS kd_skpd,
                                        LEFT (a.kd_skpd, 1) AS kd_sub_kegiatan,
                                        b.nm_urusan AS nm_rek,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN ('5101')
                                            THEN nilai ELSE 0 END
                                        ) AS ang_peg,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5102'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_brng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5103'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN (
                                                '5201', '5202', '5203', '5204', '5205', '5206'
                                            ) THEN nilai ELSE 0 END
                                        ) AS ang_mod,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5105'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_hibah,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5106'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bansos,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5401'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bghasil,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5402'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bankeu,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5301'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_urusan b ON LEFT (a.kd_skpd, 1) = b.kd_urusan
                                    WHERE LEFT (a.kd_rek6, 4) IN (
                                            '5101', '5102', '5103', '5201', '5202',
                                            '5203', '5204', '5205', '5206', '5105',
                                            '5106', '5401', '5402', '5301'
                                        ) AND jns_ang = '$jns_ang'
                                    GROUP BY LEFT (a.kd_skpd, 1), b.nm_urusan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT (a.kd_skpd, 1) AS kd_skpd,
                                            SUM (real_peg) AS real_peg,
                                            SUM (real_brng) AS real_brng,
                                            SUM (real_bng) AS real_bng,
                                            SUM (real_mod) AS real_mod,
                                            SUM (real_hibah) AS real_hibah,
                                            SUM (real_bansos) AS real_bansos,
                                            SUM (real_bghasil) AS real_bghasil,
                                            SUM (real_bankeu) AS real_bankeu,
                                            SUM (real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN ('5101')
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_peg,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5102'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_brng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5103'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN
                                                        (
                                                            '5201', '5202', '5203',
                                                            '5204', '5205', '5206'
                                                        ) THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_mod,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5105'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_hibah,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5106'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bansos,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5401'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bghasil,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5402'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bankeu,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5301'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                                                WHERE LEFT (b.map_real, 4) IN (
                                                    '5101', '5102', '5103', '5201', '5202', '5203',
                                                    '5204', '5205', '5206', '5105', '5106', '5401', '5402'
                                                    ) $periode_clause
                                                    AND YEAR (a.tgl_voucher) ='$current_year'
                                                GROUP BY a.kd_skpd

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd,
                                                    0 AS real_peg,
                                                    0 AS real_brng,
                                                    0 AS real_bng,
                                                    0 AS real_mod,
                                                    0 AS real_hibah,
                                                    0 AS real_bansos,
                                                    0 AS real_bghasil,
                                                    0 AS real_bankeu,
                                                    SUM (trdspp.nilai) AS real_btt
                                                FROM trhsp2d
                                                JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                                                JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE ( trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1' )
                                                    AND ( trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1' )
                                                    AND LEFT (trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR (tgl_sp2d) ='$current_year'
                                                GROUP BY trhsp2d.kd_skpd
                                            ) a GROUP BY LEFT (a.kd_skpd, 1)
                                    ) b ON a.kd_skpd = b.kd_skpd --1 urusan
                            UNION ALL

                            SELECT
                                a.kd_skpd,
                                a.kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg, 0) AS ang_peg,
                                ISNULL(ang_brng, 0) AS ang_brng,
                                ISNULL(ang_bng, 0) AS ang_bng,
                                ISNULL(ang_mod, 0) AS ang_mod,
                                ISNULL(ang_hibah, 0) AS ang_hibah,
                                ISNULL(ang_bansos, 0) AS ang_bansos,
                                ISNULL(ang_bghasil, 0) AS ang_bghasil,
                                ISNULL(ang_bankeu, 0) AS ang_bankeu,
                                ISNULL(ang_btt, 0) AS ang_btt,
                                ISNULL(real_peg, 0) AS real_peg,
                                ISNULL(real_brng, 0) AS real_brng,
                                ISNULL(real_bng, 0) AS real_bng,
                                ISNULL(real_mod, 0) AS real_mod,
                                ISNULL(real_hibah, 0) AS real_hibah,
                                ISNULL(real_bansos, 0) AS real_bansos,
                                ISNULL(real_bghasil, 0) AS real_bghasil,
                                ISNULL(real_bankeu, 0) AS real_bankeu,
                                ISNULL(real_btt, 0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT (a.kd_skpd, 4) AS kd_skpd,
                                        LEFT (a.kd_skpd, 4) AS kd_sub_kegiatan,
                                        b.nm_bidang_urusan AS nm_rek,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN ('5101')
                                            THEN nilai ELSE 0 END
                                        ) AS ang_peg,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5102'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_brng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5103'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN (
                                                '5201', '5202', '5203', '5204', '5205', '5206'
                                            ) THEN nilai ELSE 0 END
                                        ) AS ang_mod,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5105'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_hibah,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5106'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bansos,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5401'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bghasil,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5402'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bankeu,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5301'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_btt
                                    FROM
                                        trdrka a
                                    INNER JOIN ms_bidang_urusan b ON LEFT (a.kd_skpd, 4) = b.kd_bidang_urusan
                                    WHERE
                                        LEFT (a.kd_rek6, 4) IN (
                                            '5101', '5102', '5103', '5201', '5202', '5203', '5204',
                                            '5205', '5206', '5105', '5106', '5401', '5402', '5301'
                                        ) AND jns_ang = '$jns_ang'
                                    GROUP BY LEFT (a.kd_skpd, 4), b.nm_bidang_urusan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT (a.kd_skpd, 4) AS kd_skpd,
                                            SUM (real_peg) AS real_peg,
                                            SUM (real_brng) AS real_brng,
                                            SUM (real_bng) AS real_bng,
                                            SUM (real_mod) AS real_mod,
                                            SUM (real_hibah) AS real_hibah,
                                            SUM (real_bansos) AS real_bansos,
                                            SUM (real_bghasil) AS real_bghasil,
                                            SUM (real_bankeu) AS real_bankeu,
                                            SUM (real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN ('5101')
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_peg,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5102'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_brng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5103'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN (
                                                            '5201', '5202', '5203', '5204', '5205', '5206'
                                                        ) THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_mod,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5105'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_hibah,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5106'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bansos,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5401'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bghasil,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5402'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bankeu,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5301'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                                                WHERE LEFT (b.map_real, 4) IN (
                                                        '5101', '5102', '5103', '5201', '5202', '5203',
                                                        '5204', '5205', '5206', '5105', '5106', '5401', '5402'
                                                    ) $periode_clause
                                                    AND YEAR (a.tgl_voucher) ='$current_year'
                                                GROUP BY a.kd_skpd

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd,
                                                    0 AS real_peg,
                                                    0 AS real_brng,
                                                    0 AS real_bng,
                                                    0 AS real_mod,
                                                    0 AS real_hibah,
                                                    0 AS real_bansos,
                                                    0 AS real_bghasil,
                                                    0 AS real_bankeu,
                                                    SUM (trdspp.nilai) AS real_btt
                                                FROM trhsp2d
                                                JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                                                JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE ( trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1' )
                                                    AND ( trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1' )
                                                    AND LEFT (trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR (tgl_sp2d) ='$current_year'
                                                GROUP BY trhsp2d.kd_skpd
                                            ) a GROUP BY LEFT (a.kd_skpd, 4)
                                    ) b ON a.kd_skpd = b.kd_skpd -- 2 bidang urusan
                            UNION ALL

                            SELECT
                                a.kd_skpd, LEFT (a.kd_skpd, 4) + '.' + a.kd_sub_kegiatan AS kd_sub_kegiatan,
                                nm_rek, ISNULL(ang_peg, 0) AS ang_peg,
                                ISNULL(ang_brng, 0) AS ang_brng,
                                ISNULL(ang_bng, 0) AS ang_bng,
                                ISNULL(ang_mod, 0) AS ang_mod,
                                ISNULL(ang_hibah, 0) AS ang_hibah,
                                ISNULL(ang_bansos, 0) AS ang_bansos,
                                ISNULL(ang_bghasil, 0) AS ang_bghasil,
                                ISNULL(ang_bankeu, 0) AS ang_bankeu,
                                ISNULL(ang_btt, 0) AS ang_btt,
                                ISNULL(real_peg, 0) AS real_peg,
                                ISNULL(real_brng, 0) AS real_brng,
                                ISNULL(real_bng, 0) AS real_bng,
                                ISNULL(real_mod, 0) AS real_mod,
                                ISNULL(real_hibah, 0) AS real_hibah,
                                ISNULL(real_bansos, 0) AS real_bansos,
                                ISNULL(real_bghasil, 0) AS real_bghasil,
                                ISNULL(real_bankeu, 0) AS real_bankeu,
                                ISNULL(real_btt, 0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT (a.kd_skpd, 17) AS kd_skpd,
                                        LEFT (a.kd_skpd, 17) AS kd_sub_kegiatan,
                                        b.nm_org AS nm_rek,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN ('5101')
                                            THEN nilai ELSE 0 END
                                        ) AS ang_peg,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5102'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_brng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5103'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN (
                                                '5201', '5202', '5203', '5204', '5205', '5206'
                                            ) THEN nilai ELSE 0 END
                                        ) AS ang_mod,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5105'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_hibah,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5106'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bansos,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5401'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bghasil,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5402'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bankeu,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5301'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_organisasi b ON LEFT (a.kd_skpd, 17) = b.kd_org
                                    WHERE LEFT (a.kd_rek6, 4) IN (
                                        '5101', '5102', '5103', '5201', '5202', '5203', '5204',
                                        '5205', '5206', '5105', '5106', '5401', '5402', '5301'
                                        ) AND jns_ang = '$jns_ang'
                                    GROUP BY LEFT (a.kd_skpd, 17), b.nm_org
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT (a.kd_skpd, 17) AS kd_skpd,
                                            SUM (real_peg) AS real_peg,
                                            SUM (real_brng) AS real_brng,
                                            SUM (real_bng) AS real_bng,
                                            SUM (real_mod) AS real_mod,
                                            SUM (real_hibah) AS real_hibah,
                                            SUM (real_bansos) AS real_bansos,
                                            SUM (real_bghasil) AS real_bghasil,
                                            SUM (real_bankeu) AS real_bankeu,
                                            SUM (real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN ('5101')
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_peg,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5102'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_brng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5103'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN (
                                                            '5201', '5202', '5203', '5204', '5205', '5206'
                                                        ) THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_mod,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5105'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_hibah,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5106'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bansos,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5401'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bghasil,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5402'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bankeu,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5301'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                                                WHERE LEFT (b.map_real, 4) IN (
                                                        '5101', '5102', '5103', '5201', '5202', '5203',
                                                        '5204', '5205', '5206', '5105', '5106', '5401', '5402'
                                                    ) $periode_clause
                                                    AND YEAR (a.tgl_voucher) ='$current_year'
                                                GROUP BY a.kd_skpd

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd,
                                                    0 AS real_peg,
                                                    0 AS real_brng,
                                                    0 AS real_bng,
                                                    0 AS real_mod,
                                                    0 AS real_hibah,
                                                    0 AS real_bansos,
                                                    0 AS real_bghasil,
                                                    0 AS real_bankeu,
                                                    SUM (trdspp.nilai) AS real_btt
                                                FROM trhsp2d
                                                JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                                                JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE ( trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1' )
                                                    AND ( trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1' )
                                                    AND LEFT (trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR (tgl_sp2d) ='$current_year'
                                                GROUP BY trhsp2d.kd_skpd
                                            ) a GROUP BY LEFT (a.kd_skpd, 17)
                                    ) b ON a.kd_skpd = b.kd_skpd -- 3 org
                            UNION ALL

                            SELECT
                                a.kd_skpd,
                                LEFT (a.kd_skpd, 4) + '.' + a.kd_sub_kegiatan kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg, 0) AS ang_peg,
                                ISNULL(ang_brng, 0) AS ang_brng,
                                ISNULL(ang_bng, 0) AS ang_bng,
                                ISNULL(ang_mod, 0) AS ang_mod,
                                ISNULL(ang_hibah, 0) AS ang_hibah,
                                ISNULL(ang_bansos, 0) AS ang_bansos,
                                ISNULL(ang_bghasil, 0) AS ang_bghasil,
                                ISNULL(ang_bankeu, 0) AS ang_bankeu,
                                ISNULL(ang_btt, 0) AS ang_btt,
                                ISNULL(real_peg, 0) AS real_peg,
                                ISNULL(real_brng, 0) AS real_brng,
                                ISNULL(real_bng, 0) AS real_bng,
                                ISNULL(real_mod, 0) AS real_mod,
                                ISNULL(real_hibah, 0) AS real_hibah,
                                ISNULL(real_bansos, 0) AS real_bansos,
                                ISNULL(real_bghasil, 0) AS real_bghasil,
                                ISNULL(real_bankeu, 0) AS real_bankeu,
                                ISNULL(real_btt, 0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd,
                                        a.kd_skpd AS kd_sub_kegiatan,
                                        b.nm_skpd AS nm_rek,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN ('5101')
                                            THEN nilai ELSE 0 END
                                        ) AS ang_peg,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5102'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_brng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5103'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN (
                                                '5201', '5202', '5203', '5204', '5205', '5206'
                                            ) THEN nilai ELSE 0 END
                                        ) AS ang_mod,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5105'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_hibah,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5106'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bansos,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5401'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bghasil,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5402'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bankeu,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5301'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_skpd b ON a.kd_skpd = b.kd_skpd
                                    WHERE LEFT (a.kd_rek6, 4) IN (
                                            '5101', '5102', '5103', '5201', '5202', '5203', '5204',
                                            '5205', '5206', '5105', '5106', '5401', '5402', '5301'
                                        ) AND jns_ang = '$jns_ang'
                                    GROUP BY a.kd_skpd, b.nm_skpd
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            kd_skpd,
                                            SUM (real_peg) AS real_peg,
                                            SUM (real_brng) AS real_brng,
                                            SUM (real_bng) AS real_bng,
                                            SUM (real_mod) AS real_mod,
                                            SUM (real_hibah) AS real_hibah,
                                            SUM (real_bansos) AS real_bansos,
                                            SUM (real_bghasil) AS real_bghasil,
                                            SUM (real_bankeu) AS real_bankeu,
                                            SUM (real_btt) AS real_btt
                                        FROM
                                        (
                                            SELECT
                                                a.kd_skpd,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) IN ('5101')
                                                    THEN (debet - kredit) ELSE 0 END
                                                ) AS real_peg,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) = '5102'
                                                    THEN (debet - kredit) ELSE 0 END
                                                ) AS real_brng,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) = '5103'
                                                    THEN (debet - kredit) ELSE 0 END
                                                ) AS real_bng,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) IN (
                                                        '5201', '5202', '5203', '5204', '5205', '5206'
                                                    ) THEN (debet - kredit) ELSE 0 END
                                                ) AS real_mod,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) = '5105'
                                                    THEN (debet - kredit) ELSE 0 END
                                                ) AS real_hibah,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) = '5106'
                                                    THEN (debet - kredit) ELSE 0 END
                                                ) AS real_bansos,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) = '5401'
                                                    THEN (debet - kredit) ELSE 0 END
                                                ) AS real_bghasil,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) = '5402'
                                                    THEN (debet - kredit) ELSE 0 END
                                                ) AS real_bankeu,
                                                SUM (
                                                    CASE WHEN LEFT (b.map_real, 4) = '5301'
                                                    THEN (debet - kredit) ELSE 0 END
                                                ) AS real_btt
                                            FROM trhju_pkd a
                                            INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                                            WHERE LEFT (b.map_real, 4) IN (
                                                    '5101', '5102', '5103', '5201', '5202', '5203',
                                                    '5204', '5205', '5206', '5105', '5106', '5401', '5402'
                                                ) $periode_clause
                                                AND YEAR (a.tgl_voucher) ='$current_year'
                                            GROUP BY a.kd_skpd

                                            UNION ALL

                                            SELECT
                                                trhsp2d.kd_skpd,
                                                0 AS real_peg,
                                                0 AS real_brng,
                                                0 AS real_bng,
                                                0 AS real_mod,
                                                0 AS real_hibah,
                                                0 AS real_bansos,
                                                0 AS real_bghasil,
                                                0 AS real_bankeu,
                                                SUM (trdspp.nilai) AS real_btt
                                            FROM trhsp2d
                                            JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                                            JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                            WHERE ( trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1' )
                                                AND ( trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1' )
                                                AND LEFT (trdspp.kd_rek6, 4) = '5301'
                                                $periode_clause1
                                                AND YEAR (tgl_sp2d) ='$current_year'
                                            GROUP BY trhsp2d.kd_skpd
                                        ) a GROUP BY a.kd_skpd
                                    ) b ON a.kd_skpd = b.kd_skpd --4 unit
                            UNION ALL

                            SELECT
                                a.kd_skpd + '.1',
                                a.kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg, 0) AS ang_peg,
                                ISNULL(ang_brng, 0) AS ang_brng,
                                ISNULL(ang_bng, 0) AS ang_bng,
                                ISNULL(ang_mod, 0) AS ang_mod,
                                ISNULL(ang_hibah, 0) AS ang_hibah,
                                ISNULL(ang_bansos, 0) AS ang_bansos,
                                ISNULL(ang_bghasil, 0) AS ang_bghasil,
                                ISNULL(ang_bankeu, 0) AS ang_bankeu,
                                ISNULL(ang_btt, 0) AS ang_btt,
                                ISNULL(real_peg, 0) AS real_peg,
                                ISNULL(real_brng, 0) AS real_brng,
                                ISNULL(real_bng, 0) AS real_bng,
                                ISNULL(real_mod, 0) AS real_mod,
                                ISNULL(real_hibah, 0) AS real_hibah,
                                ISNULL(real_bansos, 0) AS real_bansos,
                                ISNULL(real_bghasil, 0) AS real_bghasil,
                                ISNULL(real_bankeu, 0) AS real_bankeu,
                                ISNULL(real_btt, 0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd,
                                        LEFT (a.kd_sub_Kegiatan, 7) AS kd_sub_kegiatan,
                                        b.nm_program AS nm_rek,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN ('5101')
                                            THEN nilai ELSE 0 END
                                        ) AS ang_peg,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5102'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_brng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5103'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN (
                                                '5201', '5202', '5203', '5204', '5205', '5206'
                                            ) THEN nilai ELSE 0 END
                                        ) AS ang_mod,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5105'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_hibah,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5106'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bansos,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5401'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bghasil,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5402'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bankeu,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5301'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd = b.kd_skpd AND a.kd_sub_kegiatan = b.kd_sub_kegiatan
                                    WHERE LEFT (a.kd_rek6, 4) IN (
                                            '5101', '5102', '5103', '5201', '5202', '5203', '5204',
                                            '5205', '5206', '5105', '5106', '5401', '5402', '5301'
                                        ) AND a.jns_ang = '$jns_ang'
                                    GROUP BY a.kd_skpd, LEFT (a.kd_sub_Kegiatan, 7), b.nm_program
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            kd_skpd,
                                            kd_sub_kegiatan,
                                            SUM (real_peg) AS real_peg,
                                            SUM (real_brng) AS real_brng,
                                            SUM (real_bng) AS real_bng,
                                            SUM (real_mod) AS real_mod,
                                            SUM (real_hibah) AS real_hibah,
                                            SUM (real_bansos) AS real_bansos,
                                            SUM (real_bghasil) AS real_bghasil,
                                            SUM (real_bankeu) AS real_bankeu,
                                            SUM (real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    LEFT (b.kd_sub_Kegiatan, 7) kd_sub_kegiatan,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN ('5101')
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_peg,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5102'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_brng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5103'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN (
                                                            '5201', '5202', '5203', '5204', '5205', '5206'
                                                        ) THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_mod,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5105'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_hibah,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5106'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bansos,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5401'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bghasil,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5402'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bankeu,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5301'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                                                WHERE LEFT (b.map_real, 4) IN (
                                                        '5101', '5102', '5103', '5201', '5202', '5203',
                                                        '5204', '5205', '5206', '5105', '5106', '5401', '5402'
                                                    ) $periode_clause
                                                    AND YEAR (a.tgl_voucher) ='$current_year'
                                                GROUP BY a.kd_skpd, LEFT (b.kd_sub_Kegiatan, 7)
                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd,
                                                    LEFT (trhspp.kd_sub_kegiatan, 7) AS kd_sub_kegiatan,
                                                    0 AS real_peg,
                                                    0 AS real_brng,
                                                    0 AS real_bng,
                                                    0 AS real_mod,
                                                    0 AS real_hibah,
                                                    0 AS real_bansos,
                                                    0 AS real_bghasil,
                                                    0 AS real_bankeu,
                                                    SUM (trdspp.nilai) AS real_btt
                                                FROM
                                                    trhsp2d
                                                JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                                                JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE
                                                    (
                                                        trhspp.sp2d_batal IS NULL
                                                        OR trhspp.sp2d_batal <> '1'
                                                    )
                                                AND (
                                                    trhsp2d.sp2d_batal IS NULL
                                                    OR trhsp2d.sp2d_batal <> '1'
                                                )
                                                AND LEFT (trdspp.kd_rek6, 4) = '5301'
                                                $periode_clause1
                                                AND YEAR (tgl_sp2d) ='$current_year'
                                                GROUP BY trhsp2d.kd_skpd, LEFT (trhspp.kd_sub_kegiatan, 7)
                                            ) a GROUP BY kd_skpd, kd_sub_kegiatan
                                    ) b ON a.kd_skpd = b.kd_skpd AND a.kd_sub_kegiatan = b.kd_sub_kegiatan -- 5 program
                            UNION ALL

                            SELECT
                                a.kd_skpd + '.1',
                                a.kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg, 0) AS ang_peg,
                                ISNULL(ang_brng, 0) AS ang_brng,
                                ISNULL(ang_bng, 0) AS ang_bng,
                                ISNULL(ang_mod, 0) AS ang_mod,
                                ISNULL(ang_hibah, 0) AS ang_hibah,
                                ISNULL(ang_bansos, 0) AS ang_bansos,
                                ISNULL(ang_bghasil, 0) AS ang_bghasil,
                                ISNULL(ang_bankeu, 0) AS ang_bankeu,
                                ISNULL(ang_btt, 0) AS ang_btt,
                                ISNULL(real_peg, 0) AS real_peg,
                                ISNULL(real_brng, 0) AS real_brng,
                                ISNULL(real_bng, 0) AS real_bng,
                                ISNULL(real_mod, 0) AS real_mod,
                                ISNULL(real_hibah, 0) AS real_hibah,
                                ISNULL(real_bansos, 0) AS real_bansos,
                                ISNULL(real_bghasil, 0) AS real_bghasil,
                                ISNULL(real_bankeu, 0) AS real_bankeu,
                                ISNULL(real_btt, 0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd,
                                        LEFT (a.kd_sub_Kegiatan, 12) AS kd_sub_kegiatan,
                                        b.nm_program AS nm_rek,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN ('5101')
                                            THEN nilai ELSE 0 END
                                        ) AS ang_peg,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5102'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_brng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5103'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN (
                                                '5201', '5202', '5203', '5204', '5205', '5206'
                                            ) THEN nilai ELSE 0 END
                                        ) AS ang_mod,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5105'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_hibah,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5106'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bansos,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5401'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bghasil,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5402'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bankeu,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5301'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd = b.kd_skpd AND a.kd_sub_kegiatan = b.kd_sub_kegiatan
                                    WHERE LEFT (a.kd_rek6, 4) IN (
                                            '5101', '5102', '5103', '5201', '5202', '5203', '5204',
                                            '5205', '5206', '5105', '5106', '5401', '5402', '5301'
                                        ) AND a.jns_ang = '$jns_ang'
                                    GROUP BY a.kd_skpd, LEFT (a.kd_sub_Kegiatan, 12), b.nm_program
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            kd_skpd,
                                            kd_sub_kegiatan,
                                            SUM (real_peg) AS real_peg,
                                            SUM (real_brng) AS real_brng,
                                            SUM (real_bng) AS real_bng,
                                            SUM (real_mod) AS real_mod,
                                            SUM (real_hibah) AS real_hibah,
                                            SUM (real_bansos) AS real_bansos,
                                            SUM (real_bghasil) AS real_bghasil,
                                            SUM (real_bankeu) AS real_bankeu,
                                            SUM (real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    LEFT (b.kd_sub_Kegiatan, 12) AS kd_sub_kegiatan,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN ('5101')
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_peg,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5102'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_brng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5103'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN (
                                                            '5201', '5202', '5203', '5204', '5205', '5206'
                                                        ) THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_mod,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5105'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_hibah,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5106'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bansos,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5401'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bghasil,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5402'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bankeu,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5301'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                                                WHERE LEFT (b.map_real, 4) IN (
                                                        '5101', '5102', '5103', '5201', '5202', '5203', '5204',
                                                        '5205', '5206', '5105', '5106', '5401', '5402'
                                                    ) $periode_clause
                                                    AND YEAR (a.tgl_voucher) ='$current_year'
                                                GROUP BY a.kd_skpd, LEFT (b.kd_sub_Kegiatan, 12)
                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd,
                                                    LEFT (trhspp.kd_sub_kegiatan, 12) AS kd_sub_kegiatan,
                                                    0 AS real_peg,
                                                    0 AS real_brng,
                                                    0 AS real_bng,
                                                    0 AS real_mod,
                                                    0 AS real_hibah,
                                                    0 AS real_bansos,
                                                    0 AS real_bghasil,
                                                    0 AS real_bankeu,
                                                    SUM (trdspp.nilai) AS real_btt
                                                FROM trhsp2d
                                                JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                                                JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE ( trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1' )
                                                    AND ( trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                                                    AND LEFT (trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR (tgl_sp2d) ='$current_year'
                                                GROUP BY trhsp2d.kd_skpd, LEFT (trhspp.kd_sub_kegiatan, 12)
                                            ) a GROUP BY kd_skpd, kd_sub_kegiatan
                                    ) b ON a.kd_skpd = b.kd_skpd AND a.kd_sub_kegiatan = b.kd_sub_kegiatan -- 6 kegiatan
                            UNION ALL

                            SELECT
                                a.kd_skpd + '.1',
                                a.kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg, 0) AS ang_peg,
                                ISNULL(ang_brng, 0) AS ang_brng,
                                ISNULL(ang_bng, 0) AS ang_bng,
                                ISNULL(ang_mod, 0) AS ang_mod,
                                ISNULL(ang_hibah, 0) AS ang_hibah,
                                ISNULL(ang_bansos, 0) AS ang_bansos,
                                ISNULL(ang_bghasil, 0) AS ang_bghasil,
                                ISNULL(ang_bankeu, 0) AS ang_bankeu,
                                ISNULL(ang_btt, 0) AS ang_btt,
                                ISNULL(real_peg, 0) AS real_peg,
                                ISNULL(real_brng, 0) AS real_brng,
                                ISNULL(real_bng, 0) AS real_bng,
                                ISNULL(real_mod, 0) AS real_mod,
                                ISNULL(real_hibah, 0) AS real_hibah,
                                ISNULL(real_bansos, 0) AS real_bansos,
                                ISNULL(real_bghasil, 0) AS real_bghasil,
                                ISNULL(real_bankeu, 0) AS real_bankeu,
                                ISNULL(real_btt, 0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd,
                                        a.kd_sub_kegiatan,
                                        b.nm_kegiatan nm_rek,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN ('5101')
                                            THEN nilai ELSE 0 END
                                        ) AS ang_peg,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5102'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_brng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5103'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bng,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) IN (
                                                '5201', '5202', '5203', '5204', '5205', '5206'
                                            ) THEN nilai ELSE 0 END
                                        ) AS ang_mod,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5105'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_hibah,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5106'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bansos,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5401'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bghasil,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5402'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_bankeu,
                                        SUM (
                                            CASE WHEN LEFT (kd_rek6, 4) = '5301'
                                            THEN nilai ELSE 0 END
                                        ) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd = b.kd_skpd AND a.kd_sub_kegiatan = b.kd_sub_kegiatan
                                    WHERE LEFT (a.kd_rek6, 4) IN (
                                            '5101', '5102', '5103', '5201', '5202', '5203', '5204',
                                            '5205', '5206', '5105', '5106', '5401', '5402', '5301'
                                        ) AND a.jns_ang = '$jns_ang'
                                    GROUP BY a.kd_skpd, a.kd_sub_kegiatan, b.nm_kegiatan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            kd_skpd,
                                            kd_sub_kegiatan,
                                            SUM (real_peg) AS real_peg,
                                            SUM (real_brng) AS real_brng,
                                            SUM (real_bng) AS real_bng,
                                            SUM (real_mod) AS real_mod,
                                            SUM (real_hibah) AS real_hibah,
                                            SUM (real_bansos) AS real_bansos,
                                            SUM (real_bghasil) AS real_bghasil,
                                            SUM (real_bankeu) AS real_bankeu,
                                            SUM (real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    b.kd_sub_kegiatan,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN ('5101')
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_peg,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5102'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_brng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5103'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bng,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) IN (
                                                            '5201', '5202', '5203', '5204', '5205', '5206'
                                                        ) THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_mod,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5105'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_hibah,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5106'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bansos,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5401'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bghasil,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5402'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_bankeu,
                                                    SUM (
                                                        CASE WHEN LEFT (b.map_real, 4) = '5301'
                                                        THEN (debet - kredit) ELSE 0 END
                                                    ) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                                                WHERE LEFT (b.map_real, 4) IN (
                                                        '5101', '5102', '5103', '5201', '5202', '5203',
                                                        '5204', '5205', '5206', '5105', '5106', '5401', '5402'
                                                    ) $periode_clause
                                                    AND YEAR (a.tgl_voucher) ='$current_year'
                                                GROUP BY a.kd_skpd, b.kd_sub_kegiatan
                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd,
                                                    trhspp.kd_sub_kegiatan,
                                                    0 AS real_peg,
                                                    0 AS real_brng,
                                                    0 AS real_bng,
                                                    0 AS real_mod,
                                                    0 AS real_hibah,
                                                    0 AS real_bansos,
                                                    0 AS real_bghasil,
                                                    0 AS real_bankeu,
                                                    SUM (trdspp.nilai) AS real_btt
                                                FROM trhsp2d
                                                JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                                                JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE ( trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                                                    AND ( trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1' )
                                                    AND LEFT (trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR (tgl_sp2d) ='$current_year'
                                                GROUP BY trhsp2d.kd_skpd, trhspp.kd_sub_kegiatan
                                            ) a GROUP BY kd_skpd, kd_sub_kegiatan
                                    ) b ON a.kd_skpd = b.kd_skpd AND a.kd_sub_kegiatan = b.kd_sub_kegiatan
                        )a where len(kd_skpd)<='22' ORDER BY kd_skpd,kd_sub_kegiatan"
                );
            } else {
                $data = DB::select(
                    "SELECT
                        kd_skpd, kd_sub_kegiatan AS kode ,nm_rek, ang_peg,
                        ang_brng, ang_bng, ang_mod, ang_hibah, ang_bansos,
                        ang_bghasil, ang_bankeu, ang_btt, real_peg, real_brng,
                        real_bng, real_mod, real_hibah, real_bansos, real_bghasil, real_bankeu, real_btt
                    FROM
                        (
                            SELECT
                                a.kd_skpd, a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,1) AS kd_skpd,
                                        LEFT(a.kd_skpd,1) AS kd_sub_kegiatan,
                                        b.nm_urusan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN ('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_urusan b ON LEFT(a.kd_skpd, 1)=b.kd_urusan
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  LEFT(a.kd_skpd,1), b.nm_urusan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,1) AS kd_skpd,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  LEFT(a.kd_skpd,1)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL --1 urusan

                            SELECT
                                a.kd_skpd, a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,4) AS kd_skpd,
                                        LEFT(a.kd_skpd,4) AS kd_sub_kegiatan,
                                        b.nm_bidang_urusan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_bidang_urusan b ON LEFT(a.kd_skpd, 4)=b.kd_bidang_urusan
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  LEFT(a.kd_skpd,4), b.nm_bidang_urusan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,4) AS kd_skpd,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                            GROUP BY  LEFT(a.kd_skpd,4)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL -- 2 bidang urusan

                            SELECT
                                a.kd_skpd,
                                LEFT(a.kd_skpd,4)+'.'+a.kd_sub_kegiatan AS kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,17) AS kd_skpd,
                                        LEFT(a.kd_skpd,17) AS kd_sub_kegiatan,
                                        b.nm_org AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_organisasi b ON LEFT(a.kd_skpd, 17) = b.kd_org
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY LEFT(a.kd_skpd,17), b.nm_org
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,17) AS kd_skpd,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  LEFT(a.kd_skpd,17)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL -- 3 org

                            SELECT
                                a.kd_skpd,
                                LEFT(a.kd_skpd,4)+'.'+a.kd_sub_kegiatan AS kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd,
                                        a.kd_skpd AS kd_sub_kegiatan,
                                        b.nm_skpd AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_skpd b ON a.kd_skpd=b.kd_skpd
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103','5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, b.nm_skpd
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            a.kd_skpd,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103','5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  a.kd_skpd
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL --4 unit

                            SELECT
                                a.kd_skpd+'.1',
                                a.kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd,
                                        LEFT(a.kd_sub_Kegiatan,7) AS kd_sub_kegiatan,
                                        b.nm_program AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang=b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103','5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, LEFT(a.kd_sub_Kegiatan,7), b.nm_program
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            a.kd_skpd, LEFT(b.kd_sub_Kegiatan,7) AS kd_sub_kegiatan,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  a.kd_skpd, LEFT(b.kd_sub_Kegiatan,7)
                                    )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan

                            UNION ALL -- 5 program

                            SELECT
                                a.kd_skpd+'.1', a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd,
                                        LEFT(a.kd_sub_Kegiatan,12) AS kd_sub_kegiatan,
                                        b.nm_program AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang=b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102',  '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, LEFT(a.kd_sub_Kegiatan,12), b.nm_program
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            a.kd_skpd, LEFT(b.kd_sub_Kegiatan,12) AS kd_sub_kegiatan,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  a.kd_skpd, LEFT(b.kd_sub_Kegiatan,12)
                                    )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan

                            UNION ALL -- 6 kegiatan

                            SELECT
                                a.kd_skpd+'.1', a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd,
                                        a.kd_sub_kegiatan, b.nm_kegiatan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang=b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, a.kd_sub_kegiatan, b.nm_kegiatan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            a.kd_skpd, b.kd_sub_kegiatan,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  a.kd_skpd, b.kd_sub_kegiatan
                                    )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan

                        )a where len(kd_skpd)<='22' ORDER BY kd_skpd,kd_sub_kegiatan"
                );
            }
        } else {
            if ($btt == 1) {
                $data = DB::select(
                    "SELECT
                        kd_skpd, kd_sub_kegiatan AS kode, nm_rek, ang_peg, ang_brng,
                        ang_bng, ang_mod, ang_hibah, ang_bansos, ang_bghasil, ang_bankeu,
                        ang_btt, real_peg, real_brng, real_bng, real_mod, real_hibah, real_bansos, real_bghasil, real_bankeu, real_btt
                    FROM
                        (
                            SELECT
                                a.kd_skpd, a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,1) AS kd_skpd,
                                        LEFT(a.kd_skpd,1) AS kd_sub_kegiatan,
                                        b.nm_urusan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a INNER JOIN ms_urusan b ON LEFT(a.kd_skpd, 1)=b.kd_urusan
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  LEFT(a.kd_skpd,1), b.nm_urusan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,1) AS kd_skpd,
                                            SUM(real_peg) AS real_peg,
                                            SUM(real_brng) AS real_brng,
                                            SUM(real_bng) AS real_bng,
                                            SUM(real_mod) AS real_mod,
                                            SUM(real_hibah) AS real_hibah,
                                            SUM(real_bansos) AS real_bansos,
                                            SUM(real_bghasil) AS real_bghasil,
                                            SUM(real_bankeu) AS real_bankeu,
                                            SUM(real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                                WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103','5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402')
                                                    $periode_clause
                                                    AND YEAR(a.tgl_voucher)='$current_year'
                                                GROUP BY  a.kd_skpd

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd, 0 AS real_peg, 0 AS real_brng,
                                                    0 AS real_bng, 0 AS real_mod, 0 AS real_hibah,
                                                    0 AS real_bansos, 0 AS real_bghasil, 0 AS real_bankeu,
                                                    SUM(trdspp.nilai) AS real_btt
                                                FROM trhsp2d JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE (trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                                                    AND (trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                                                    AND LEFT(trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR(tgl_sp2d)='$current_year'
                                                GROUP BY  trhsp2d.kd_skpd
                                            ) a GROUP BY  LEFT(a.kd_skpd,1)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL --1 urusan

                            SELECT
                                a.kd_skpd, a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,4) AS kd_skpd,
                                        LEFT(a.kd_skpd,4) AS kd_sub_kegiatan,
                                        b.nm_bidang_urusan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_bidang_urusan b ON LEFT(a.kd_skpd, 4)=b.kd_bidang_urusan
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  LEFT(a.kd_skpd,4), b.nm_bidang_urusan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,4) AS kd_skpd,
                                            SUM(real_peg) AS real_peg,
                                            SUM(real_brng) AS real_brng,
                                            SUM(real_bng) AS real_bng,
                                            SUM(real_mod) AS real_mod,
                                            SUM(real_hibah) AS real_hibah,
                                            SUM(real_bansos) AS real_bansos,
                                            SUM(real_bghasil) AS real_bghasil,
                                            SUM(real_bankeu) AS real_bankeu,
                                            SUM(real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                                WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402')
                                                    $periode_clause
                                                    AND YEAR(a.tgl_voucher)='$current_year'
                                                GROUP BY  a.kd_skpd

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd, 0 AS real_peg,
                                                    0 AS real_brng, 0 AS real_bng,
                                                    0 AS real_mod, 0 AS real_hibah,
                                                    0 AS real_bansos, 0 AS real_bghasil,
                                                    0 AS real_bankeu, SUM(trdspp.nilai) AS real_btt
                                                FROM trhsp2d JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE (trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                                                    AND (trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                                                    AND LEFT(trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR(tgl_sp2d)='$current_year'
                                                GROUP BY  trhsp2d.kd_skpd
                                            ) a
                                        GROUP BY  LEFT(a.kd_skpd,4)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL -- 2 bidang urusan

                            SELECT
                                a.kd_skpd,
                                LEFT(a.kd_skpd,4)+'.'+a.kd_sub_kegiatan AS kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,17) AS kd_skpd,
                                        LEFT(a.kd_skpd,17) AS kd_sub_kegiatan,
                                        b.nm_org AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_organisasi b ON LEFT(a.kd_skpd, 17)=b.kd_org
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  LEFT(a.kd_skpd,17), b.nm_org
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,17) AS kd_skpd,
                                            SUM(real_peg) AS real_peg,
                                            SUM(real_brng) AS real_brng,
                                            SUM(real_bng) AS real_bng,
                                            SUM(real_mod) AS real_mod,
                                            SUM(real_hibah) AS real_hibah,
                                            SUM(real_bansos) AS real_bansos,
                                            SUM(real_bghasil) AS real_bghasil,
                                            SUM(real_bankeu) AS real_bankeu,
                                            SUM(real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                                FROM trhju_pkd a
                                                INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                                WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402')
                                                    $periode_clause
                                                    AND YEAR(a.tgl_voucher)='$current_year'
                                                GROUP BY  a.kd_skpd

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd, 0 AS real_peg, 0 AS real_brng,
                                                    0 AS real_bng, 0 AS real_mod, 0 AS real_hibah,
                                                    0 AS real_bansos, 0 AS real_bghasil, 0 AS real_bankeu, SUM(trdspp.nilai) AS real_btt
                                                FROM trhsp2d JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE (trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                                                    AND (trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                                                    AND LEFT(trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR(tgl_sp2d)='$current_year'
                                                GROUP BY  trhsp2d.kd_skpd

                                            ) a GROUP BY  LEFT(a.kd_skpd,17)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL -- 3 org

                            SELECT
                                a.kd_skpd,
                                LEFT(a.kd_skpd,4)+'.'+a.kd_sub_kegiatan AS kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd, a.kd_skpd AS kd_sub_kegiatan, b.nm_skpd AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a INNER JOIN ms_skpd b ON a.kd_skpd=b.kd_skpd
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, b.nm_skpd
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            kd_skpd,
                                            SUM(real_peg) AS real_peg,
                                            SUM(real_brng) AS real_brng,
                                            SUM(real_bng) AS real_bng,
                                            SUM(real_mod) AS real_mod,
                                            SUM(real_hibah) AS real_hibah,
                                            SUM(real_bansos) AS real_bansos,
                                            SUM(real_bghasil) AS real_bghasil,
                                            SUM(real_bankeu) AS real_bankeu,
                                            SUM(real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd,
                                                    SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                                FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                                WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402')
                                                    $periode_clause
                                                    AND YEAR(a.tgl_voucher)='$current_year'
                                                GROUP BY  a.kd_skpd

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd, 0 AS real_peg, 0 AS real_brng,
                                                    0 AS real_bng, 0 AS real_mod, 0 AS real_hibah,
                                                    0 AS real_bansos, 0 AS real_bghasil, 0 AS real_bankeu, SUM(trdspp.nilai) AS real_btt
                                                FROM trhsp2d JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE (trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                                                    AND (trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                                                    AND LEFT(trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR(tgl_sp2d)='$current_year'
                                                GROUP BY  trhsp2d.kd_skpd
                                            ) a GROUP BY  a.kd_skpd
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL --4 unit

                            SELECT
                                a.kd_skpd+'.1', a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd, LEFT(a.kd_sub_Kegiatan,7) AS kd_sub_kegiatan, b.nm_program AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang = b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, LEFT(a.kd_sub_Kegiatan,7), b.nm_program
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            kd_skpd, kd_sub_kegiatan,
                                            SUM(real_peg) AS real_peg,
                                            SUM(real_brng) AS real_brng,
                                            SUM(real_bng) AS real_bng,
                                            SUM(real_mod) AS real_mod,
                                            SUM(real_hibah) AS real_hibah,
                                            SUM(real_bansos) AS real_bansos,
                                            SUM(real_bghasil) AS real_bghasil,
                                            SUM(real_bankeu) AS real_bankeu,
                                            SUM(real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd, LEFT(b.kd_sub_Kegiatan,7) AS kd_sub_kegiatan,
                                                    SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                                FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                                WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402')
                                                    $periode_clause
                                                    AND YEAR(a.tgl_voucher)='$current_year'
                                                GROUP BY  a.kd_skpd, LEFT(b.kd_sub_Kegiatan,7)

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd, LEFT(trhspp.kd_sub_kegiatan,7) AS kd_sub_kegiatan,
                                                    0 AS real_peg, 0 AS real_brng, 0 AS real_bng, 0 AS real_mod,
                                                    0 AS real_hibah, 0 AS real_bansos, 0 AS real_bghasil,
                                                    0 AS real_bankeu, SUM(trdspp.nilai) AS real_btt
                                                FROM trhsp2d JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE (trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                                                    AND (trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                                                    AND LEFT(trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR(tgl_sp2d)='$current_year'
                                                GROUP BY  trhsp2d.kd_skpd, LEFT(trhspp.kd_sub_kegiatan,7)

                                            ) a GROUP BY  kd_skpd, kd_sub_kegiatan
                                    )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan

                            UNION ALL -- 5 program

                            SELECT
                                a.kd_skpd+'.1', a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd, LEFT(a.kd_sub_Kegiatan,12) AS kd_sub_kegiatan,
                                        b.nm_program AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang = b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, LEFT(a.kd_sub_Kegiatan,12), b.nm_program
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            kd_skpd, kd_sub_kegiatan,
                                            SUM(real_peg) AS real_peg,
                                            SUM(real_brng) AS real_brng,
                                            SUM(real_bng) AS real_bng,
                                            SUM(real_mod) AS real_mod,
                                            SUM(real_hibah) AS real_hibah,
                                            SUM(real_bansos) AS real_bansos,
                                            SUM(real_bghasil) AS real_bghasil,
                                            SUM(real_bankeu) AS real_bankeu,
                                            SUM(real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd, LEFT(b.kd_sub_Kegiatan,12) AS kd_sub_kegiatan,
                                                    SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                                FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                                WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402')
                                                    $periode_clause
                                                    AND YEAR(a.tgl_voucher)='$current_year'
                                                GROUP BY  a.kd_skpd, LEFT(b.kd_sub_Kegiatan,12)

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd, LEFT(trhspp.kd_sub_kegiatan,12) AS kd_sub_kegiatan, 0 AS real_peg,
                                                    0 AS real_brng, 0 AS real_bng, 0 AS real_mod, 0 AS real_hibah, 0 AS real_bansos,
                                                    0 AS real_bghasil, 0 AS real_bankeu, SUM(trdspp.nilai) AS real_btt
                                                FROM trhsp2d JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE (trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                                                    AND (trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                                                    AND LEFT(trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR(tgl_sp2d)='$current_year'
                                                GROUP BY  trhsp2d.kd_skpd, LEFT(trhspp.kd_sub_kegiatan,12)

                                            ) a GROUP BY  kd_skpd, kd_sub_kegiatan
                                    )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan

                            UNION ALL -- 6 kegiatan

                            SELECT
                                a.kd_skpd+'.1', a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd, a.kd_sub_kegiatan, b.nm_kegiatan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang = b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, a.kd_sub_kegiatan, b.nm_kegiatan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            kd_skpd, kd_sub_kegiatan,
                                            SUM(real_peg) AS real_peg,
                                            SUM(real_brng) AS real_brng,
                                            SUM(real_bng) AS real_bng,
                                            SUM(real_mod) AS real_mod,
                                            SUM(real_hibah) AS real_hibah,
                                            SUM(real_bansos) AS real_bansos,
                                            SUM(real_bghasil) AS real_bghasil,
                                            SUM(real_bankeu) AS real_bankeu,
                                            SUM(real_btt) AS real_btt
                                        FROM
                                            (
                                                SELECT
                                                    a.kd_skpd, b.kd_sub_kegiatan,
                                                    SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                                    SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                                FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                                WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402')
                                                    $periode_clause
                                                    AND YEAR(a.tgl_voucher)='$current_year'
                                                GROUP BY  a.kd_skpd, b.kd_sub_kegiatan

                                                UNION ALL

                                                SELECT
                                                    trhsp2d.kd_skpd, trhspp.kd_sub_kegiatan, 0 AS real_peg,
                                                    0 AS real_brng, 0 AS real_bng, 0 AS real_mod, 0 AS real_hibah,
                                                    0 AS real_bansos, 0 AS real_bghasil, 0 AS real_bankeu, SUM(trdspp.nilai) AS real_btt
                                                FROM trhsp2d JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                                                WHERE (trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                                                    AND (trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                                                    AND LEFT(trdspp.kd_rek6, 4) = '5301'
                                                    $periode_clause1
                                                    AND YEAR(tgl_sp2d)='$current_year'
                                                GROUP BY  trhsp2d.kd_skpd, trhspp.kd_sub_kegiatan
                                            )a GROUP BY  kd_skpd, kd_sub_kegiatan
                                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan
                        )a  ORDER BY kd_skpd,kd_sub_kegiatan"
                );
            } else {
                $data = DB::select(
                    "SELECT
                        kd_skpd, kd_sub_kegiatan AS kode, nm_rek, ang_peg,
                        ang_brng, ang_bng, ang_mod, ang_hibah, ang_bansos, ang_bghasil,
                        ang_bankeu, ang_btt, real_peg, real_brng, real_bng, real_mod,
                        real_hibah, real_bansos, real_bghasil, real_bankeu, real_btt
                    FROM
                        (
                            SELECT
                                a.kd_skpd, a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,1) AS kd_skpd, LEFT(a.kd_skpd,1) AS kd_sub_kegiatan,
                                        b.nm_urusan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_urusan b ON LEFT(a.kd_skpd, 1)=b.kd_urusan
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  LEFT(a.kd_skpd,1), b.nm_urusan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,1) AS kd_skpd,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  LEFT(a.kd_skpd,1)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL --1 urusan

                            SELECT
                                a.kd_skpd, a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,4) AS kd_skpd, LEFT(a.kd_skpd,4) AS kd_sub_kegiatan,
                                        b.nm_bidang_urusan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_bidang_urusan b ON LEFT(a.kd_skpd, 4)=b.kd_bidang_urusan
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  LEFT(a.kd_skpd,4), b.nm_bidang_urusan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,4) AS kd_skpd,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  LEFT(a.kd_skpd,4)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL -- 2 bidang urusan

                            SELECT
                                a.kd_skpd, LEFT(a.kd_skpd,4)+'.'+a.kd_sub_kegiatan AS kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        LEFT(a.kd_skpd,17) AS kd_skpd, LEFT(a.kd_skpd,17) AS kd_sub_kegiatan,
                                        b.nm_org AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_organisasi b ON LEFT(a.kd_skpd, 17)=b.kd_org
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  LEFT(a.kd_skpd,17), b.nm_org
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            LEFT(a.kd_skpd,17) AS kd_skpd,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  LEFT(a.kd_skpd,17)
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL -- 3 org

                            SELECT
                                a.kd_skpd, LEFT(a.kd_skpd,4)+'.'+a.kd_sub_kegiatan AS kd_sub_kegiatan,
                                nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd, a.kd_skpd AS kd_sub_kegiatan, b.nm_skpd AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN ms_skpd b ON a.kd_skpd=b.kd_skpd
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, b.nm_skpd
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            a.kd_skpd,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a
                                        INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  a.kd_skpd
                                    )b ON a.kd_skpd=b.kd_skpd

                            UNION ALL --4 unit

                            SELECT
                                a.kd_skpd+'.1', a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd, LEFT(a.kd_sub_Kegiatan,7) AS kd_sub_kegiatan,
                                        b.nm_program AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a
                                    INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang = b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, LEFT(a.kd_sub_Kegiatan,7), b.nm_program
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            a.kd_skpd, LEFT(b.kd_sub_Kegiatan,7) AS kd_sub_kegiatan,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  a.kd_skpd, LEFT(b.kd_sub_Kegiatan,7)
                                    )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan

                            UNION ALL -- 5 program

                            SELECT
                                a.kd_skpd+'.1', a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd, LEFT(a.kd_sub_Kegiatan,12) AS kd_sub_kegiatan,
                                        b.nm_program AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang = b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, LEFT(a.kd_sub_Kegiatan,12), b.nm_program
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            a.kd_skpd, LEFT(b.kd_sub_Kegiatan,12) AS kd_sub_kegiatan,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  a.kd_skpd, LEFT(b.kd_sub_Kegiatan,12)
                                    )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan

                            UNION ALL -- 6 kegiatan

                            SELECT
                                a.kd_skpd+'.1', a.kd_sub_kegiatan, nm_rek,
                                ISNULL(ang_peg,0) AS ang_peg,
                                ISNULL(ang_brng,0) AS ang_brng,
                                ISNULL(ang_bng,0) AS ang_bng,
                                ISNULL(ang_mod,0) AS ang_mod,
                                ISNULL(ang_hibah,0) AS ang_hibah,
                                ISNULL(ang_bansos,0) AS ang_bansos,
                                ISNULL(ang_bghasil,0) AS ang_bghasil,
                                ISNULL(ang_bankeu,0) AS ang_bankeu,
                                ISNULL(ang_btt,0) AS ang_btt,
                                ISNULL(real_peg,0) AS real_peg,
                                ISNULL(real_brng,0) AS real_brng,
                                ISNULL(real_bng,0) AS real_bng,
                                ISNULL(real_mod,0) AS real_mod,
                                ISNULL(real_hibah,0) AS real_hibah,
                                ISNULL(real_bansos,0) AS real_bansos,
                                ISNULL(real_bghasil,0) AS real_bghasil,
                                ISNULL(real_bankeu,0) AS real_bankeu,
                                ISNULL(real_btt,0) AS real_btt
                            FROM
                                (
                                    SELECT
                                        a.kd_skpd, a.kd_sub_kegiatan, b.nm_kegiatan AS nm_rek,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN('5101') THEN nilai ELSE 0 END) AS ang_peg,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5102' THEN nilai ELSE 0 END) AS ang_brng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5103' THEN nilai ELSE 0 END) AS ang_bng,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) IN( '5201','5202','5203','5204','5205','5206' ) THEN nilai ELSE 0 END) AS ang_mod,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5105' THEN nilai ELSE 0 END) AS ang_hibah,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5106' THEN nilai ELSE 0 END) AS ang_bansos,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5401' THEN nilai ELSE 0 END) AS ang_bghasil,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5402' THEN nilai ELSE 0 END) AS ang_bankeu,
                                        SUM(CASE WHEN LEFT(kd_rek6,4) = '5301' THEN nilai ELSE 0 END) AS ang_btt
                                    FROM trdrka a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang = b.jns_ang
                                    WHERE LEFT(a.kd_rek6, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301') AND a.jns_ang = '$jns_ang'
                                    GROUP BY  a.kd_skpd, a.kd_sub_kegiatan, b.nm_kegiatan
                                ) a LEFT JOIN
                                    (
                                        SELECT
                                            a.kd_skpd, b.kd_sub_kegiatan,
                                            SUM(CASE WHEN LEFT(b.map_real,4) IN('5101') THEN (debet-kredit) ELSE 0 END) AS real_peg,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5102' THEN (debet-kredit) ELSE 0 END) AS real_brng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5103' THEN (debet-kredit) ELSE 0 END) AS real_bng,
                                            SUM(CASE WHEN LEFT(b.map_real,4)IN('5201','5202','5203','5204','5205','5206') THEN (debet-kredit) ELSE 0 END) AS real_mod,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5105' THEN (debet-kredit) ELSE 0 END) AS real_hibah,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5106' THEN (debet-kredit) ELSE 0 END) AS real_bansos,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5401' THEN (debet-kredit) ELSE 0 END) AS real_bghasil,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5402' THEN (debet-kredit) ELSE 0 END) AS real_bankeu,
                                            SUM(CASE WHEN LEFT(b.map_real,4)='5301' THEN (debet-kredit) ELSE 0 END) AS real_btt
                                        FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                        WHERE LEFT(b.map_real, 4) IN ('5101', '5102', '5103', '5201', '5202', '5203', '5204', '5205', '5206', '5105', '5106', '5401', '5402', '5301')
                                            $periode_clause
                                            AND YEAR(a.tgl_voucher)='$current_year'
                                        GROUP BY  a.kd_skpd, b.kd_sub_kegiatan
                                    )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan

                        )a ORDER BY kd_skpd,kd_sub_kegiatan"
                );
            }

        }

        $view = view('laporan_pemda.lra_urusan.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'tgl_ttd' => $tgl_ttd,
            'ttd' => $ttd,
            'data' => $data,
        ));

        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('legal');
            return $pdf->stream("Laporan_urusan.pdf");
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
