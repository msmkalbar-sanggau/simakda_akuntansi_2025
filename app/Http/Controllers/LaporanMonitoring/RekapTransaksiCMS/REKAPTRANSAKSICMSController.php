<?php

namespace App\Http\Controllers\LaporanMonitoring\RekapTransaksiCMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class REKAPTRANSAKSICMSController extends Controller
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
        ];
        return view('laporan_monitoring.rekap_transaksi_cms.index')->with($data);
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
        $kd_skpd = $request->kd_skpd;
        $jenis = $request->jenis;
        $status = $request->status;
        $thn = tahun_anggaran();

        if ($status == '1') {
            $statusV = '=';
        } else {
            $statusV = '<>';
        }

        $skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();

        $data = DB::table('trhtransout_cmsbank as a')
            ->join('trdtransout_cmsbank as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_skpd');
            })->whereIn('jns_spp', ['1', '2', '3'])
            ->where(['a.kd_skpd' => $kd_skpd, 'b.kd_skpd' => $kd_skpd])
            ->where('status_validasi', $statusV, '1')
            ->whereMonth('a.tgl_voucher', '=', $bulan)
            ->whereYear('a.tgl_voucher', '=', $thn)->get();

        $view = view('laporan_monitoring.rekap_transaksi_cms.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'jenis' => $jenis,
            'data' => $data,
            'skpd' => $skpd
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
