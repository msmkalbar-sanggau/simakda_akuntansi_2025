<?php

namespace App\Http\Controllers\Jurnal\CetakJurnal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class CetakJurnalController extends Controller
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

        if ($role == 1){
            $skpd1 = DB::table('ms_skpd')->get();
        }else {
            $skpd1 = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->get();
        }

        $data = [
            'skpd' => $skpd1,
        ];
        return view('jurnal.cetak_jurnal.index')->with($data);
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
        $current_year = tahun_anggaran();
        $jenis = $request->jenis;

        $skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();

        $total = collect(\DB::select(
            "SELECT count(*) as tot FROM trdju_pkd a
                LEFT JOIN trhju_pkd b ON a.no_voucher= b.no_voucher and a.kd_unit = b.kd_skpd
			WHERE b.tgl_voucher >= '$tgl_awal' and b.tgl_voucher <= '$tgl_akhir'
                and b.kd_skpd = '$kd_skpd'"
        ))->first();

        $data = DB::select(
            "SELECT
                b.tgl_voucher, a.no_voucher, a.kd_rek6, nm_rek6, a.debet, a.kredit, a.rk
            FROM trdju_pkd a
            JOIN trhju_pkd b on a.no_voucher=b.no_voucher and a.kd_unit = b.kd_skpd
            WHERE b.tgl_voucher >= '$tgl_awal' and b.tgl_voucher <= '$tgl_akhir'
			    and b.kd_skpd = '$kd_skpd'
            ORDER BY b.tgl_voucher, a.no_voucher, a.urut, a.rk, a.kd_rek6"
        );

        $view = view('jurnal.cetak_jurnal.print', array(
            'header1' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'daerah' => DB::table('sclient')->select('daerah')->first(),
            'jenis' => $jenis,
            'skpd' => $skpd,
            'total' => $total,
            'data' => $data,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
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
