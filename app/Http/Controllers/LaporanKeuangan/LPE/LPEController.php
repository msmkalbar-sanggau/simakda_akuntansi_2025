<?php

namespace App\Http\Controllers\LaporanKeuangan\LPE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class LPEController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $data = [
            'ttd' => DB::table('ms_ttd')->select('nip', 'nama')->whereIn('kode', ['PPKD', 'BUP'])->get(),
            'data_skpd' => DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd', 'bank', 'rekening', 'npwp')->where('kd_skpd', $kd_skpd)->first(),
            'jns_anggaran' => jenis_anggaran(),
            'jns_anggaran2' => jenis_anggaran()
        ];
        return view('laporan_keuangan.lpe.index')->with($data);
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
                ->where(['kd_skpd' => $kd_skpd, 'kode' => 'PA'])
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

     public function cariSkpd(Request $request)
    {
        $type       = Auth::user()->is_admin;
        $jenis      = $request->jenis;
        $kd_skpd    = Auth::user()->kd_skpd;
        $kd_org     = substr($kd_skpd, 0, 17);
        $skpd_cek   = substr($kd_skpd,18,4);
        // dd($skpd_cek);
        if ($type == '1') {
            if ($jenis == 'skpd') {
                $data   = DB::table('ms_organisasi')->select('kd_org as kd_skpd', 'nm_org as nm_skpd')->orderBy('kd_org')->get();
            } else {
                $data   = DB::table('ms_skpd')->select('kd_skpd', 'nm_skpd')->orderBy('kd_skpd')->get();
            }
        } else {
            if ($jenis == 'skpd') {
                $data   = DB::table('ms_organisasi')->where(DB::raw("LEFT(kd_skpd,17)"), '=', $kd_org)->select('kd_org as kd_skpd', 'nm_org as nm_skpd')->get();
            } else {
                if ($skpd_cek=="0000") {

                    $data   = DB::table('ms_skpd')->where(DB::raw("LEFT(kd_skpd,17)"), '=', $kd_org)->select('kd_skpd', 'nm_skpd')->get();
                }else{
                    $data   = DB::table('ms_skpd')->where(DB::raw("kd_skpd"), '=', $kd_skpd)->select('kd_skpd', 'nm_skpd')->get();
                }
            }
        }
        return response()->json($data);


        return response()->json($data);
    }

    public function show(Request $request)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        $penandatangan  = $request->penandatangan;
        $tgl_ttd        = $request->tgl_ttd;
        $bulan          = $request->bulan;
        $enter          = $request->spasi;
        $cetak          = $request->cetak;
        $kd_skpd        = $request->kd_skpd;
        $skpdunit    = $request->skpdunit;
        if ($request->kd_skpd == '') {
            $kd_skpd        = "";
            $skpd_clause    = "";
            $skpd_clauses    = "";
        } else {
            if ($skpdunit == "unit") {
                $kd_skpd = $kd_skpd;
            } else if ($skpdunit == "skpd") {
                $kd_skpd = substr($kd_skpd, 0, 22);
            }
            $skpd_clause    = "where left(kd_unit,len('$kd_skpd'))='$kd_skpd'";
            $skpd_clauses    = "and left(b.kd_skpd,len('$kd_skpd'))='$kd_skpd'";
        }

        $thn_ang    = tahun_anggaran();
        $thn_ang1   = $thn_ang - 1;
        $thn_ang2   = $thn_ang - 2;

        $modtahun = $thn_ang % 4;

        if ($modtahun = 0) {
            $nilaibulan = ".31 JANUARI.29 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
        } else {
            $nilaibulan = ".31 JANUARI.28 FEBRUARI.31 MARET.30 APRIL.31 MEI.30 JUNI.31 JULI.31 AGUSTUS.30 SEPTEMBER.31 OKTOBER.30 NOVEMBER.31 DESEMBER";
        }

        $arraybulan = explode(".", $nilaibulan);

        $ttd = DB::table('ms_ttd')->where(['nip' => $penandatangan])->first();
        $nm_bln = $arraybulan[$bulan];




        $ekuitas_awal = collect(DB::select("SELECT sum(nilai) nilai,sum(nilai_lalu) nilai_lalu
                        from(
                        --1 ekuitas_awal
                        select isnull(sum(nilai),0)nilai,0 nilai_lalu from data_ekuitas_lalu($bulan,$thn_ang1,$thn_ang2) $skpd_clause
                        union all
                        --1 ekuitas lalu
                        select 0 nilai, isnull(sum(nilai),0)nilai_lalu from data_real_ekuitas_lalu($bulan,$thn_ang1,$thn_ang2) $skpd_clause
                        )a"))->first();
        // dd($ekuitas_awal);
        $surdef = collect(DB::select("SELECT sum(nilai)nilai,sum(nilai_lalu)nilai_lalu
                        from(
                        --2 surplus lo
                        select sum(nilai_pen-nilai_bel) nilai,0 nilai_lalu
                        from(
                            select sum(kredit-debet) as nilai_pen,0 nilai_bel from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and a.kd_unit=b.kd_skpd
                            where year(tgl_voucher)=$thn_ang and month(tgl_voucher)<=$bulan and left(kd_rek6,1) in ('7') $skpd_clauses
                            union all
                            select 0 nilai_pen,sum(debet-kredit) as nilai_bel from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and a.kd_unit=b.kd_skpd
                            where year(tgl_voucher)=$thn_ang and month(tgl_voucher)<=$bulan and left(kd_rek6,1) in ('8') $skpd_clauses
                            )a
                            union all
                            -- 2 surplus lo lalu
                            select 0 nilai,isnull(sum(nilai_pen-nilai_bel),0) nilai_lalu
                            from(
                            select sum(kredit-debet) as nilai_pen,0 nilai_bel
                            from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and a.kd_unit=b.kd_skpd
                            where year(tgl_voucher)=$thn_ang1 and left(kd_rek6,1) in ('7') $skpd_clauses
                            union all
                            select 0 nilai_pen,sum(debet-kredit) as nilai_bel
                            from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and a.kd_unit=b.kd_skpd
                            where year(tgl_voucher)=$thn_ang1 and left(kd_rek6,1) in ('8') $skpd_clauses
                            )a
                        )a"))->first();

        $koreksi = collect(DB::select("SELECT sum(nilai)nilai,sum(nilai_lalu)nilai_lalu
                        from(
                            --5 nilai lpe 1
                            select isnull(sum(kredit-debet),0) nilai , 0 nilai_lalu
                            from trdju a inner join trhju b on a.no_voucher=b.no_voucher and b.kd_skpd=a.kd_unit
                            where  reev='2' and kd_rek6='310101010001' and year(b.tgl_voucher)=$thn_ang and month(b.tgl_voucher)<=$bulan $skpd_clauses
                            union all
                            --5 nilai lpe 1 lalu
                            select 0 nilai,isnull(sum(kredit-debet),0) nilai_lalu
                            from trdju a inner join trhju b on a.no_voucher=b.no_voucher and b.kd_skpd=a.kd_unit
                            where  reev='2' and kd_rek6='310101010001' and year(b.tgl_voucher)=$thn_ang1 $skpd_clauses
                        )a"))->first();

        $selisih = collect(DB::select("SELECT sum(nilai)nilai,sum(nilai_lalu)nilai_lalu
                        from(
                            --6 nilai dr
                            select isnull(sum(kredit-debet),0) nilai, 0 nilai_lalu
                            from trdju a inner join trhju b on a.no_voucher=b.no_voucher and b.kd_skpd=a.kd_unit
                            where  reev='1' and kd_rek6='310101010001' and year(tgl_voucher)=$thn_ang and month(tgl_voucher)<=$bulan $skpd_clauses
                            union all
                            --6 nilai dr lalu
                            select 0 nilai, isnull(sum(kredit-debet),0) nilai_lalu
                            from trdju a inner join trhju b on a.no_voucher=b.no_voucher and b.kd_skpd=a.kd_unit
                            where  reev='1' and kd_rek6='310101010001' and year(tgl_voucher)=$thn_ang1 $skpd_clauses
                        )a"))->first();

        $lain = collect(DB::select("SELECT sum(nilai)nilai,sum(nilai_lalu)nilai_lalu
                        from(
                            --7 nilai lpe2
                            select isnull(sum(kredit-debet),0) nilai, 0 nilai_lalu
                            from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and b.kd_skpd=a.kd_unit
                            where  reev='3' and left(kd_rek6,4)='3101' and year(tgl_voucher)>=$thn_ang and month(tgl_voucher)<=$bulan $skpd_clauses
                            union all
                            --7 nilai lpe2 lalu
                            select 0 nilai,isnull(sum(kredit-debet),0) nilai_lalu
                            from trdju_pkd a inner join trhju_pkd b on a.no_voucher=b.no_voucher and b.kd_skpd=a.kd_unit
                            where  reev='3' and left(kd_rek6,4)='3101' and year(tgl_voucher)>=$thn_ang1 $skpd_clauses
                        )a"))->first();







        $data = [
            'header'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first(),
            // 'ekuitas_awal'      => $ekuitas_awal,
            'ekuitas_awal'      => $ekuitas_awal->nilai,
            'ekuitas_awal_lalu' => $ekuitas_awal->nilai_lalu,
            'surdef'            => $surdef->nilai,
            'surdef_lalu'       => $surdef->nilai_lalu,
            'koreksi'           => $koreksi->nilai,
            'koreksi_lalu'      => $koreksi->nilai_lalu,
            'selisih'           => $selisih->nilai,
            'selisih_lalu'      => $selisih->nilai_lalu,
            'lain'              => $lain->nilai,
            'lain_lalu'         => $lain->nilai_lalu,
            'enter'             => $enter,
            'daerah'            => DB::table('sclient')->select('daerah')->first(),
            'bulan'             => $bulan,
            'kd_skpd'           => $kd_skpd,
            'nm_bln'            => $nm_bln,
            'thn_ang'           => $thn_ang,
            'thn_ang1'          => $thn_ang1,
            'tgl_ttd'           => $tgl_ttd,
            'ttd'               => $ttd,
        ];
        // dd($data['ekuitas_awal']->nilai);
        $view =  view('laporan_keuangan.lpe.print')->with($data);


        if ($cetak == '1') {
            return $view;
        } else if ($cetak == '2') {
            $pdf = PDF::loadHtml($view)->setPaper('legal');
            return $pdf->stream('LPE.pdf');
        } else {

            header("Cache-Control: no-cache, no-store, must_revalidate");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachement; filename="LPE.xls"');
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
