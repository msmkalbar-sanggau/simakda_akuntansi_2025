<?php

namespace App\Http\Controllers\LaporanKeuangan\LRA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class LRAController extends Controller
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
            })->where(['id_role' => Auth::user()->role, 'b.name' => 'all-skpd-lra'])->count(),
            'skpd' => DB::table('ms_skpd')->get(),
            'skpdL' => DB::table('ms_skpd')->where(['kd_skpd' => Auth::user()->kd_skpd])->first(),
            'jenis_anggaran' => DB::table('trhrka as a')->select('a.jns_ang', 'a.tgl_dpa', 'b.nama')
                ->join('tb_status_anggaran as b', function ($join) {
                    $join->on('a.jns_ang', '=', 'b.kode');
                })->distinct()->get(),
        ];
        return view('laporan_keuangan.lra.index')->with($data);
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
                ->where('kd_skpd', $kd_skpd)
                ->whereIn('kode', ['PA', 'KPA'])
                // ->where(['kd_skpd' => $kd_skpd, 'kode' => 'PA'])
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
        $bulan = $request->bulan;
        $jns_ang = $request->jns_ang;
        $label = $request->label;
        $permen = $request->permen;
        $ttdyt = $request->ttd;
        $current_year = tahun_anggaran();
        $jenis = $request->jenis;

        $nm_skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();
        $ttd = DB::table('ms_ttd') ->where(['nip' => $penandatangan])->first();

        if ($label == 1) {
            $header = 'Unaudited';
        } else if ($label == 2) {
            $header = 'Audited';
        } else {
            $header = '';
        }

        $daftar_periode = ["", "JANUARI", "FEBRUARI", "TRIWULAN I", "APRIL", "MEI", "SEMESTER PERTAMA", "JULI", "AGUSTUS", "TRIWULAN III", "OKTOBER", "NOVEMBER", "SEMESTER KEDUA"];
        $periode = $daftar_periode[$bulan];

        $skpd_clause = $kd_skpd ? "AND trdju_pkd.kd_unit = '$kd_skpd'" : '';
		$anggaran_clause = $kd_skpd ? "WHERE kd_skpd = '$kd_skpd' AND jns_ang = '$jns_ang'" : "WHERE jns_ang = '$jns_ang'";

        $data = DB::select(
            "SELECT group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align, SUM(nilai) AS anggaran, SUM(jurnal.realisasi) AS realisasi
                FROM $permen
                LEFT JOIN (
                SELECT * FROM trdrka $anggaran_clause
                ) trdrka ON LEFT(trdrka.kd_rek6, LEN(kd_rek)) = $permen.kd_rek
                LEFT JOIN
                (
                SELECT
                    trdju_pkd.kd_unit,
                    trdju_pkd.kd_sub_kegiatan,
                    trdju_pkd.kd_rek6,
                    CASE
                        WHEN LEFT(trdju_pkd.kd_rek6, 1) = '4' THEN SUM(kredit) - SUM(debet)
                        WHEN LEFT(trdju_pkd.kd_rek6, 1) = '5' THEN SUM(debet) - SUM(kredit)
                        WHEN LEFT(trdju_pkd.kd_rek6, 2) = '61' THEN SUM(kredit) - SUM(debet)
                        WHEN LEFT(trdju_pkd.kd_rek6, 2) = '62' THEN SUM(debet) - SUM(kredit)
                        ELSE 0
                    END AS realisasi
                FROM trhju_pkd
                JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                WHERE left(trdju_pkd.kd_rek6,1) IN ('4', '5', '6') AND MONTH(trhju_pkd.tgl_voucher) <= $bulan AND YEAR(trhju_pkd.tgl_voucher) = $current_year
                $skpd_clause
                GROUP BY trdju_pkd.kd_unit, trdju_pkd.kd_sub_kegiatan, trdju_pkd.kd_rek6
                ) jurnal
                ON trdrka.kd_skpd = jurnal.kd_unit AND trdrka.kd_sub_kegiatan = jurnal.kd_sub_kegiatan AND trdrka.kd_rek6 = jurnal.kd_rek6
                GROUP BY group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align
                ORDER BY group_id, group_name"
        );

        $view = view('laporan_keuangan.lra.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'header' => $header,
            'periode' => $periode,
            'nm_skpd' => $nm_skpd,
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
