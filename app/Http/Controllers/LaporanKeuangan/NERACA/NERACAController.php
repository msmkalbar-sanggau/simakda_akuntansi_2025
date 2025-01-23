<?php

namespace App\Http\Controllers\LaporanKeuangan\NERACA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class NERACAController extends Controller
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
            })->where(['id_role' => Auth::user()->role, 'b.name' => 'all-skpd-neraca'])->count(),
            'skpd' => DB::table('ms_skpd')->get(),
            'skpdL' => DB::table('ms_skpd')->where(['kd_skpd' => Auth::user()->kd_skpd])->first(),
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama')->where(['kode' => 'BUD'])->get(),
        ];
        return view('laporan_keuangan.neraca.index')->with($data);
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
        $thn_lalu_1 = $thn_lalu - 1;
		$bulan < 10 ? $xbulan = "0$bulan" : $xbulan = $bulan;

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
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '1'
                                            AND left(trhju_pkd.tgl_voucher,7) <= '$thn-$xbulan' THEN
                                            SUM ( debet ) - SUM ( kredit )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '2'
                                            AND left(trhju_pkd.tgl_voucher,7) <= '$thn-$xbulan' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '3'
                                            AND left(trhju_pkd.tgl_voucher,7) <= '$thn-$xbulan' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        ELSE 0
                                    END AS nilai_berjalan,
                                    CASE
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '1'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn_lalu' THEN
                                            SUM ( debet ) - SUM ( kredit )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '2'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn_lalu' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '3'
                                            AND year(trhju_pkd.tgl_voucher) <= '$thn_lalu' THEN
                                            SUM ( kredit ) - SUM ( debet )
                                        ELSE 0
                                    END AS nilai_lalu
                                        FROM
                                            trhju_pkd
                                        JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher
                                            AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                                        WHERE
                                            left(trdju_pkd.kd_rek6,1) IN ( '1', '2','3' )
                                            $skpd_clause2
                                        GROUP BY
                                        trdju_pkd.kd_rek6, trhju_pkd.tgl_voucher, trdju_pkd.kd_rek6
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

        //get saldo awal tahun seblumnnya
        // Created by elvara (SALDO AWAL 2024 / SALDO AKHIR 2023)
        $pen_bel_lalu = collect(\DB::select(
            "SELECT pendapatan_lo, belanja_lo
                FROM
                    (
                    SELECT
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' THEN a.kredit- a.debet ELSE 0 END ) pendapatan_lo,
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' THEN a.debet- a.kredit ELSE 0 END ) belanja_lo
                FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher and a.kd_unit=b.kd_skpd
                WHERE year(b.tgl_voucher) < '$thn_lalu' $skpd_clause
            ) nilaiAkhir"
        ))->first();

        $real_lalu = collect(\DB::select(
            "SELECT sum(a.debet) as debet, sum(a.kredit) as kredit
                FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher and a.kd_unit=b.kd_skpd
                WHERE a.kd_rek6='310101010001' and b.tabel=1 and reev=0 and year(b.tgl_voucher) < '$thn_lalu' $skpd_clause"
        ))->first();

        $lpe_lalu_1 = collect(\DB::select(
            "SELECT srat, knp, ll
                FROM
                (
                    SELECT
                        SUM( CASE WHEN b.reev = '1' THEN a.kredit-a.debet ELSE 0 END ) srat,
                        SUM( CASE WHEN b.reev = '2' THEN a.kredit-a.debet ELSE 0 END ) knp,
                        SUM( CASE WHEN b.reev = '3' THEN a.kredit-a.debet ELSE 0 END ) ll
                    FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher
                    AND a.kd_unit= b.kd_skpd
                    WHERE left(a.kd_rek6,4) = '3101' and year(b.tgl_voucher) = '$thn_lalu_1' $skpd_clause
                ) nilaiAkhir"
        ))->first();

        $pen_bel_lalu_1 = collect(\DB::select(
            "SELECT pendapatan_lo, belanja_lo
                FROM
                    (
                    SELECT
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' THEN a.kredit- a.debet ELSE 0 END ) pendapatan_lo,
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' THEN a.debet- a.kredit ELSE 0 END ) belanja_lo
                FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn_lalu' $skpd_clause
            ) nilaiAkhir"
        ))->first();

        $nilai_eku_lalu = collect(\DB::select(
            "SELECT sum(kredit-debet) as eku_lalu
                    FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher
                    AND a.kd_unit= b.kd_skpd
                    WHERE left(a.kd_rek6,4) = '3101' and year(b.tgl_voucher) = '$thn_lalu' $skpd_clause"
        ))->first();

        $saldo_awal_s = $real_lalu->kredit - $real_lalu->debet + $pen_bel_lalu->pendapatan_lo - $pen_bel_lalu->belanja_lo + $lpe_lalu_1->srat + $lpe_lalu_1->knp + $lpe_lalu_1->ll;

        $surplus_lalu_1 = $pen_bel_lalu_1->pendapatan_lo - $pen_bel_lalu_1->belanja_lo;

        $saldo_awal_lalu = $saldo_awal_s + $surplus_lalu_1 + $nilai_eku_lalu->eku_lalu ;

        // end get saldo awal tahun sebelumnnya
        // (SALDO AWAL 2024 / SALDO AKHIR 2023)



        // batas tahun berjalan
        $rk_skpd = collect(\DB::select(
            "SELECT
                SUM ( CASE WHEN a.kd_rek6 = '111301010001' AND left(b.tgl_voucher,7) <= '$thn-$xbulan' THEN debet - kredit ELSE 0 END ) rk_skpd_24,
                SUM ( CASE WHEN a.kd_rek6 = '111301010001' AND year(b.tgl_voucher) <= '$thn_lalu' THEN debet - kredit ELSE 0 END ) rk_skpd_23
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd $skpd_clause1"
        ))->first();

        $rk_ppkd = collect(\DB::select(
            "SELECT
                SUM ( CASE WHEN a.kd_rek6 = '310301010001' AND left(b.tgl_voucher,7) <= '$thn-$xbulan' THEN kredit - debet ELSE 0 END ) rk_ppkd_24,
                SUM ( CASE WHEN a.kd_rek6 = '310301010001' AND year(b.tgl_voucher) <= '$thn_lalu' THEN kredit - debet ELSE 0 END ) rk_ppkd_23
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher AND a.kd_unit= b.kd_skpd $skpd_clause1"
        ))->first();

        $saldo_akhir_lalu = $saldo_awal_lalu ;
        $eku_lalu = $saldo_akhir_lalu + $rk_ppkd->rk_ppkd_23 - $rk_skpd->rk_skpd_23;
        $eku_lalu_objek = $saldo_akhir_lalu + $rk_ppkd->rk_ppkd_23 - $rk_skpd->rk_skpd_23;

        // tahun berjalan
        $saldo_awal = $saldo_akhir_lalu;

        $pen_bel = collect(\DB::select(
            "SELECT pendapatan_lo, belanja_lo
                FROM
                    (
                    SELECT
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '7' THEN a.kredit- a.debet ELSE 0 END ) pendapatan_lo,
                        SUM ( CASE WHEN LEFT ( a.kd_rek6, 1 ) = '8' THEN a.debet- a.kredit ELSE 0 END ) belanja_lo
                FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' $skpd_clause
            ) nilaiAkhir"
        ))->first();

        $real = collect(\DB::select(
            "SELECT sum(a.debet) as debet, sum(a.kredit) as kredit
                FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                    AND a.no_voucher= b.no_voucher and a.kd_unit=b.kd_skpd
                WHERE a.kd_rek6='310101010001' and b.tabel=1 and reev=0 and year(b.tgl_voucher) < '$thn' $skpd_clause"
        ))->first();

        $lpe = collect(\DB::select(
            "SELECT srat, knp, ll
                FROM
                (
                    SELECT
                        SUM ( CASE WHEN reev = '1' THEN a.kredit - a.debet ELSE 0 END ) srat,
                        SUM ( CASE WHEN reev = '2' THEN a.kredit - a.debet ELSE 0 END ) knp,
                        SUM ( CASE WHEN reev = '3' THEN a.kredit - a.debet ELSE 0 END ) ll
                    FROM
                    trdju_pkd a
                    INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher
                    AND a.kd_unit= b.kd_skpd
                    WHERE left(a.kd_rek6,4) = '3101' and year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan' $skpd_clause
                ) nilaiAkhir"
        ))->first();

        $surplus = $pen_bel->pendapatan_lo - $pen_bel->belanja_lo;

        $eku = $saldo_awal + $surplus + $lpe->srat + $lpe->knp + $lpe->ll + $rk_ppkd->rk_ppkd_24 - $rk_skpd->rk_skpd_24;

        $eku_objek = $saldo_awal + $surplus + $lpe->srat + $lpe->knp + $lpe->ll + $rk_ppkd->rk_ppkd_24 - $rk_skpd->rk_skpd_24;

        // aset

        $kewajiban = collect(\DB::select(
            "SELECT
                SUM ( CASE WHEN left( a.kd_rek6, 1) = '2' AND left(b.tgl_voucher,7) <= '$thn-$xbulan' THEN kredit - debet ELSE 0 END ) kewajiban_22,
                SUM ( CASE WHEN left( a.kd_rek6, 1) = '2' AND year(b.tgl_voucher) <= '$thn_lalu' THEN kredit - debet ELSE 0 END ) kewajiban_21
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher
            AND a.kd_unit= b.kd_skpd $skpd_clause1"
        ))->first();

        $kewajiban_ekuitas = $eku + $kewajiban->kewajiban_22;
        $kewajiban_ekuitas_lalu = $eku_lalu + $kewajiban->kewajiban_21;
        $nilai_cp = collect(DB::select("SELECT sum(b.rupiah) as nilai_cp from trhkasin_pkd a inner join trdkasin_pkd b on a.kd_skpd = b.kd_skpd and a.no_sts = b.no_sts where a.jns_cp='3' and b.kd_rek6 ='110103010001' and a.kd_skpd='$kd_skpd'"))->first();
        // dd ($nilai_cp);  

        $view = view('laporan_keuangan.neraca.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'nm_skpd' => $nm_skpd,
            'arraybulan' => $arraybulan,
            'bulan' => $bulan,
            'data' => $data,
            'rk_skpd' => $rk_skpd,
            'tgl_ttd' => $tgl_ttd,
            'eku' => $eku,
            'eku_objek' => $eku_objek,
            'eku_lalu' => $eku_lalu,
            'eku_lalu_objek' => $eku_lalu_objek,
            'kewajiban_ekuitas' => $kewajiban_ekuitas,
            'rk_ppkd' => $rk_ppkd,
            'kewajiban_ekuitas_lalu' => $kewajiban_ekuitas_lalu,
            'nilai_cp' => $nilai_cp,
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
