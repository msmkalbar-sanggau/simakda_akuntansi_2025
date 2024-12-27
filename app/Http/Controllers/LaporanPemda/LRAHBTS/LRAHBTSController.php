<?php

namespace App\Http\Controllers\LaporanPemda\LRAHBTS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class LRAHBTSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'cekSKPD' => DB::table('akses_peran1 as a')->join('akses1 as b', function ($join) {
                        $join->on('a.id_akses', '=', 'b.id');
                    })->where(['id_role' => Auth::user()->role, 'b.name' => 'all-skpd-lra-hbts'])->count(),
            'skpd' => DB::table('ms_skpd')->get(),
            'skpdL' => DB::table('ms_skpd')->where(['kd_skpd' => Auth::user()->kd_skpd])->first(),
            'jenis_anggaran' => DB::table('trhrka as a')->select('a.jns_ang', 'a.tgl_dpa', 'b.nama')
                ->join('tb_status_anggaran as b', function ($join) {
                    $join->on('a.jns_ang', '=', 'b.kode');
                })->distinct()->get(),
        ];
        return view('laporan_pemda.lra_hbts.index')->with($data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function penandatangan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        if ($kd_skpd == 'ALL') {
            $data = DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->whereIn('kode', ['PPKD', 'BUP'])
                ->get();
        } else {
            $data = DB::table('ms_ttd')
                ->select('nip', 'nama')
                ->where(['kd_skpd' => $kd_skpd, 'kode' => 'PA'])
                ->get();
        }

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
        $akumulasi = $request->akumulasi;
        $btt = $request->btt;
        $jenis_data = $request->jenis_data;
        $kode = $request->kode;
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

        $anggaran_clause = $kd_skpd ? "WHERE kd_skpd = '$kd_skpd' AND jns_ang = '$jns_ang'" : "WHERE jns_ang = '$jns_ang'";
        if ($akumulasi == 1) {
            $js_akumulasi = '<=';
        } else {
            $js_akumulasi = '=';
        }

        switch ($jenis_data) {
            case 'SPJ':
                $skpd_clause = $kd_skpd ? "AND trdju_pkd.kd_unit = '$kd_skpd'" : '';
                $periode_clause = $bulan ? "AND MONTH(trhju_pkd.tgl_voucher) $js_akumulasi $bulan AND YEAR(trhju_pkd.tgl_voucher) = $current_year"
                    : "AND trhju_pkd.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";
                if ($btt == 1) {
                    $btt_skpd_clause = $kd_skpd ? "AND trhsp2d.kd_skpd = '$kd_skpd'" : '';
                    $btt_periode_clause = $bulan ? "AND MONTH(trhsp2d.tgl_sp2d) $js_akumulasi $bulan AND YEAR(trhsp2d.tgl_sp2d) = $current_year"
                        : "AND trhju_pkd.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";

                    $data = DB::select(
                        "SELECT group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align, SUM(trdrka.nilai) AS anggaran, SUM(jurnal.realisasi) AS realisasi
                            FROM $kode
                            LEFT JOIN (
                            SELECT * FROM trdrka $anggaran_clause
                            ) trdrka ON LEFT(trdrka.kd_rek6, LEN(kd_rek)) = $kode.kd_rek
                            LEFT JOIN
                            (
                            SELECT
                                trdju_pkd.kd_unit,
                                trdju_pkd.kd_sub_kegiatan,
                                trdju_pkd.map_real,
                                CASE
                                    WHEN LEFT(trdju_pkd.map_real, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                    WHEN LEFT(trdju_pkd.map_real, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                    WHEN LEFT(trdju_pkd.map_real, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                    WHEN LEFT(trdju_pkd.map_real, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                    ELSE 0
                                END AS realisasi
                            FROM trhju_pkd
                            JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                            WHERE left(trdju_pkd.map_real,1) IN ('4', '5', '6') $skpd_clause $periode_clause
                            AND kd_rek6 <> '530101010001'
                            GROUP BY trdju_pkd.kd_unit, trdju_pkd.kd_sub_kegiatan, trdju_pkd.map_real

                            UNION ALL

                            SELECT trhsp2d.kd_skpd, trdspp.kd_sub_kegiatan, trdspp.kd_rek6, SUM(trdspp.nilai) AS realisasi
                            FROM trhsp2d
                            JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                            JOIN trhspp ON trhspp.no_spp = trdspp.no_spp
                            WHERE (trhspp.sp2d_batal IS NULL OR trhspp.sp2d_batal <> '1')
                            AND (trhsp2d.sp2d_batal IS NULL OR trhsp2d.sp2d_batal <> '1')
                            AND trdspp.kd_rek6 = '530101010001'
                            $btt_skpd_clause $btt_periode_clause
                            GROUP BY trhsp2d.kd_skpd, trdspp.kd_sub_kegiatan, trdspp.kd_rek6
                            ) jurnal
                            ON trdrka.kd_skpd = jurnal.kd_unit AND trdrka.kd_sub_kegiatan = jurnal.kd_sub_kegiatan
                            AND trdrka.kd_rek6 = jurnal.map_real
                            GROUP BY group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align
                            ORDER BY group_id, group_name"
                    );
                } else {
                    $data = DB::select(
                        "SELECT group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align, SUM(trdrka.nilai) AS anggaran, SUM(jurnal.realisasi) AS realisasi
                            FROM $kode
                            LEFT JOIN (
                                SELECT * FROM trdrka $anggaran_clause
                            ) trdrka ON LEFT(trdrka.kd_rek6, LEN(kd_rek)) = $kode.kd_rek
                            LEFT JOIN
                            (
                                SELECT
                                trdju_pkd.kd_unit,
                                trdju_pkd.kd_sub_kegiatan,
                                trdju_pkd.map_real,
                                CASE
                                    WHEN LEFT(trdju_pkd.map_real, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                    WHEN LEFT(trdju_pkd.map_real, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                    WHEN LEFT(trdju_pkd.map_real, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                    WHEN LEFT(trdju_pkd.map_real, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                    ELSE 0
                                END AS realisasi
                                FROM trhju_pkd
                                JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                                WHERE left(trdju_pkd.map_real,1) IN ('4', '5', '6') $skpd_clause $periode_clause
                                GROUP BY trdju_pkd.kd_unit, trdju_pkd.kd_sub_kegiatan, trdju_pkd.map_real
                            ) jurnal
                            ON trdrka.kd_skpd = jurnal.kd_unit AND trdrka.kd_sub_kegiatan = jurnal.kd_sub_kegiatan
                            AND trdrka.kd_rek6 = jurnal.map_real
                            GROUP BY group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align
                            ORDER BY group_id, group_name"
                    );
                }
                break;

            case 'SP2D Terbit':
                $skpd_clause = $kd_skpd ? "AND trdju_pkd.kd_unit = '$kd_skpd'" : '';
                $sp2d_skpd_clause = $kd_skpd ? "AND trhsp2d.kd_skpd = '$kd_skpd'" : '';
                $periode_clause = $bulan ? "AND MONTH(trhju_pkd.tgl_voucher) $js_akumulasi $bulan AND YEAR(trhju_pkd.tgl_voucher) = $current_year"
                    : "AND trhju_pkd.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";
                $sp2d_periode_clause = $bulan ? "MONTH(trhsp2d.tgl_sp2d) $js_akumulasi $bulan AND YEAR(trhsp2d.tgl_sp2d) = $current_year"
                    : "trhsp2d.tgl_sp2d BETWEEN '$tgl_awal' AND '$tgl_akhir'";

                $data = DB::select(
                    "SELECT group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align, SUM(trdrka.nilai) AS anggaran, SUM(jurnal.realisasi) AS realisasi
                        FROM $kode
                        LEFT JOIN (
                            SELECT * FROM trdrka $anggaran_clause
                        ) trdrka ON LEFT(trdrka.kd_rek6, LEN(kd_rek)) = $kode.kd_rek
                        LEFT JOIN
                        (
                            SELECT
                            trdju_pkd.kd_unit,
                            trdju_pkd.kd_sub_kegiatan,
                            trdju_pkd.map_real,
                            CASE
                                WHEN LEFT(trdju_pkd.map_real, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                WHEN LEFT(trdju_pkd.map_real, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                WHEN LEFT(trdju_pkd.map_real, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                -- WHEN LEFT(trdju_pkd.map_real, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                ELSE 0
                            END AS realisasi
                            FROM trhju_pkd
                            JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                            WHERE left(trdju_pkd.map_real,1) IN ('4', '6') $skpd_clause $periode_clause
                            AND LEFT (trdju_pkd.map_real, 2) <> '62'
                            GROUP BY trdju_pkd.kd_unit, trdju_pkd.kd_sub_kegiatan, trdju_pkd.map_real

                            UNION ALL

                            SELECT trhsp2d.kd_skpd, trdspp.kd_sub_kegiatan, trdspp.kd_rek6, SUM(trdspp.nilai) AS realisasi
                            FROM trhsp2d
                            JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                            WHERE $sp2d_periode_clause $sp2d_skpd_clause
                            GROUP BY trhsp2d.kd_skpd, trdspp.kd_sub_kegiatan, trdspp.kd_rek6
                        ) jurnal
                        ON trdrka.kd_skpd = jurnal.kd_unit AND trdrka.kd_sub_kegiatan = jurnal.kd_sub_kegiatan
                        AND trdrka.kd_rek6 = jurnal.map_real
                        GROUP BY group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align
                        ORDER BY group_id, group_name"
                );
                break;

            case 'SP2D Lunas':
                $skpd_clause = $kd_skpd ? "AND trdju_pkd.kd_unit = '$kd_skpd'" : '';
                $sp2d_skpd_clause = $kd_skpd ? "AND trhsp2d.kd_skpd = '$kd_skpd'" : '';
                $periode_clause = $bulan ? "AND MONTH(trhju_pkd.tgl_voucher) $js_akumulasi $bulan AND YEAR(trhju_pkd.tgl_voucher) = $current_year"
                    : "AND trhju_pkd.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";
                $sp2d_periode_clause = $bulan ? "AND MONTH(trhsp2d.tgl_sp2d) $js_akumulasi $bulan AND YEAR(trhsp2d.tgl_sp2d) = $current_year"
                    : "AND trhsp2d.tgl_sp2d BETWEEN '$tgl_awal' AND '$tgl_akhir'";

                $data = DB::select(
                    "SELECT group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align, SUM(trdrka.nilai) AS anggaran, SUM(jurnal.realisasi) AS realisasi
                        FROM $kode
                        LEFT JOIN (
                            SELECT * FROM trdrka $anggaran_clause
                        ) trdrka ON LEFT(trdrka.kd_rek6, LEN(kd_rek)) = $kode.kd_rek
                        LEFT JOIN
                        (
                            SELECT
                            trdju_pkd.kd_unit,
                            trdju_pkd.kd_sub_kegiatan,
                            trdju_pkd.map_real,
                            CASE
                                WHEN LEFT(trdju_pkd.map_real, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                WHEN LEFT(trdju_pkd.map_real, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                WHEN LEFT(trdju_pkd.map_real, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                -- WHEN LEFT(trdju_pkd.map_real, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                ELSE 0
                            END AS realisasi
                            FROM trhju_pkd
                            JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                            WHERE left(trdju_pkd.map_real,1) IN ('4', '6') $skpd_clause $periode_clause
                            AND LEFT (trdju_pkd.map_real, 2) <> '62'
                            GROUP BY trdju_pkd.kd_unit, trdju_pkd.kd_sub_kegiatan, trdju_pkd.map_real

                            UNION ALL

                            SELECT trhsp2d.kd_skpd, trdspp.kd_sub_kegiatan, trdspp.kd_rek6, SUM(trdspp.nilai) AS realisasi
                            FROM trhsp2d
                            JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                            WHERE trhsp2d.status_bud = '1' $sp2d_periode_clause $sp2d_skpd_clause
                            GROUP BY trhsp2d.kd_skpd, trdspp.kd_sub_kegiatan, trdspp.kd_rek6
                        ) jurnal
                        ON trdrka.kd_skpd = jurnal.kd_unit AND trdrka.kd_sub_kegiatan = jurnal.kd_sub_kegiatan AND trdrka.kd_rek6 = jurnal.map_real
                        GROUP BY group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align
                        ORDER BY group_id, group_name"
                );
                break;

            case 'SP2D Advice':
                $skpd_clause = $kd_skpd ? "AND trdju_pkd.kd_unit = '$kd_skpd'" : '';
                $sp2d_skpd_clause = $kd_skpd ? "AND trhsp2d.kd_skpd = '$kd_skpd'" : '';
                $periode_clause = $bulan ? "AND MONTH(trhju_pkd.tgl_voucher) $js_akumulasi $bulan AND YEAR(trhju_pkd.tgl_voucher) = $current_year"
                    : "AND trhju_pkd.tgl_voucher BETWEEN '$tgl_awal' AND '$tgl_akhir'";
                $sp2d_periode_clause = $bulan ? "AND MONTH(trhsp2d.tgl_sp2d) $js_akumulasi $bulan AND YEAR(trhsp2d.tgl_sp2d) = $current_year"
                    : "AND trhsp2d.tgl_sp2d BETWEEN '$tgl_awal' AND '$tgl_akhir'";

                $data = DB::select(
                    "SELECT group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align, SUM(trdrka.nilai) AS anggaran, SUM(jurnal.realisasi) AS realisasi
                        FROM $kode
                        LEFT JOIN (
                            SELECT * FROM trdrka $anggaran_clause
                        ) trdrka ON LEFT(trdrka.kd_rek6, LEN(kd_rek)) = $kode.kd_rek
                        LEFT JOIN
                        (
                            SELECT
                            trdju_pkd.kd_unit,
                            trdju_pkd.kd_sub_kegiatan,
                            trdju_pkd.map_real,
                            CASE
                                WHEN LEFT(trdju_pkd.map_real, 1) = '4' THEN SUM(kredit) - SUM(debet)
                                WHEN LEFT(trdju_pkd.map_real, 1) = '5' THEN SUM(debet) - SUM(kredit)
                                WHEN LEFT(trdju_pkd.map_real, 2) = '61' THEN SUM(kredit) - SUM(debet)
                                -- WHEN LEFT(trdju_pkd.map_real, 2) = '62' THEN SUM(debet) - SUM(kredit)
                                ELSE 0
                            END AS realisasi
                            FROM trhju_pkd
                            JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                            WHERE left(trdju_pkd.map_real,1) IN ('4') $skpd_clause $periode_clause
                            AND LEFT (trdju_pkd.map_real, 2) <> '62'
                            GROUP BY trdju_pkd.kd_unit, trdju_pkd.kd_sub_kegiatan, trdju_pkd.map_real

                            UNION ALL

                            SELECT trhsp2d.kd_skpd, trdspp.kd_sub_kegiatan, trdspp.kd_rek6, SUM(trdspp.nilai) AS realisasi
                            FROM trhsp2d
                            JOIN trdspp ON trhsp2d.no_spp = trdspp.no_spp
                            WHERE EXISTS (SELECT 1 FROM trhuji JOIN trduji ON trhuji.no_uji = trduji.no_uji WHERE trduji.no_sp2d = trhsp2d.no_sp2d)
                            $sp2d_periode_clause $sp2d_skpd_clause
                            GROUP BY trhsp2d.kd_skpd, trdspp.kd_sub_kegiatan, trdspp.kd_rek6
                        ) jurnal
                        ON trdrka.kd_skpd = jurnal.kd_unit AND trdrka.kd_sub_kegiatan = jurnal.kd_sub_kegiatan AND trdrka.kd_rek6 = jurnal.map_real
                        GROUP BY group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align
                        ORDER BY group_id, group_name"
                );
                break;

            default:
                break;
        }

        $view = view('laporan_pemda.lra_hbts.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'bulan' => $bulan,
            'periode' => $periode,
            'sisa_bulan' => $sisa_bulan,
            'urusan' => $urusan,
            'bidang_urusan' => $bidang_urusan,
            'skpd' => $skpd,
            'akumulasi' => $akumulasi,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'tgl_ttd' => $tgl_ttd,
            'ttd' => $ttd,
            'ttdyt' => $ttdyt,
            'kd_rek_separator' => function ($kd_rek) {
                $result = '';
                if (strlen($kd_rek) > 0) $result .= substr($kd_rek, 0, 1);
                if (strlen($kd_rek) > 1) $result .= '.' . substr($kd_rek, 1, 1);
                if (strlen($kd_rek) > 3) $result .= '.' . substr($kd_rek, 2, 2);
                if (strlen($kd_rek) > 5) $result .= '.' . substr($kd_rek, 4, 2);
                if (strlen($kd_rek) > 7) $result .= '.' . substr($kd_rek, 6, 2);
                if (strlen($kd_rek) > 9) $result .= '.' . substr($kd_rek, 8, 4);
                return $result;
            }
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('portrait')->setPaper('a4');
            if ($skpd == null) {
                return $pdf->stream("Semua_skpd.pdf");
            } else {
                return $pdf->stream("$urutan_skpd->urutan. $skpd->nm_skpd.pdf");
            }
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
