<?php

namespace App\Http\Controllers\BukuBesar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class BUKUBESARController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Auth::user()->role;
        $kd_skpd = Auth::user()->kd_skpd;

        if ($role == 1 || $role == 3 || $role == 7){
            $skpd1 = DB::table('ms_skpd')->get();
        }else {
            $skpd1 = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->get();
        }
        $data = [
            'skpd' => $skpd1,
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama')->where(['kode' => 'BUD'])->get(),
        ];
        return view('buku_besar.index')->with($data);
    }

    public function rekening(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $data = DB::select(
            "SELECT DISTINCT
                isnull( kd_rek6, '' ) kd_rek6,
                isnull( ltrim( nm_rek6 ), '' ) nm_rek6
            FROM trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher = b.no_voucher AND a.kd_unit = b.kd_skpd
            WHERE kd_skpd = ?
            GROUP BY kd_rek6, ltrim( nm_rek6 )
            ORDER BY kd_rek6", [$kd_skpd]
        );

        return response()->json($data);
    }

    public function penandatangan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;

        $data = DB::table('ms_ttd')
            ->select('nip', 'nama')
            ->where(['kd_skpd' => $kd_skpd])
            ->where(['kode' => 'PA'])
            ->orderBy('nama')
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
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $rekening = $request->rekening;
        $penandatangan = $request->penandatangan;
        $current_year = tahun_anggaran();
        $jenis = $request->jenis;

        $skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();
        $ttd = DB::table('ms_ttd')->where(['nip' => $penandatangan])->first();

        if ($rekening == '000000000000') {
            $dataRekening = '000000000000 - Perubahan SAL';
        } else {
            $Cekrekening = DB::table('ms_rek6')->where(['kd_rek6' => $rekening])->first();
            $dataRekening = $Cekrekening->kd_rek6 .' - '. $Cekrekening->nm_rek6;
        }

        if ((substr($rekening, 0, 4) == '1113') or ($rekening == '310101010001') or (substr($rekening, 0, 1) == '7') or (substr($rekening, 0, 1) == '8')) {
            $data = Collect(\DB::select(
                "SELECT
                    sum(a.debet) as debet,
                    sum(a.kredit) as kredit
                FROM trdju_pkd a
                LEFT JOIN trhju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_unit=b.kd_skpd
                WHERE a.kd_rek6 = ? AND b.kd_skpd = ?
                and b.tgl_voucher < ? AND YEAR(b.tgl_voucher) = ?",
                [$rekening, $kd_skpd, $tgl_awal, $current_year]
            ))->first();
        } else {
            $data = Collect(\DB::select(
                "SELECT
                    sum(a.debet) as debet,
                    sum(a.kredit) as kredit
                FROM trdju_pkd a
                LEFT JOIN trhju_pkd b ON a.no_voucher=b.no_voucher AND a.kd_unit=b.kd_skpd
                WHERE a.kd_rek6= ? AND b.kd_skpd = ?
                and b.tgl_voucher < ?", [$rekening, $kd_skpd, $tgl_awal]
            ))->first();
        }

        if ((substr($rekening, 0, 1) == '8') or (substr($rekening, 0, 1) == '5') or (substr($rekening, 0, 2) == '62') or (substr($rekening, 0, 1) == '1')) {
            $saldo_awal = $data->debet - $data->kredit;
        } else {
            $saldo_awal = $data->kredit - $data->debet;
        }

        $dataBukuBesar = DB::select(
            "SELECT
                a.kd_unit, a.kd_rek6, a.debet, a.kredit,
                b.tgl_voucher, b.ket, b.no_voucher
            FROM
                trdju_pkd a
                LEFT JOIN trhju_pkd b ON a.no_voucher= b.no_voucher
                AND a.kd_unit= b.kd_skpd
            WHERE
                a.kd_rek6= ?
                AND b.kd_skpd = ?
                AND b.tgl_voucher >= ?
                AND b.tgl_voucher <= ?
            ORDER BY
                tgl_voucher,
                no_voucher", [$rekening, $kd_skpd, $tgl_awal, $tgl_akhir]
        );

        $view = view('buku_besar.print', array(
            'jenis' => $jenis,
            'skpd' => $skpd,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'ttd' => $ttd,
            'saldo_awal' => $saldo_awal,
            'dataBukuBesar' => $dataBukuBesar,
            'rekening' => $rekening,
            'dataRekening' => $dataRekening,
        ));


        if ($jenis == 'pdf') {
            $pdf = PDF::loadHtml($view)->setOrientation('landscape')->setPaper('a4');
            return $pdf->stream("$skpd->nm_skpd.pdf");
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
