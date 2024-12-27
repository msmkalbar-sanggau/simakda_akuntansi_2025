<?php

namespace App\Http\Controllers\LaporanMonitoring\RegisterSP2DBPK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class REGISTERSP2DBPKTController extends Controller
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
        return view('laporan_monitoring.register_sp2d_bpk_terbaru.index')->with($data);
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
        $jenis = $request->jenis;

		$skpd = $kd_skpd ? "AND b.kd_skpd = '$kd_skpd'" : '';
        if ($akumulasi == 1) {
            $js_akumulasi = '<=';
        } else {
            $js_akumulasi = '=';
        }
        
        $data = DB::select(
            "SELECT 
                left(c.kd_sub_kegiatan, 1) as kd_urusan,
                left(c.kd_sub_kegiatan, 7) as kd_bidang,
                b.kd_skpd as kd_unit,
                c.kd_sub_kegiatan,
                b.nm_skpd as nm_opd,
                LEFT ( a.kd_rek6, 1 ) AS kd_rek1,
                ( SELECT nm_rek1 FROM ms_rek1 WHERE LEFT ( a.kd_rek6, 1 ) = kd_rek1 ) AS nm_rek1,
                LEFT ( a.kd_rek6, 2 ) AS kd_rek2,
                ( SELECT nm_rek2 FROM ms_rek2 WHERE LEFT ( a.kd_rek6, 2 ) = kd_rek2 ) AS nm_rek2,
                LEFT ( a.kd_rek6, 4 ) AS kd_rek3,
                ( SELECT nm_rek3 FROM ms_rek3 WHERE LEFT ( a.kd_rek6, 4 ) = kd_rek3 ) AS nm_rek3,
                LEFT ( a.kd_rek6, 6 ) AS kd_rek4,
                ( SELECT nm_rek4 FROM ms_rek4 WHERE LEFT ( a.kd_rek6, 6 ) = kd_rek4 ) AS nm_rek4,
                LEFT ( a.kd_rek6, 8 ) AS kd_rek5,
                ( SELECT nm_rek5 FROM ms_rek5 WHERE LEFT ( a.kd_rek6, 8 ) = kd_rek5 ) AS nm_rek5,
                a.kd_rek6,
                a.nm_rek6,
                left(c.kd_sub_kegiatan, 7) as kd_program,
                left(c.kd_sub_kegiatan, 12) as kd_kegiatan, 
                b.no_spm,
                b.tgl_spm,
                b.no_sp2d,
                b.tgl_sp2d,
                sum(a.nilai) as nilai_sp2d,
                b.jns_spp as jns_sp2d,
                b.keperluan as uraian_sp2d,
                LEFT ( a.kd_rek6, 4 ) AS jenis_belanja,
                ( SELECT nm_rek3 FROM ms_rek3 WHERE LEFT ( a.kd_rek6, 4 ) = kd_rek3 ) AS objek_belanja,
                b.no_rek as norek_penerima,
                '' as nama_pemilik_norek,
                b.nmrekan as nm_perusahaan,
                c.kontrak as no_kontrak,
                (SELECT tgl_kerja from ms_kontrak where no_kontrak = c.kontrak and kd_skpd = b.kd_skpd) as tgl_kontrak,
                '' as waktu_kontrak,
                '' as no_addendum,
                '' as tgl_addendum,
                '' as waktu_addendum,
                '' as nilai_addendum,
                (SELECT nm_kerja from ms_kontrak where no_kontrak = c.kontrak and kd_skpd = b.kd_skpd) as keperluaan_kontrak,
                (SELECT nilai from ms_kontrak where no_kontrak = c.kontrak and kd_skpd = b.kd_skpd) as nilai_kontrak,
                --mulai potongan
                '0' as iwp1,
                '0' as iwp8,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210108010001' and kd_trans=a.kd_rek6)as iwp,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210107010001' and kd_trans=a.kd_rek6)as taperum,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210105010001' and kd_trans=a.kd_rek6)as pph21,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210105050001' and kd_trans=a.kd_rek6)as lain2,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210601010019' and kd_trans=a.kd_rek6)as jkk_asn,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210601010020' and kd_trans=a.kd_rek6)as jkk_p3k,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210601010021' and kd_trans=a.kd_rek6)as jkm_asn,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210601010022' and kd_trans=a.kd_rek6)as jkm_p3k,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210102010001' and kd_trans=a.kd_rek6)as BPJS,
                '0' as BPJS1,
                '0' as sewarumah,
                '0' as lbhtunj,
                '0' as iujk,
                '0' as BPJS4,
                '0' as iwp3,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210106010001' and kd_trans=a.kd_rek6)as ppn,
                '0' as pph21pjk,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210105020001' and kd_trans=a.kd_rek6)as pph22,
                (select isnull(sum(nilai),0) from trspmpot c where b.no_spm=c.no_spm and c.kd_rek6='210105030001' and kd_trans=a.kd_rek6)as pph23,
                '0' as pphfinal
            from trdspp a 
            INNER JOIN trhsp2d b on a.no_spp = b.no_spp 
            and a.kd_skpd=b.kd_skpd
            INNER JOIN trhspp c on b.no_spp = c.no_spp
            and b.kd_skpd = c.kd_skpd
            where (b.sp2d_batal<>1 OR b.sp2d_batal is null) and b.status_bud = 1
                and month(b.tgl_sp2d) $js_akumulasi $bulan $skpd
            GROUP BY
                c.kd_sub_kegiatan,
                b.kd_skpd,
                b.nm_skpd,
                a.kd_rek6,
                a.nm_rek6,
                b.no_spm,
                b.tgl_spm,
                b.no_sp2d,
                b.tgl_sp2d,
                b.jns_spp,
                b.keperluan,
                b.jenis_beban,
                b.no_rek,
                b.nmrekan,
                c.kontrak"
        ); 

        $view = view('laporan_monitoring.register_sp2d_bpk_terbaru.print', array(
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
