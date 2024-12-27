<?php

namespace App\Http\Controllers\LaporanKeuangan\LO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class LOController extends Controller
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
            })->where(['id_role' => Auth::user()->role, 'b.name' => 'all-skpd-lo'])->count(),
            'skpd' => DB::table('ms_skpd')->get(),
            'skpdL' => DB::table('ms_skpd')->where(['kd_skpd' => Auth::user()->kd_skpd])->first(),
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama')->where(['kode' => 'BUD'])->get(),
        ];
        return view('laporan_keuangan.lo.index')->with($data);
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
        $jns_ctk = $request->jns_ctk;
        $penandatangan = $request->penandatangan;
        $tgl_ttd = $request->tgl_ttd;
        $bulan = $request->bulan;
        $jenis = $request->jenis;
        $thn = tahun_anggaran();
        $thn_lalu = $thn - 1;

        $nm_skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();

        $ttd = DB::table('ms_ttd')->where(['nip' => $penandatangan])->first();

        $skpd_clause = $kd_skpd ? "AND a.kd_unit = '$kd_skpd'" : '';
        $skpd_clause1 = $kd_skpd ? "WHERE a.kd_unit = '$kd_skpd'" : '';
        $skpd_clause2 = $kd_skpd ? "AND trdju_pkd.kd_unit = '$kd_skpd'" : '';

        $modtahun = $thn % 4;
        if ($modtahun == 0) {
			$bulanx = ".31 JANUARI.29 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
		} else {
			$bulanx = ".31 JANUARI.28 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
		}
		$arraybulan = explode(".", $bulanx);

        $data = DB::select(
            "SELECT group_id, group_name, padding, is_bold, right_align,
                        SUM ( jurnal.nilai_berjalan ) AS nilai_berjalan,
                        SUM ( jurnal.nilai_lalu ) AS nilai_lalu
                    FROM $jns_ctk
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
                                            AND year(trhju_pkd.tgl_voucher) = '$thn' and month(trhju_pkd.tgl_voucher) <= '$bulan' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '8'
                                            AND year(trhju_pkd.tgl_voucher) = '$thn' and month(trhju_pkd.tgl_voucher) <= '$bulan' THEN
                                            SUM ( debet ) - SUM ( kredit ) ELSE 0
                                    END AS nilai_berjalan,
                                    CASE
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '7'
                                            AND year(trhju_pkd.tgl_voucher) = '$thn_lalu' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '8'
                                            AND year(trhju_pkd.tgl_voucher) = '$thn_lalu' THEN
                                            SUM ( debet ) - SUM ( kredit ) ELSE 0
                                    END AS nilai_lalu
                                        FROM
                                            trhju_pkd
                                        JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher
                                            AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                                        WHERE
                                            left(trdju_pkd.kd_rek6,1) IN ( '7', '8' )
                                            $skpd_clause2
                                        GROUP BY
                                        trdju_pkd.kd_rek6,trhju_pkd.tgl_voucher
                                ) jurnal
                                    GROUP BY kd_rek6
                        ) jurnal ON LEFT ( jurnal.kd_rek6, LEN( kd_rek ) ) = $jns_ctk.kd_rek
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
                        WHEN LEFT ( a.kd_rek6, 2 ) in ('71', '72', '73') AND year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 2 ) in ('81', '82','84') AND year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            $skpd_clause1"
        ))->first();

        $jumlahsddos = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 2 ) in ('71', '72', '73') AND year(b.tgl_voucher) = '$thn_lalu' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 2 ) in ('81', '82', '84') AND year(b.tgl_voucher) = '$thn_lalu' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            $skpd_clause1"
        ))->first();
        $bertambah_berkurang_sddos = $jumlahsddo->nilai - $jumlahsddos->nilai;

        // Surplus/Defisit dari Kegiatan Non Operasional
        $jumlahsdkno = collect(\DB::select(
            "SELECT
                SUM(kredit-debet) as nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 4) IN ('7401','7402', '7403','8301', '8302', '8303') and year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' $skpd_clause"
        ))->first();

        $jumlahsdknos = collect(\DB::select(
            "SELECT
                SUM(kredit-debet) as nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 4) IN ('7401','7402', '7403','8301', '8302', '8303') and year(b.tgl_voucher) = '$thn_lalu' $skpd_clause"
        ))->first();
        $bertambah_berkurang_sdknos = $jumlahsdkno->nilai - $jumlahsdknos->nilai;

        // Surplus Defisit Sebelum Pos Luar Biasa
        $jumlahsdsplb = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 2 ) in ('71', '72', '73', '74') AND year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 2 ) in ('81', '82', '83', '84') AND year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            $skpd_clause1"
        ))->first();

        $jumlahsdsplbs = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 2 ) in ('71', '72', '73', '74') AND year(b.tgl_voucher) = '$thn_lalu' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 2 ) in ('81', '82', '83', '84') AND year(b.tgl_voucher) = '$thn_lalu' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            $skpd_clause1"
        ))->first();
        $bertambah_berkurang_sdsplb = $jumlahsdsplb->nilai - $jumlahsdsplbs->nilai;

        // jumlah Surplus/Defisit dari Kegiatan Non Operasional
        $jumlahjsdkno = collect(\DB::select(
            "SELECT
                SUM(kredit-debet) as nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 4) IN ('7401','7402', '7403','8301', '8302', '8303') and year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' $skpd_clause"
        ))->first();

        $jumlahjsdknos = collect(\DB::select(
            "SELECT
                SUM(kredit-debet) as nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            WHERE LEFT ( a.kd_rek6, 4) IN ('7401','7402', '7403','8301', '8302', '8303') and year(b.tgl_voucher) = '$thn_lalu' $skpd_clause"
        ))->first();
        $bertambah_berkurang_jsdknos = $jumlahjsdkno->nilai - $jumlahjsdknos->nilai;

        // surplus defisit lo
        $jumlahsdl = collect(\DB::select(
            "SELECT
                SUM(
                    CASE
                        WHEN LEFT ( a.kd_rek6, 1 ) = '7' AND year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN kredit - debet
                        WHEN LEFT ( a.kd_rek6, 1 ) = '8' AND year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' THEN (debet - kredit) * -1
                        ELSE 0
                        END
                ) nilai
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            $skpd_clause1"
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
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd
            $skpd_clause1"
        ))->first();
        $bertambah_berkurang_sdl = $jumlahsdl->nilai - $jumlahsdls->nilai;

        $view = view('laporan_keuangan.lo.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'nm_skpd' => $nm_skpd,
            'arraybulan' => $arraybulan,
            'bulan' => $bulan,
            'data' => $data,
            'jumlahsddo' => $jumlahsddo,
            'jumlahsddos' => $jumlahsddos,
            'bertambah_berkurang_sddos' => $bertambah_berkurang_sddos,
            'jumlahsdkno' => $jumlahsdkno,
            'jumlahsdknos' => $jumlahsdknos,
            'bertambah_berkurang_sdknos' => $bertambah_berkurang_sdknos,
            'jumlahsdsplb' => $jumlahsdsplb,
            'jumlahsdsplbs' => $jumlahsdsplbs,
            'bertambah_berkurang_sdsplb' => $bertambah_berkurang_sdsplb,
            'jumlahjsdkno' => $jumlahjsdkno,
            'jumlahjsdknos' => $jumlahjsdknos,
            'bertambah_berkurang_jsdknos' => $bertambah_berkurang_jsdknos,
            'jumlahsdl' => $jumlahsdl,
            'jumlahsdls' => $jumlahsdls,
            'bertambah_berkurang_sdl' => $bertambah_berkurang_sdl,
            'tgl_ttd' => $tgl_ttd,
            'ttd' => $ttd
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
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
