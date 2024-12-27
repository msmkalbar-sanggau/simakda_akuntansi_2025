<?php

namespace App\Http\Controllers\LaporanMonitoring\RekapSP2DSKPD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class REKAPSP2DSKPDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('laporan_monitoring.rekap_sp2d_skpd.index');
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
        $jns_cetak = $request->jns_cetak;
        $jenis = $request->jenis;

        if ($jns_cetak == '1') {
            $data = DB::table('trhsp2d')->select('kd_skpd', 'nm_skpd', 'no_sp2d', 'tgl_sp2d', 'nilai')
                ->where(['status_bud' => '1', 'status_terima' => '0'])
                ->orderBy('kd_skpd')->orderBy('tgl_sp2d')->get();
            $dataHeader = 'REKAP SP2D YANG BELUM DITERIMA SKPD';
        } else {
            $data = DB::table('trhsp2d')->select('kd_skpd', 'nm_skpd', 'no_sp2d', 'tgl_sp2d', 'nilai')
                ->where(['status_terima' => '1', 'status' => '0'])
                ->orderBy('kd_skpd')->orderBy('tgl_sp2d')->get();
            $dataHeader = 'REKAP SP2D YANG BELUM DICAIRKAN SKPD';
        }

        $view = view('laporan_monitoring.rekap_sp2d_skpd.print', array(
            'header' => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            'data' => $data,
            'dataHeader' => $dataHeader,
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
