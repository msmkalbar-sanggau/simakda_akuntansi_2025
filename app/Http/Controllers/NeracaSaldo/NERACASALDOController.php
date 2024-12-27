<?php

namespace App\Http\Controllers\NeracaSaldo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class NERACASALDOController extends Controller
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
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama')->where(['kode' => 'BUD'])->get(),
        ];
        return view('neraca_saldo.index')->with($data);
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
        $jenis = $request->jenis;

        $skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();
        // if ($rekening == '0') {
        //     $dataRekening = '0 - Perubahan SAL';
        // } else {
        //     $Cekrekening = DB::table('ms_rek1')->where(['kd_rek1' => $rekening])->first();
        //     $dataRekening = $Cekrekening->kd_rek1 .' - '. $Cekrekening->nm_rek1;
        // }
        // if($jns_rekening == '1'){
        //     $dataRek = "(SELECT kd_rek3 from ms_rek3 where kd_rek3 = left(a.kd_rek6, 4)) as kd_rek,
        //         (SELECT nm_rek3 from ms_rek3 where kd_rek3 = left(a.kd_rek6, 4)) as nm_rek,";
        // } else if ($jns_rekening == '2'){
        //     $dataRek = "(SELECT kd_rek4 from ms_rek4 where kd_rek4 = left(a.kd_rek6, 6)) as kd_rek,
        //         (SELECT nm_rek4 from ms_rek4 where kd_rek4 = left(a.kd_rek6, 6)) as nm_rek,";
        // } else{
        //     $dataRek = "(SELECT kd_rek5 from ms_rek5 where kd_rek5 = left(a.kd_rek6, 8)) as kd_rek,
        //         (SELECT nm_rek5 from ms_rek5 where kd_rek5 = left(a.kd_rek6, 8)) as nm_rek,";
        // }


        $dataneracasaldo = DB::select(
            "SELECT DISTINCT
            isnull( kd_rek6, '' ) kd_rek6,
            isnull( nm_rek6, '' ) nm_rek6,
            SUM ( debet ) AS debet,
            SUM ( kredit ) AS kredit
        FROM
            trdju_pkd a
            INNER JOIN trhju_pkd b ON a.no_voucher= b.no_voucher
            AND a.kd_unit= b.kd_skpd
        WHERE
            LEFT ( kd_skpd, 22 ) = LEFT ( ?, 22 )
            AND b.tgl_voucher>= ?
            AND b.tgl_voucher<= ?
        GROUP BY
            kd_rek6,
            nm_rek6
        ORDER BY
            kd_rek6", [$kd_skpd,$tgl_awal,$tgl_akhir]
        );
        //dd($dataneracasaldo);

        $view = view('neraca_saldo.print', array(
            'jenis' => $jenis,
            'skpd' => $skpd,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'dataNeracaSaldo' => $dataneracasaldo,
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
