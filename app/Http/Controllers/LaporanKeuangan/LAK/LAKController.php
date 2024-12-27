<?php

namespace App\Http\Controllers\LaporanKeuangan\LAK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LAKController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama')->whereIn('kode', ['PPKD', 'BUP'])->get(),
        ];
        return view('laporan_keuangan.lak.index')->with($data);
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
        $jenis = $request->jenis;
        $thn = tahun_anggaran();

        $modtahun = $thn % 4;

        if ($modtahun == 0) {
			$bulanx = ".31 JANUARI.29 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
		} else {
			$bulanx = ".31 JANUARI.28 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
		}

		$arraybulan = explode(".", $bulanx);

        $ttd = DB::table('ms_ttd')->where(['nip' => $penandatangan])->first();

        $data = DB::select(
            "SELECT group_id, group_name, padding, is_bold, right_align,
                SUM ( jurnal.nilai ) AS nilai
            FROM map_lak_permen_77
            LEFT JOIN (
                    SELECT
                        kd_rek6,
                        SUM ( nilai ) AS nilai
                    FROM
                        (
                            SELECT
                                trdju_pkd.kd_rek6,
                                CASE
                                    WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '4'
                                        AND year(trhju_pkd.tgl_voucher) = '$thn' and month(trhju_pkd.tgl_voucher) <= '$bulan' THEN
                                        SUM ( kredit ) - SUM ( debet )
                                    WHEN LEFT ( trdju_pkd.kd_rek6, 1 ) = '5'
                                        AND year(trhju_pkd.tgl_voucher) = '$thn' and month(trhju_pkd.tgl_voucher) <= '$bulan' THEN
                                        SUM ( debet ) - SUM ( kredit )
                                    WHEN LEFT ( trdju_pkd.kd_rek6, 2 ) = '61'
                                        AND year(trhju_pkd.tgl_voucher) = '$thn' and month(trhju_pkd.tgl_voucher) <= '$bulan' THEN
                                        SUM ( kredit ) - SUM ( debet )
                                    WHEN LEFT ( trdju_pkd.kd_rek6, 2 ) = '62'
                                        AND year(trhju_pkd.tgl_voucher) = '$thn' and month(trhju_pkd.tgl_voucher) <= '$bulan' THEN
                                        SUM ( debet ) - SUM ( kredit ) ELSE 0
                                END AS nilai
                            FROM
                                trhju_pkd
                            JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher
                                AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                            WHERE
                                left(trdju_pkd.kd_rek6,1) IN ( '4', '5', '6')
                            GROUP BY
                            trdju_pkd.kd_rek6,trhju_pkd.tgl_voucher
                        ) jurnal
                            GROUP BY kd_rek6
                ) jurnal ON LEFT ( jurnal.kd_rek6, LEN( kd_rek ) ) = map_lak_permen_77.kd_rek
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

        $arusMasukKas = 0;
        $arusKeluarKas = 0;
        $arusMasukKas1 = 0;
        $arusKeluarKas1 = 0;
        $arusMasukKas2 = 0;
        $arusKeluarKas2 = 0;
        $saldoAwalKas = 0;
        foreach ($data as $value) {
            if ($value->group_id >= 3 && $value->group_id <= 18) {
                $arusMasukKas += $value->nilai;
            }
            if ($value->group_id >= 22 && $value->group_id <= 33) {
                $arusKeluarKas += $value->nilai;
            }
            if ($value->group_id >= 39 && $value->group_id <= 47) {
                $arusMasukKas1 += $value->nilai;
            }
            if ($value->group_id >= 51 && $value->group_id <= 59) {
                $arusKeluarKas1 += $value->nilai;
            }
            if ($value->group_id >= 65 && $value->group_id <= 70) {
                $arusMasukKas2 += $value->nilai;
            }
            if ($value->group_id >= 74 && $value->group_id <= 79) {
                $arusKeluarKas2 += $value->nilai;
            }
            if ($value->group_id == 94) {
                $saldoAwalKas += $value->nilai;
            }
        }
        $arusOperasi = $arusMasukKas - $arusKeluarKas;
        $arusInvestasi = $arusMasukKas1 - $arusKeluarKas1;
        $arusPendanaan = $arusMasukKas2 - $arusKeluarKas2;

        $dataPFKMasuk = Collect(\DB::select(
            "SELECT
                SUM(b.nilai) as nilai
            FROM
                trhtrmpot a
            INNER JOIN trdtrmpot b ON a.no_bukti=b.no_bukti AND a.kd_skpd=b.kd_skpd
            WHERE LEFT(b.kd_rek6,3) IN ('210') and month(a.tgl_bukti)<='$bulan' and year(a.tgl_bukti) = '$thn'"
        ))->first();

        $dataPFKkeluar = Collect(\DB::select(
            "SELECT
                SUM(b.nilai) AS nilai
            FROM
                trhstrpot a
            INNER JOIN trdstrpot b ON a.no_bukti= b.no_bukti AND a.kd_skpd= b.kd_skpd
            WHERE LEFT ( b.kd_rek6, 3 ) IN ( '210' ) AND MONTH ( a.tgl_bukti ) <= '$bulan' and year(a.tgl_bukti) = '$thn'"
        ))->first();
        $arusTransitoris = $dataPFKMasuk->nilai - $dataPFKkeluar->nilai;

        $KenaikanPenurunanKas = $arusOperasi + $arusInvestasi + $arusPendanaan + $arusTransitoris;
        $saldoAkhirKas = $KenaikanPenurunanKas + $saldoAwalKas;

        $view = view('laporan_keuangan.lak.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'arraybulan' => $arraybulan,
            'bulan' => $bulan,
            'data' => $data,
            'arusMasukKas' => $arusMasukKas,
            'arusMasukKas1' => $arusMasukKas1,
            'arusMasukKas2' => $arusMasukKas2,
            'arusKeluarKas' => $arusKeluarKas,
            'arusKeluarKas1' => $arusKeluarKas1,
            'arusKeluarKas2' => $arusKeluarKas2,
            'arusOperasi' => $arusOperasi,
            'arusInvestasi' => $arusInvestasi,
            'arusPendanaan' => $arusPendanaan,
            'dataPFKMasuk' => $dataPFKMasuk,
            'dataPFKkeluar' => $dataPFKkeluar,
            'arusTransitoris' => $arusTransitoris,
            'KenaikanPenurunanKas' => $KenaikanPenurunanKas,
            'saldoAkhirKas' => $saldoAkhirKas,
            'tgl_ttd' => $tgl_ttd,
            'ttd' => $ttd,
        ));
        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('portrait')->setPaper('a4');
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
