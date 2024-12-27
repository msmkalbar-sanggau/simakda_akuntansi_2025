<?php

namespace App\Http\Controllers\LaporanMonitoring\RegisterSP2DBPK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class REGISTERSP2DBPKController extends Controller
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
            'kd_rekening' => DB::select(
                "SELECT
                    DISTINCT kd_rek4, nm_rek4
                FROM ms_rek4
                JOIN trdspp ON ms_rek4.kd_rek4 = LEFT ( trdspp.kd_rek6, 6 )
                WHERE LEFT ( kd_rek4, 1 ) = '5'"),
        ];
        return view('laporan_monitoring.register_sp2d_bpk.index')->with($data);
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
        $akumulasi = $request->akumulasi;
        $bulan = $request->bulan;
        $rekening = $request->rekening;
        $kd_belanja = $request->kd_belanja;
        $jenis = $request->jenis;

		$skpd = $kd_skpd ? "AND a.kd_skpd = '$kd_skpd'" : '';
        if ($rekening == '5') {
            $belanja = $kd_belanja ? "AND left(kd_rek6, 6) = '$kd_belanja'" : '';
        } else {
            $belanja = $rekening ? "AND LEFT(kd_rek6,1) = '$rekening'" : '';
        }

        if ($akumulasi == 1) {
            $js_akumulasi = '<=';
        } else {
            $js_akumulasi = '=';
        }

        $data = DB::select(
            "SELECT a.kd_skpd, a.nm_skpd, a.jns_spp, a.no_spp, a.tgl_spp,
				c.keperluan, a.no_rek, LEFT(kd_rek6,1) as kd_rek1,
					(select nm_rek1 from ms_rek1 where left(b.kd_rek6,1)=kd_rek1) as nm_rek1,
					LEFT(kd_rek6,2) as kd_rek2,
					(select nm_rek2 from ms_rek2 where left(b.kd_rek6,2)=kd_rek2) as nm_rek2,
					LEFT(kd_rek6,4) as kd_rek3,
					(select nm_rek3 from ms_rek3 where left(b.kd_rek6,4)=kd_rek3) as nm_rek3,
					LEFT(kd_rek6,6) as kd_rek4,
					(select nm_rek4 from ms_rek4 where left(b.kd_rek6,6)=kd_rek4) as nm_rek4,
					LEFT(kd_rek6,8) as kd_rek5,
					(select nm_rek5 from ms_rek5 where left(b.kd_rek6,8)=kd_rek5) as nm_rek5,
					kd_rek6, nm_rek6, b.nilai, c.no_spm, c.tgl_spm, no_sp2d,
					b.kd_sub_kegiatan, b.nm_sub_kegiatan, tgl_sp2d,
					tgl_kas_bud, c.nilai as nilai_sp2d, c.nmrekan
			from trhspp a INNER JOIN trdspp b on a.no_spp=b.no_spp and a.kd_skpd=b.kd_skpd
			INNER JOIN trhsp2d c on a.no_spp=c.no_spp and a.kd_skpd=c.kd_skpd
			WHERE MONTH(tgl_sp2d) $js_akumulasi $bulan $skpd $belanja
			and (c.sp2d_batal=0 OR c.sp2d_batal is NULL)
			order by kd_skpd,no_sp2d"
        );

        $view = view('laporan_monitoring.register_sp2d_bpk.print', array(
            'data' => $data,
            'jenis' => $jenis,
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
