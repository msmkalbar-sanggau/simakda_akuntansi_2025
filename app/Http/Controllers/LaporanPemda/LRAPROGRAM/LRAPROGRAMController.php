<?php

namespace App\Http\Controllers\LaporanPemda\LRAPROGRAM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class LRAPROGRAMController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'skpd' => DB::table('ms_skpd')->get(),
            'jenis_anggaran' => DB::table('trhrka as a')->select('a.jns_ang', 'a.tgl_dpa', 'b.nama')
                ->join('tb_status_anggaran as b', function ($join) {
                    $join->on('a.jns_ang', '=', 'b.kode');
                })->distinct()->get(),
        ];
        return view('laporan_pemda.lra_program.index')->with($data);
    }

    public function penandatangan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('ms_ttd')
            ->select('nip', 'nama')
            ->where(['kd_skpd' => $kd_skpd, 'kode' => 'PA'])
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
        $kd_skpd = $request->kd_skpd;
        $penandatangan = $request->penandatangan;
        $tgl_ttd = $request->tgl_ttd;
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $bulan = $request->bulan;
        $pilih = $request->kode;
        $jns_ang = $request->jns_ang;
        $ttdyt = $request->ttd;
        $current_year = tahun_anggaran();
        $jenis = $request->jenis;

        $ttd = DB::table('ms_ttd')->where(['nip' => $penandatangan])->first();
        $urutan_skpd = collect(\DB::select("SELECT urutan FROM (SELECT ROW_NUMBER() OVER (ORDER BY kd_skpd ASC) AS urutan, kd_skpd FROM ms_skpd) z where kd_skpd = '$kd_skpd'"))->first();
        $urusan = null;
        $bidang_urusan = null;
        $skpd = null;
        if ($kd_skpd) {
            $skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();
            $bidang_urusan = DB::table('ms_bidang_urusan')->where(['kd_bidang_urusan' => $skpd->kd_urusan])->first();
            $urusan = DB::table('ms_urusan')->where(['kd_urusan' => $bidang_urusan->kd_urusan])->first();
        }

        if ($bulan) {
            $daftar_periode = ["", "JANUARI", "FEBRUARI", "TRIWULAN I", "APRIL", "MEI", "SEMESTER PERTAMA", "JULI", "AGUSTUS", "TRIWULAN III", "OKTOBER", "NOVEMBER", "SEMESTER KEDUA"];
            $sisa_bulan = 12 - $bulan;
            $periode = $daftar_periode[$bulan];
        } else {
            $sisa_bulan = 12 - date('m', strtotime($tgl_akhir));
            $periode = strftime('%d %B %Y', strtotime($tgl_awal)) . ' S.D. ' . strftime('%d %B %Y', strtotime($tgl_akhir));
        }

        $periode_clause1 = $bulan ? "AND MONTH(a.tgl_voucher) < '$bulan'"
        : "AND a.tgl_voucher < '$tgl_awal'";

        $periode_clause2 = $bulan ? "AND MONTH(a.tgl_voucher) = '$bulan'"
            : "AND a.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";

        $periode_clause3 = $bulan ? "AND MONTH(a.tgl_voucher) <= '$bulan'"
            : "AND a.tgl_voucher <= '$tgl_akhir'";

        $periode_clause4 = $bulan ? "AND MONTH(a.tgl_voucher) < '$bulan'"
            : "AND a.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";

        $periode_clause5 = $bulan ? "AND MONTH(a.tgl_voucher) = '$bulan'"
            : "AND a.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";

        $periode_clause6 = $bulan ? "AND MONTH(a.tgl_voucher) <= '$bulan'"
            : "AND a.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";

        $data = DB::select(
            "SELECT
                kd_sub_kegiatan,
                kd_rek,nm_rek,
                SUM(anggaran) AS anggaran,
                SUM(sd_bulan_ini) AS sd_bulan_ini
            FROM
                (
                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        '' kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                a.kd_skpd,
                                a.kd_sub_kegiatan,
                                '' kd_rek,
                                b.nm_kegiatan AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan = b.kd_sub_kegiatan AND a.jns_ang = b.jns_ang
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND a.jns_ang = '$jns_ang'
                            GROUP BY  a.kd_skpd, a.kd_sub_kegiatan, b.nm_kegiatan
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) = '5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY  a.kd_skpd, b.kd_sub_kegiatan
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan = b.kd_sub_kegiatan
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,2) AS kd_rek,
                                b.nm_rek2 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek2 b ON LEFT(a.kd_rek6, 2) = b.kd_rek2
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY  kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,2), b.nm_rek2
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,2) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,2)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0)   AS bulan_lalu,
                        ISNULL(bulan_ini,0)    AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,4) AS kd_rek,
                                b.nm_rek3 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek3 b ON LEFT(a.kd_rek6, 4) = b.kd_rek3
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY  kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,4), b.nm_rek3
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,4) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY  a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,4)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,6) AS kd_rek,
                                b.nm_rek4 nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek4 b ON LEFT(a.kd_rek6, 6) = b.kd_rek4
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY  kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,6), b.nm_rek4
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,6) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,6)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,8) AS kd_rek,
                                b.nm_rek5 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek5 b ON LEFT(a.kd_rek6, 8) = b.kd_rek5
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY  kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,8), b.nm_rek5
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,8) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_skpd = b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,8)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        a.kd_rek,
                        a.nm_rek,
                        SUM(a.anggaran) AS anggaran,
                        SUM(a.bulan_lalu) AS bulan_lalu,
                        SUM(a.bulan_ini) AS bulan_ini,
                        SUM(a.sd_bulan_ini) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                left(kd_skpd,17) AS kd_org,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,12) AS kd_rek,
                                b.nm_rek6 nm_rek,
                                SUM(a.nilai) AS anggaran,
                                0 AS bulan_lalu,
                                0 AS bulan_ini,
                                0 AS sd_bulan_ini
                            FROM trdrka a
                            LEFT JOIN ms_rek6 b ON LEFT(a.kd_rek6, 12) = b.kd_rek6
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY  kd_skpd, left(kd_skpd,17), kd_sub_kegiatan, LEFT(a.kd_rek6,12), b.nm_rek6
                            UNION ALL

                            SELECT
                                a.kd_skpd,
                                LEFT(a.kd_skpd,17) AS kd_org,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,12) AS kd_rek,
                                c.nm_rek6,
                                0 AS anggaran,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit INNER JOIN ms_rek6 c ON b.map_real=c.kd_rek6
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY  a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,12), c.nm_rek6
                        )a GROUP BY  a.kd_skpd, a.kd_sub_kegiatan, a.kd_rek, a.nm_rek

                )a WHERE kd_skpd='$kd_skpd' AND kd_skpd<>'4.02.02.02' AND LEN(kd_rek)<='$pilih' and kd_sub_kegiatan<>'' GROUP BY kd_sub_kegiatan,kd_rek,nm_rek ORDER BY kd_sub_kegiatan,kd_rek"
        );

        $totalpendapatan = collect(\DB::select(
            "SELECT
                SUM(anggaran) AS anggaran,
                SUM(sd_bulan_ini) AS sd_bulan_ini
            FROM
                (
                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        '' AS kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                a.kd_skpd,
                                a.kd_sub_kegiatan,
                                '' AS kd_rek,
                                b.nm_kegiatan AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan and a.jns_ang=b.jns_ang
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND a.jns_ang = '$jns_ang'
                            GROUP BY  a.kd_skpd, a.kd_sub_kegiatan, b.nm_kegiatan
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,2) AS kd_rek,
                                b.nm_rek2 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek2 b ON LEFT(a.kd_rek6, 2)=b.kd_rek2
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY  kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,2), b.nm_rek2
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,2) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,2)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,4) AS kd_rek,
                                b.nm_rek3 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek3 b ON LEFT(a.kd_rek6, 4)=b.kd_rek3
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,4), b.nm_rek3
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,4) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                                FROM trhju_pkd a
                                INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                WHERE LEFT(b.map_real, 1) IN ('4')
                                GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,4)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,6) AS kd_rek,
                                b.nm_rek4 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek4 b ON LEFT(a.kd_rek6, 6)=b.kd_rek4
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,6), b.nm_rek4
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,6) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,6)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,8) AS kd_rek,
                                b.nm_rek5 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek5 b ON LEFT(a.kd_rek6, 8)=b.kd_rek5
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,8), b.nm_rek5
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,8) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,8)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        a.kd_skpd,
                        LEFT(a.kd_skpd,17) AS kd_org,
                        a.kd_sub_kegiatan,
                        a.kd_rek,
                        a.nm_rek,
                        SUM(a.anggaran) AS anggaran,
                        SUM(a.bulan_lalu) AS bulan_lalu,
                        SUM(a.bulan_ini) AS bulan_ini,
                        SUM(a.sd_bulan_ini) AS sd_bulan_ini
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                left(kd_skpd,17) AS kd_org,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,12) AS kd_rek,
                                b.nm_rek6 AS nm_rek,
                                SUM(a.nilai) AS anggaran,
                                0 AS bulan_lalu,
                                0 AS bulan_ini,
                                0 AS sd_bulan_ini
                            FROM trdrka a
                            LEFT JOIN ms_rek6 b ON LEFT(a.kd_rek6, 12)=b.kd_rek6
                            WHERE LEFT(a.kd_rek6, 1) IN ('4') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, left(kd_skpd,17), kd_sub_kegiatan, LEFT(a.kd_rek6,12), b.nm_rek6
                            UNION ALL

                            SELECT
                                a.kd_skpd,
                                LEFT(a.kd_skpd,17) AS kd_org,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,12) AS kd_rek,
                                c.nm_rek6,
                                0 AS anggaran,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause4 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause5 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1)='4' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1)='5' AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause6 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit INNER JOIN ms_rek6 c ON b.map_real=c.kd_rek6
                            WHERE LEFT(b.map_real, 1) IN ('4')
                            GROUP BY  a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,12), c.nm_rek6
                        )a GROUP BY  a.kd_skpd, a.kd_sub_kegiatan, a.kd_rek, a.nm_rek
                )a WHERE kd_skpd='$kd_skpd' AND kd_skpd<>'4.02.02.02' AND LEN(kd_rek)='$pilih'"
        ))->first();

        $nil_ang_pen = $totalpendapatan->anggaran;
        $nil_rea_pen = $totalpendapatan->sd_bulan_ini;
        $sisa_rea_pen = $nil_ang_pen - $nil_rea_pen;

        $totalbelanja = collect(\DB::select(
            "SELECT
                SUM(anggaran) AS anggaran,
                SUM(sd_bulan_ini) AS sd_bulan_ini
            FROM
                (
                    SELECT
                        1 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        left(a.kd_sub_kegiatan,7) AS kd_sub_kegiatan,
                        right(a.kd_sub_kegiatan,1) AS kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' lokasi,
                        '' sumber
                    FROM
                        (
                            SELECT
                                a.kd_skpd,
                                LEFT(a.kd_sub_kegiatan,7) AS kd_sub_kegiatan,
                                '' AS kd_rek,
                                b.nm_program AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND LEFT(a.kd_sub_kegiatan, 15)=left(b.kd_sub_kegiatan, 15) and a.jns_ang = b.jns_ang
                            WHERE LEFT(a.kd_rek6, 1) IN ('5')  AND a.jns_ang = '$jns_ang'
                            GROUP BY a.kd_skpd, LEFT(a.kd_sub_kegiatan,7), b.nm_program
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                LEFT(b.kd_sub_kegiatan,7) AS kd_sub_kegiatan,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                                WHERE LEFT(b.map_real, 1) IN ('5') GROUP BY  a.kd_skpd, LEFT(b.kd_sub_kegiatan,7)
                        )b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 7)=left(b.kd_sub_kegiatan, 7)
                    UNION ALL

                    SELECT
                        2 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        left(a.kd_sub_kegiatan,12) AS kd_sub_kegiatan,
                        right(a.kd_sub_kegiatan,2) AS kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        lokasi,
                        CASE WHEN sumber4='' AND sumber3='' AND sumber2='' THEN sumber1
                            WHEN sumber4='' AND sumber3='' AND sumber2<>'' AND sumber1<>'' THEN sumber1+','+sumber2
                            WHEN sumber4='' AND sumber3<>'' AND sumber2<>'' AND sumber1<>'' THEN sumber1+','+sumber2+','+ sumber3  ELSE sumber1+','+sumber2+','+sumber3+','+sumber4
                        END AS sumber
                    FROM
                        (
                            SELECT
                                a.kd_skpd,
                                left(a.kd_sub_kegiatan,12) AS kd_sub_kegiatan,
                                '' AS kd_rek,
                                b.nm_kegiatan AS nm_rek,
                                (
                                    SELECT top 1 sumber1
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 12)=left(a.kd_sub_kegiatan, 12) AND jns_ang = '$jns_ang'
                                ) sumber1,
                                (
                                    SELECT  top 1 sumber2
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 12)=left(a.kd_sub_kegiatan, 12) AND jns_ang = '$jns_ang'
                                ) sumber2,
                                (
                                    SELECT  top 1 sumber3
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 12)=left(a.kd_sub_kegiatan, 12) AND jns_ang = '$jns_ang'
                                ) sumber3,
                                (
                                    SELECT  top 1 sumber4
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 12)=left(a.kd_sub_kegiatan, 12) AND jns_ang = '$jns_ang'
                                ) sumber4,
                                SUM(a.nilai) AS anggaran,
                                rtrim(ltrim(b.lokasi)) AS lokasi
                            FROM trdrka a INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 15)=left(b.kd_sub_kegiatan, 15) and a.jns_ang = b.jns_ang
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND a.jns_ang = '$jns_ang'
                            GROUP BY  a.kd_skpd, left(a.kd_sub_kegiatan,12), b.nm_kegiatan, b.lokasi
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                left(b.kd_sub_kegiatan,12) AS kd_sub_kegiatan,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5') GROUP BY  a.kd_skpd, left(b.kd_sub_kegiatan,12)
                        )b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 12)=left(b.kd_sub_kegiatan, 12)
                    UNION ALL

                    SELECT
                        3 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        left(a.kd_sub_kegiatan,15) AS kd_sub_kegiatan,
                        right(a.kd_sub_kegiatan,4) AS kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        lokasi,
                        CASE WHEN sumber4='' AND sumber3='' AND sumber2='' THEN sumber1
                            WHEN sumber4='' AND sumber3='' AND sumber2<>'' AND sumber1<>'' THEN sumber1+','+sumber2
                            WHEN sumber4='' AND sumber3<>'' AND sumber2<>'' AND sumber1<>'' THEN sumber1+','+sumber2+','+ sumber3
                            ELSE sumber1+','+sumber2+','+sumber3+','+sumber4
                        END AS sumber
                    FROM
                        (
                            SELECT
                                a.kd_skpd,
                                left(a.kd_sub_kegiatan,15) AS kd_sub_kegiatan,
                                '' AS kd_rek,
                                b.nm_sub_kegiatan AS nm_rek,
                                (
                                    SELECT
                                        top 1 sumber1
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 15)=left(a.kd_sub_kegiatan, 15) AND jns_ang = '$jns_ang'
                                ) sumber1,
                                (
                                    SELECT
                                        top 1 sumber2
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 15)=left(a.kd_sub_kegiatan, 15) AND jns_ang = '$jns_ang'
                                ) sumber2,
                                (
                                    SELECT
                                        top 1 sumber3
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 15)=left(a.kd_sub_kegiatan, 15) AND jns_ang = '$jns_ang'
                                ) sumber3,
                                (
                                    SELECT
                                        top 1 sumber4
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 15)=left(a.kd_sub_kegiatan, 15) AND jns_ang = '$jns_ang'
                                ) sumber4,
                                SUM(a.nilai) AS anggaran,
                                rtrim(ltrim(b.lokasi)) AS lokasi
                            FROM trdrka a
                            INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 15)=left(b.kd_sub_kegiatan, 15) and a.jns_ang = b.jns_ang
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND a.jns_ang = '$jns_ang'
                            GROUP BY a.kd_skpd, left(a.kd_sub_kegiatan,15), b.nm_sub_kegiatan, b.lokasi
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                left(b.kd_sub_kegiatan,15) AS kd_sub_kegiatan,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5') GROUP BY  a.kd_skpd, left(b.kd_sub_kegiatan,15)
                        )b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 15)=left(b.kd_sub_kegiatan, 15)
                    UNION ALL

                    SELECT
                        4 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        a.kd_sub_kegiatan+'.'+kd_rek AS kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' lokasi,
                        '' sumber
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,4) AS kd_rek,
                                b.nm_rek3 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a LEFT JOIN ms_rek3 b ON LEFT(a.kd_rek6, 4)=b.kd_rek3
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,4), b.nm_rek3
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,4) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,4)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        5 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        a.kd_sub_kegiatan+'.'+kd_rek AS kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' AS lokasi,
                        '' AS sumber
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,6) AS kd_rek,
                                b.nm_rek4 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek4 b ON LEFT(a.kd_rek6, 6)=b.kd_rek4
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,6), b.nm_rek4
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,6) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,6)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        6 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        a.kd_sub_kegiatan+'.'+kd_rek AS kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' lokasi,
                        '' sumber
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,8) AS kd_rek,
                                b.nm_rek5 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek5 b ON LEFT(a.kd_rek6, 8)=b.kd_rek5
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,8), b.nm_rek5
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,8) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,8)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        7 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        a.kd_sub_kegiatan+'.'+kd_rek AS kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' AS lokasi,
                        '' AS sumber
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,12) AS kd_rek,
                                b.nm_rek6 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek6 b ON LEFT(a.kd_rek6, 12)=b.kd_rek6
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,12), b.nm_rek6
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,12) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,12)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                )a WHERE kd_skpd ='$kd_skpd' AND kd_skpd<>'4.02.02.02' AND LEN(kd_rek)='$pilih' AND kd_rek NOT LIKE '%.%'"
        ))->first();

        $nil_ang_bel = $totalbelanja->anggaran;
        $nil_rea_bel = $totalbelanja->sd_bulan_ini;
        $sisa_rea_bel = $nil_ang_bel - $nil_rea_bel;

        $databelanja = DB::select(
            "SELECT
                urut,
                kd_sub_kegiatan,
                kd_rek,
                nm_rek,
                SUM(anggaran) AS anggaran,
                SUM(sd_bulan_ini) AS sd_bulan_ini
            FROM
                (
                    SELECT
                        1 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        left(a.kd_sub_kegiatan,7) kd_sub_kegiatan,
                        right(a.kd_sub_kegiatan,1) kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' AS lokasi,
                        '' AS sumber
                    FROM
                        (
                            SELECT
                                a.kd_skpd,
                                LEFT(a.kd_sub_kegiatan,7) AS kd_sub_kegiatan,
                                '' AS kd_rek,
                                b.nm_program AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND LEFT(a.kd_sub_kegiatan, 15)=left(b.kd_sub_kegiatan, 15) and a.jns_ang = b.jns_ang
                            WHERE LEFT(a.kd_rek6, 1) IN ('5')  AND a.jns_ang = '$jns_ang'
                            GROUP BY a.kd_skpd, LEFT(a.kd_sub_kegiatan,7), b.nm_program
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                LEFT(b.kd_sub_kegiatan,7) AS kd_sub_kegiatan,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, LEFT(b.kd_sub_kegiatan,7)
                        )b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 7)=left(b.kd_sub_kegiatan, 7)
                    UNION ALL

                    SELECT
                        2 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        left(a.kd_sub_kegiatan,12) kd_sub_kegiatan,
                        right(a.kd_sub_kegiatan,2) AS kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        lokasi,
                        CASE WHEN sumber4='' AND sumber3='' AND sumber2='' THEN sumber1
                            WHEN sumber4='' AND sumber3='' AND sumber2<>'' AND sumber1<>'' THEN sumber1+','+sumber2
                            WHEN sumber4='' AND sumber3<>'' AND sumber2<>'' AND sumber1<>'' THEN sumber1+','+sumber2+','+ sumber3
                            ELSE sumber1+','+sumber2+','+sumber3+','+sumber4
                        END AS sumber
                    FROM
                        (
                            SELECT
                                a.kd_skpd,
                                left(a.kd_sub_kegiatan,12) AS kd_sub_kegiatan,
                                '' AS kd_rek,
                                b.nm_kegiatan AS nm_rek,
                                (
                                    SELECT
                                        top 1 sumber1
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 12)=left(a.kd_sub_kegiatan, 12) AND jns_ang = '$jns_ang'
                                ) sumber1,
                                (
                                    SELECT
                                        top 1 sumber2
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 12)=left(a.kd_sub_kegiatan, 12) AND jns_ang = '$jns_ang'
                                ) sumber2,
                                (
                                    SELECT
                                        top 1 sumber3
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 12)=left(a.kd_sub_kegiatan, 12) AND jns_ang = '$jns_ang'
                                ) sumber3,
                                (
                                    SELECT
                                    top 1 sumber4
                                    FROM trdrka WHERE left(kd_sub_kegiatan, 12)=left(a.kd_sub_kegiatan, 12) AND jns_ang = '$jns_ang'
                                ) sumber4,
                                SUM(a.nilai) AS anggaran,
                                rtrim(ltrim(b.lokasi)) AS lokasi
                            FROM trdrka a
                            INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 15)=left(b.kd_sub_kegiatan, 15) and a.jns_ang = b.jns_ang
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND a.jns_ang = '$jns_ang'
                            GROUP BY a.kd_skpd, left(a.kd_sub_kegiatan,12), b.nm_kegiatan, b.lokasi
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                left(b.kd_sub_kegiatan,12) AS kd_sub_kegiatan,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, left(b.kd_sub_kegiatan,12)
                        )b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 12)=left(b.kd_sub_kegiatan, 12)
                    UNION ALL

                    SELECT
                        3 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        left(a.kd_sub_kegiatan,15) kd_sub_kegiatan,
                        right(a.kd_sub_kegiatan,4) AS kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        lokasi,
                        CASE WHEN sumber4='' AND sumber3='' AND sumber2='' THEN sumber1
                            WHEN sumber4='' AND sumber3='' AND sumber2<>'' AND sumber1<>'' THEN sumber1+','+sumber2
                            WHEN sumber4='' AND sumber3<>'' AND sumber2<>'' AND sumber1<>'' THEN sumber1+','+sumber2+','+ sumber3
                            ELSE sumber1+','+sumber2+','+sumber3+','+sumber4
                        END AS sumber
                    FROM
                        (
                            SELECT
                                a.kd_skpd,
                                left(a.kd_sub_kegiatan,15) AS kd_sub_kegiatan,
                                '' AS kd_rek,
                                b.nm_sub_kegiatan AS nm_rek,
                                (
                                    SELECT
                                        top 1 sumber1
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 15)=left(a.kd_sub_kegiatan, 15) AND jns_ang = '$jns_ang'
                                ) sumber1,
                                (
                                    SELECT
                                        top 1 sumber2
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 15)=left(a.kd_sub_kegiatan, 15) AND jns_ang = '$jns_ang'
                                ) sumber2,
                                (
                                    SELECT
                                        top 1 sumber3
                                    FROM trdrka
                                    WHERE left(kd_sub_kegiatan, 15)=left(a.kd_sub_kegiatan, 15) AND jns_ang = '$jns_ang'
                                ) sumber3,
                                (
                                    SELECT
                                        top 1 sumber4
                                    FROM trdrka
                                        WHERE left(kd_sub_kegiatan, 15)=left(a.kd_sub_kegiatan, 15) AND jns_ang = '$jns_ang'
                                ) sumber4,
                                SUM(a.nilai) AS anggaran,
                                rtrim(ltrim(b.lokasi)) AS lokasi
                            FROM trdrka a
                            INNER JOIN trskpd b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 15)=left(b.kd_sub_kegiatan, 15) and a.jns_ang = b.jns_ang
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND a.jns_ang = '$jns_ang' GROUP BY  a.kd_skpd, left(a.kd_sub_kegiatan,15), b.nm_sub_kegiatan, b.lokasi
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                left(b.kd_sub_kegiatan,15) AS kd_sub_kegiatan,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, left(b.kd_sub_kegiatan,15)
                        )b ON a.kd_skpd=b.kd_skpd AND left(a.kd_sub_kegiatan, 15)=left(b.kd_sub_kegiatan, 15)
                    UNION ALL

                    SELECT
                        4 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        a.kd_sub_kegiatan+'.'+kd_rek AS kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' AS lokasi,
                        '' AS sumber
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,4) AS kd_rek,
                                b.nm_rek3 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek3 b ON LEFT(a.kd_rek6, 4)=b.kd_rek3
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,4), b.nm_rek3
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,4) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,4)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        5 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        a.kd_sub_kegiatan+'.'+kd_rek AS kd_sub_kegiatan,
                        kd_rek, nm_rek, anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' AS lokasi,
                        '' AS sumber
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,6) AS kd_rek,
                                b.nm_rek4 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek4 b ON LEFT(a.kd_rek6, 6)=b.kd_rek4
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,6), b.nm_rek4
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,6) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5') GROUP BY  a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,6)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        6 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        a.kd_sub_kegiatan+'.'+kd_rek AS kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' AS lokasi,
                        '' AS sumber
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,8) AS kd_rek,
                                b.nm_rek5 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek5 b ON LEFT(a.kd_rek6, 8)=b.kd_rek5
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,8), b.nm_rek5
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,8) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,8)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                    UNION ALL

                    SELECT
                        7 AS urut,
                        a.kd_skpd,
                        CASE WHEN a.kd_skpd='1.20.02.01' THEN '1.20.03'
                            WHEN a.kd_skpd='1.20.01.01' THEN '1.20.04'
                            WHEN a.kd_skpd='4.02.02.02' THEN '0.00.00'
                            ELSE left(a.kd_skpd,17)
                        END kd_org,
                        a.kd_sub_kegiatan+'.'+kd_rek AS kd_sub_kegiatan,
                        kd_rek,
                        nm_rek,
                        anggaran,
                        ISNULL(bulan_lalu,0) AS bulan_lalu,
                        ISNULL(bulan_ini,0) AS bulan_ini,
                        ISNULL(sd_bulan_ini,0) AS sd_bulan_ini,
                        '' AS lokasi,
                        '' AS sumber
                    FROM
                        (
                            SELECT
                                kd_skpd,
                                kd_sub_kegiatan,
                                LEFT(a.kd_rek6,12) AS kd_rek,
                                b.nm_rek6 AS nm_rek,
                                SUM(a.nilai) AS anggaran
                            FROM trdrka a
                            LEFT JOIN ms_rek6 b ON LEFT(a.kd_rek6, 12)=b.kd_rek6
                            WHERE LEFT(a.kd_rek6, 1) IN ('5') AND jns_ang = '$jns_ang'
                            GROUP BY kd_skpd, kd_sub_kegiatan, LEFT(a.kd_rek6,12), b.nm_rek6
                        ) a LEFT JOIN
                        (
                            SELECT
                                a.kd_skpd,
                                b.kd_sub_kegiatan,
                                LEFT(b.map_real,12) AS map_real,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause1 THEN (debet-kredit) ELSE 0 END) AS bulan_lalu,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause2 THEN (debet-kredit) ELSE 0 END) AS bulan_ini,
                                SUM(CASE WHEN LEFT(b.map_real,1) IN('4') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (kredit-debet) WHEN LEFT(b.map_real,1) IN ('5') AND YEAR(a.tgl_voucher) = '$current_year' $periode_clause3 THEN (debet-kredit) ELSE 0 END) AS sd_bulan_ini
                            FROM trhju_pkd a
                            INNER JOIN trdju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_skpd=b.kd_unit
                            WHERE LEFT(b.map_real, 1) IN ('5')
                            GROUP BY a.kd_skpd, b.kd_sub_kegiatan, LEFT(b.map_real,12)
                        )b ON a.kd_skpd=b.kd_skpd AND a.kd_sub_kegiatan=b.kd_sub_kegiatan AND a.kd_rek=b.map_real
                )a WHERE kd_skpd='$kd_skpd' AND kd_skpd<>'4.02.02.02' AND LEN(kd_rek)<='$pilih' AND SUBSTRING(kd_sub_kegiatan,17,2)!='00' AND anggaran<>0 GROUP BY urut,kd_sub_kegiatan,kd_rek,nm_rek ORDER BY kd_sub_kegiatan,kd_rek"
        );

        $view = view('laporan_pemda.lra_program.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'databelanja' => $databelanja,
            'bulan' => $bulan,
            'periode' => $periode,
            'sisa_bulan' => $sisa_bulan,
            'urusan' => $urusan,
            'bidang_urusan' => $bidang_urusan,
            'skpd' => $skpd,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'tgl_ttd' => $tgl_ttd,
            'ttd' => $ttd,
            'ttdyt' => $ttdyt,
            'nil_ang_pen' => $nil_ang_pen,
            'nil_rea_pen' => $nil_rea_pen,
            'sisa_rea_pen' => $sisa_rea_pen,
            'nil_ang_bel' => $nil_ang_bel,
            'nil_rea_bel' => $nil_rea_bel,
            'sisa_rea_bel' => $sisa_rea_bel,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
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
