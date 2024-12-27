<?php

namespace App\Http\Controllers\RekapBukuBesar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class REKAPBUKUBESARController extends Controller
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
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama')->where(['kode' => 'BUD'])->get(),
        ];
        return view('rekap_buku_besar.index')->with($data);
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
        $jns_rekening = $request->jns_rekening;
        $jenis = $request->jenis;

        $skpd = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->first();
        if ($rekening == '0') {
            $dataRekening = '0 - Perubahan SAL';
        } else {
            $Cekrekening = DB::table('ms_rek1')->where(['kd_rek1' => $rekening])->first();
            $dataRekening = $Cekrekening->kd_rek1 .' - '. $Cekrekening->nm_rek1;
        } 
        if($jns_rekening == '1'){
            $dataRek = "(SELECT kd_rek3 from ms_rek3 where kd_rek3 = left(a.kd_rek6, 4)) as kd_rek,
                (SELECT nm_rek3 from ms_rek3 where kd_rek3 = left(a.kd_rek6, 4)) as nm_rek,";
        } else if ($jns_rekening == '2'){
            $dataRek = "(SELECT kd_rek4 from ms_rek4 where kd_rek4 = left(a.kd_rek6, 6)) as kd_rek,
                (SELECT nm_rek4 from ms_rek4 where kd_rek4 = left(a.kd_rek6, 6)) as nm_rek,";
        } else{
            $dataRek = "(SELECT kd_rek5 from ms_rek5 where kd_rek5 = left(a.kd_rek6, 8)) as kd_rek,
                (SELECT nm_rek5 from ms_rek5 where kd_rek5 = left(a.kd_rek6, 8)) as nm_rek,";
        }


        $dataBukuBesar = DB::select(
            "SELECT 
                kd_rek,
                nm_rek,
                SUM(debet) as debet,
                SUM(kredit) as kredit,
                SUM(debet_lalu) as debet_lalu,
                SUM(kredit_lalu) as kredit_lalu
            FROM (
                SELECT
                    $dataRek
                    sum(a.debet) as debet,
                    sum(a.kredit) as kredit,
                    0 as debet_lalu,
                    0 as kredit_lalu
                FROM
                    trdju_pkd a
                    LEFT JOIN trhju_pkd b ON a.no_voucher= b.no_voucher 
                    AND a.kd_unit= b.kd_skpd 
                WHERE
                    left(a.kd_rek6, 1) = ?
                    AND b.kd_skpd = ?
                    AND b.tgl_voucher >= ? 
                    AND b.tgl_voucher <= ?
                GROUP BY a.kd_rek6 
                    UNION ALL
                SELECT
                    $dataRek
                    0 as debet,
                    0 as kredit,
                    sum(a.debet) as debet_lalu,
                    sum(a.kredit) as kredit_lalu
                FROM
                    trdju_pkd a
                    LEFT JOIN trhju_pkd b ON a.no_voucher= b.no_voucher 
                    AND a.kd_unit= b.kd_skpd 
                WHERE
                    left(a.kd_rek6, 1) = ? 
                    AND b.kd_skpd = ? 
                    AND b.tgl_voucher < ? 
                GROUP BY a.kd_rek6
            ) x GROUP BY kd_rek, nm_rek ORDER BY kd_rek", [$rekening, $kd_skpd, $tgl_awal, $tgl_akhir, $rekening, $kd_skpd, $tgl_awal]
        );

        $view = view('rekap_buku_besar.print', array(
            'jenis' => $jenis,            
            'skpd' => $skpd,            
            'tgl_awal' => $tgl_awal,            
            'tgl_akhir' => $tgl_akhir,            
            'dataRekening' => $dataRekening,            
            'dataBukuBesar' => $dataBukuBesar,            
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
