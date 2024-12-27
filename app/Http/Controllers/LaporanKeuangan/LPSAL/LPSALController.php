<?php

namespace App\Http\Controllers\LaporanKeuangan\LPSAL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LPSALController extends Controller
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
        return view('laporan_keuangan.lpsal.index')->with($data);
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
        $thn_lalu = $thn - 1;

        $modtahun = $thn % 4;

        if ($modtahun == 0) {
			$bulanx = ".31 JANUARI.29 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
		} else {
			$bulanx = ".31 JANUARI.28 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
		}

		$arraybulan = explode(".", $bulanx);

        $ttd = DB::table('ms_ttd')->where(['nip' => $penandatangan])->first();

        $data = DB::table('map_lpsal_permen_77')->get();

        $kas_lalu_thn = collect(\DB::select(
            "SELECT
                ((pendapatan+penerimaan)-(belanja+pengeluaran)) as saldo_awal,
                (silpa_lalu+penerimaan_pembayaran) as saldo_awal_s
            FROM
            (
                SELECT
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '4' THEN a.kredit-a.debet ELSE 0 END ) pendapatan,
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '5' THEN a.debet-a.kredit ELSE 0 END ) belanja,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '61' THEN a.kredit-a.debet ELSE 0 END ) penerimaan,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '62' THEN a.debet-a.kredit ELSE 0 END ) pengeluaran,
                    SUM ( CASE WHEN left(a.kd_rek6, 4) = '6101' THEN a.kredit-a.debet ELSE 0 END ) silpa_lalu,
                    SUM ( CASE WHEN a.kd_rek6 = '610107010001' THEN a.debet ELSE 0 END ) penerimaan_pembayaran
                FROM trdju_pkd a
                INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn_lalu'
            ) saldoAkhirKas"
        ))->first();

        $pengguna_sal = collect(\DB::select(
            "SELECT SUM ( CASE WHEN left(a.kd_rek6, 4) = '6101' THEN a.kredit-a.debet ELSE 0 END )*-1 as nilai
                FROM trdju_pkd a
                INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan'"
        ))->first();

        $pengguna_sal_s = collect(\DB::select(
            "SELECT SUM ( CASE WHEN left(a.kd_rek6, 4) = '6101' THEN a.kredit-a.debet ELSE 0 END )*-1 as nilai
                FROM trdju_pkd a
                INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn_lalu'"
        ))->first();

        $sub_total_3 = $kas_lalu_thn->saldo_awal + $pengguna_sal->nilai;
        $sub_total_3_s = ($kas_lalu_thn->saldo_awal_s + 20001) + $pengguna_sal_s->nilai;

        $silpa_ini = collect(DB::select(
            "SELECT
            SUM ( CASE WHEN left(a.kd_rek6, 1) = '4' THEN a.kredit-a.debet ELSE 0 END ) pendapatan,
            SUM ( CASE WHEN left(a.kd_rek6, 1) = '5' THEN a.debet-a.kredit ELSE 0 END ) belanja,
            SUM ( CASE WHEN left(a.kd_rek6, 2) = '61' THEN a.kredit-a.debet ELSE 0 END ) penerimaan,
            SUM ( CASE WHEN left(a.kd_rek6, 2) = '62' THEN a.debet-a.kredit ELSE 0 END ) pengeluaran
        FROM trdju_pkd a
        INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
        AND a.no_voucher= b.no_voucher
        WHERE year(b.tgl_voucher) = '$thn' and month(b.tgl_voucher) <= '$bulan'"))->first();

        $silpa = ($silpa_ini->pendapatan - $silpa_ini->belanja) + ($silpa_ini->penerimaan - $silpa_ini->pengeluaran);

        $silpa_s = collect(\DB::select(
            "SELECT
                ((pendapatan-belanja)+(penerimaan-pengeluaran)) as nilai
            FROM
            (
                SELECT
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '4' THEN a.kredit-a.debet ELSE 0 END ) pendapatan,
                    SUM ( CASE WHEN left(a.kd_rek6, 1) = '5' THEN a.debet-a.kredit ELSE 0 END ) belanja,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '61' THEN a.kredit-a.debet ELSE 0 END ) penerimaan,
                    SUM ( CASE WHEN left(a.kd_rek6, 2) = '62' THEN a.debet-a.kredit ELSE 0 END ) pengeluaran
                FROM trdju_pkd a
                INNER JOIN trhju_pkd b ON a.kd_unit= b.kd_skpd
                AND a.no_voucher= b.no_voucher
                WHERE year(b.tgl_voucher) = '$thn_lalu'
            ) saldoAkhirKas"
        ))->first();

        $sub_total_4 = $sub_total_3 + $silpa;
        $sub_total_4_s = $sub_total_3_s + $silpa_s->nilai;

        $koreksi_lain = 0;

        $koreksi = ($pengguna_sal->nilai*-1) - $kas_lalu_thn->saldo_awal;
        $koreksi_s = ($pengguna_sal_s->nilai*-1) - $kas_lalu_thn->saldo_awal_s - 20001;

        $saldoAkhir = $sub_total_4 + $koreksi + $koreksi_lain;
        $saldoAkhir_s = $sub_total_4_s + $koreksi_s + $koreksi_lain;

        $view = view('laporan_keuangan.lpsal.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'arraybulan' => $arraybulan,
            'bulan' => $bulan,
            'data' => $data,
            'kas_lalu_thn' => $kas_lalu_thn,
            'kas_lalu_thn_s' => $kas_lalu_thn->saldo_awal_s + 20001,
            'pengguna_sal' => $pengguna_sal,
            'pengguna_sal_s' => $pengguna_sal_s,
            'sub_total_3' => $sub_total_3,
            'sub_total_3_s' => $sub_total_3_s,
            'silpa' => $silpa,
            'silpa_s' => $silpa_s,
            'sub_total_4' => $sub_total_4,
            'sub_total_4_s' => $sub_total_4_s,
            'koreksi' => $koreksi,
            'koreksi_s' => $koreksi_s,
            'saldoAkhir' => $saldoAkhir,
            'saldoAkhir_s' => $saldoAkhir_s,
            'tgl_ttd' => $tgl_ttd,
            'ttd' => $ttd,
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
