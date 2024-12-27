<?php

namespace App\Http\Controllers\JurnalKoreksi\KoreksiRekening;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KOREKSIREKENINGController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('jurnal_koreksi.koreksi_rekening.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'skpd' => DB::table('ms_skpd')->get(),
        ];

        return view('jurnal_koreksi.koreksi_rekening.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->data;
        try {
            DB::beginTransaction();
            $username = Auth::user()->id;

            $ceklastNoKas = DB::table('trhbku')->selectRaw('MAX(no_kas) AS no_kas')
                ->where(['kd_skpd' => $data['kd_skpd']])
                ->first();
            
            $lastNoKas = $ceklastNoKas->no_kas;
			$lastNoKas = is_null($lastNoKas) ? 1 : $lastNoKas + 1;
            
            $cekTransaksiAwal = DB::table('trhtransout')
                ->where(['kd_skpd' => $data['kd_skpd'], 'no_bukti' => $data['no_bukti']])
                ->first();


            DB::table('trhtransout')
                ->insert([
                    'no_kas' => $lastNoKas,
					'tgl_kas' => $data['tgl'],
					'no_bukti' => $lastNoKas,
					'tgl_bukti' => $data['tgl'],
					'kd_skpd' => $data['kd_skpd'],
					'nm_skpd' => $data['nm_skpd'],
					'no_sp2d' => $cekTransaksiAwal->no_sp2d,
					'ket' => $data['ket'],
					'jns_spp' => $cekTransaksiAwal->jns_spp,
					'panjar' => 3,
					'total' => 0,
					'pay' => 'BANK',
					'username' => $username,
					'tgl_update' => date('Y-m-d H:i:s'),
					'no_transaksi_awal' => $data['no_bukti'],
                ]);

            DB::table('trhbku')
                ->insert([
                    'no_kas' => $lastNoKas,
					'tgl_kas' => $data['tgl'],
					'uraian' => $data['ket'],
					'kd_skpd' => $data['kd_skpd'],
					'nm_skpd' => $data['nm_skpd'],
					'jns_trans' => '12',
					'id_user' => $username,
                ]);

            $data['tampungan_data'] = json_decode($data['tampungan_data'], true);
            $daftar_data = $data['tampungan_data'];

            foreach ($daftar_data as $key => $value) {
                DB::insert(
                    "INSERT INTO trdtransout (no_bukti, kd_skpd, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, nilai, sumber, no_sp2d, volume, satuan, id_transaksi_awal, no_transaksi_awal)
                    SELECT ?, kd_skpd, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, nilai * -1, sumber, no_sp2d, volume, satuan, ?, ? FROM trdtransout
                    WHERE kd_skpd = ? AND no_bukti = ? AND id = ?", 
                    [
                        $lastNoKas, $daftar_data[$key]['id'], $data['no_bukti'],
                        $data['kd_skpd'], $data['no_bukti'], $daftar_data[$key]['id']
                    ]
                );

                DB::insert(
                    "INSERT INTO trdbku (no_kas, kd_skpd, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, keluar)
                    SELECT ?, kd_skpd, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, nilai * -1 FROM trdtransout
                    WHERE kd_skpd = ? AND no_bukti = ? AND id = ?",
                    [
                        $lastNoKas, $data['kd_skpd'], $data['no_bukti'], $daftar_data[$key]['id']
                    ]
                );

                DB::insert(
                    "INSERT INTO trdtransout (no_bukti, kd_skpd, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, nilai, sumber, no_sp2d, volume, satuan, id_transaksi_awal, no_transaksi_awal)
                    SELECT ?, kd_skpd, ?, ?, ?, ?, nilai, sumber, no_sp2d, volume, satuan, ?, ? FROM trdtransout
                    WHERE kd_skpd = ? AND no_bukti = ? AND id = ?",
                    [
                        $lastNoKas, $daftar_data[$key]['kd_sub_kegiatan'], $daftar_data[$key]['nm_sub_kegiatan'],
                        $daftar_data[$key]['kd_rek6'], $daftar_data[$key]['nm_rek6'], $daftar_data[$key]['id'], $data['no_bukti'],
                        $data['kd_skpd'], $data['no_bukti'], $daftar_data[$key]['id']
                    ]
                );

                DB::insert(
                    "INSERT INTO trdbku (no_kas, kd_skpd, kd_sub_kegiatan, nm_sub_kegiatan, kd_rek6, nm_rek6, keluar)
                    SELECT ?, kd_skpd, ?, ?, ?, ?, nilai FROM trdtransout
                    WHERE kd_skpd = ? AND no_bukti = ? AND id = ?",
                    [
                        $lastNoKas, $daftar_data[$key]['kd_sub_kegiatan'], $daftar_data[$key]['nm_sub_kegiatan'],
                        $daftar_data[$key]['kd_rek6'], $daftar_data[$key]['nm_rek6'],
                        $data['kd_skpd'], $data['no_bukti'], $daftar_data[$key]['id']
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($no_bukti, $kd_skpd)
    {
        $no_bukti = Crypt::decrypt($no_bukti);
        $kd_skpd = Crypt::decrypt($kd_skpd);
        $data = [
            'dataKoreksiRekening' => DB::table('trhtransout as a')->join('trhbku as b ', function ($join) {
                    $join->on('a.kd_skpd', '=', 'b.kd_skpd');
                    $join->on('a.no_transaksi_awal', '=', 'b.no_kas');
                })->where(['a.kd_skpd' => $kd_skpd, 'a.no_bukti' => $no_bukti])
                ->select('a.kd_skpd', 'a.nm_skpd', 'a.no_bukti', 'a.tgl_bukti', 'a.no_transaksi_awal',
                    'a.no_transaksi_awal', 'b.tgl_kas as tgl_kas_trh', 'a.ket',
                    'a.no_sp2d', 'a.jns_spp')->first(),
        ];
        return view('jurnal_koreksi.koreksi_rekening.show')->with($data);
    }

    public function load_data()
    {
        $data = DB::table('trhtransout')
            ->where(['panjar' => '3'])
            ->orderBy('tgl_bukti')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("koreksi_rekening.show", ['no_bukti' => Crypt::encrypt($row->no_bukti), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-info btn-xs" style="margin-right:4px" title="Lihat Data"><i class="fas fa-info-circle"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusKoreksi(\'' . $row->no_bukti . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-xs" style="margin-right:4px" title="Hapus Data"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function show_load_data(Request $request)
    {
        $noBukti = $request->no_bukti;
        $kdSKpd = $request->kd_skpd;
        $data = DB::table('trdtransout')->where(['no_bukti' => $noBukti, 'kd_skpd' => $kdSKpd])->get();
        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function load_daftar_transaksi(Request $request) 
    {
        $kd_skpd = $request->kd_skpd;
        $data = DB::table('trhtransout')->where(['kd_skpd' => $kd_skpd, 'panjar' => '0'])->orderBy('no_bukti', 'DESC')->get();
        return response()->json($data);
    }

    public function load_rincian_transaksi(Request $request) 
    {
        $kd_skpd = $request->kd_skpd;
        $no_bukti = $request->no_bukti;
        $data = DB::table('trdtransout')->distinct()->select('no_bukti', 'kd_sub_kegiatan', 'nm_sub_kegiatan')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_bukti])->orderBy('kd_sub_kegiatan', 'DESC')->get();
        return response()->json($data);
    }

    public function load_rincian_rekening(Request $request) 
    {
        $kd_skpd = $request->kd_skpd;
        $no_bukti = $request->no_bukti;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $data = DB::table('trdtransout')->where(['kd_skpd' => $kd_skpd, 'no_bukti' => $no_bukti, 'kd_sub_kegiatan' => $kd_sub_kegiatan])->orderBy('kd_rek6', 'DESC')->get();
        return response()->json($data);
    }

    public function load_daftar_transaksi_koreksi(Request $request) 
    {
        $kd_skpd = $request->kd_skpd;
        $data = DB::table('trskpd')->distinct()->select('kd_sub_kegiatan', 'nm_sub_kegiatan')->where(['kd_skpd' => $kd_skpd])->orderBy('kd_sub_kegiatan', 'DESC')->get();
        return response()->json($data);
    }

    public function load_rincian_rekening_koreksi(Request $request) 
    {
        $kd_skpd = $request->kd_skpd;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $jns_ang = collect(\DB::select("SELECT max(jns_ang) as jns_ang from trhrka WHERE kd_skpd = '$kd_skpd'"))->first();

        $data = DB::table('trdrka')->where(['kd_skpd' => $kd_skpd, 'jns_ang' => $jns_ang->jns_ang, 'kd_sub_kegiatan' => $kd_sub_kegiatan])->orderBy('kd_rek6', 'DESC')->get();
        return response()->json($data);
    }

    public function load_sumber_dana(Request $request) 
    {
        $kd_skpd = $request->kd_skpd;
        $kd_rek6 = $request->kd_rek6;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $jns_ang = collect(\DB::select("SELECT max(jns_ang) as jns_ang from trhrka WHERE kd_skpd = '$kd_skpd'"))->first();

        $data = DB::select(
            "SELECT 
                sumber, nm_sumber, SUM(total) as nilai 
            FROM trdpo
            WHERE kd_sub_kegiatan = '$kd_sub_kegiatan' and kd_skpd = '$kd_skpd' 
                    and jns_ang = '$jns_ang->jns_ang' and kd_rek6 = '$kd_rek6' 
            GROUP BY sumber, nm_sumber"
        );
        return response()->json($data);
    }

    public function load_realisasi_sumber_dana(Request $request) 
    {
        $kd_skpd = $request->kd_skpd;
        $kd_rek6 = $request->kd_rek6;
        $kd_sumber_dana = $request->kd_sumber_dana;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;

        $data = collect(\DB::select(
            "SELECT ISNULL(SUM(nilai), 0) AS nilai FROM (
				-- Realisasi SPP
				SELECT SUM(d.nilai) AS nilai FROM trhspp h
				JOIN trdspp d ON h.no_spp = d.no_spp
				WHERE jns_spp NOT IN (1, 2, 3) AND (sp2d_batal IS NULL OR sp2d_batal <> '1')
				AND h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ? AND d.sumber = ?

				UNION ALL

				-- Realisasi Penagihan
				SELECT SUM(d.nilai) AS nilai FROM trhtagih h
				JOIN trdtagih d ON h.no_bukti = d.no_bukti AND h.kd_skpd = d.kd_skpd
				WHERE NOT EXISTS (
					SELECT 1 FROM trhspp spp WHERE spp.kd_skpd = h.kd_skpd AND spp.no_tagih = h.no_bukti
				) AND h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ? AND d.sumber = ?

				UNION ALL

				-- Realisasi Transout CMS
				SELECT SUM(d.nilai) AS nilai FROM trhtransout_cmsbank h
				JOIN trdtransout_cmsbank d ON h.no_voucher = d.no_voucher AND h.kd_skpd = d.kd_skpd
				WHERE jns_spp IN (1, 2, 3) AND h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ? AND d.sumber = ?
				AND status_validasi = '0'

				UNION ALL

				-- Realisasi Transout
				SELECT SUM(d.nilai) AS nilai FROM trhtransout h
				JOIN trdtransout d ON h.no_bukti = d.no_bukti AND h.kd_skpd = d.kd_skpd
				WHERE (jns_spp IN (1, 2, 3) OR panjar IN (3)) AND h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ? AND d.sumber = ?
			) realisasi",
            [
                $kd_skpd, $kd_sub_kegiatan, $kd_rek6, $kd_sumber_dana,
				$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $kd_sumber_dana,
				$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $kd_sumber_dana,
				$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $kd_sumber_dana,
            ]
        ))->first();
        return response()->json($data);
    }

    public function load_daftar_nilai(Request $request) 
    {
        $kd_skpd = $request->kd_skpd;
        $kd_rek6 = $request->kd_rek6;
        $bulan = $request->bulan;
        $kd_sub_kegiatan = $request->kd_sub_kegiatan;
        $fbulan = date('m', strtotime($bulan));

        if ($fbulan >= 1) $col = 'jan';
		if ($fbulan >= 2) $col .= ' + feb';
		if ($fbulan >= 3) $col .= ' + mar';
		if ($fbulan >= 4) $col .= ' + apr';
		if ($fbulan >= 5) $col .= ' + mei';
		if ($fbulan >= 6) $col .= ' + jun';
		if ($fbulan >= 7) $col .= ' + jul';
		if ($fbulan >= 8) $col .= ' + agu';
		if ($fbulan >= 9) $col .= ' + sep';
		if ($fbulan >= 10) $col .= ' + okt';
		if ($fbulan >= 11) $col .= ' + nov';
		if ($fbulan >= 12) $col .= ' + des';

        $angkas = collect(\DB::select(
            "SELECT TOP 1 $col AS nilai from trdangkas where kd_skpd = ? and kd_sub_kegiatan =? 
                and kd_rek6 = ? order by jns_ang, jns_angkas DESC", [$kd_skpd, $kd_sub_kegiatan, $kd_rek6]
        ))->first();        
        $nilaiAngkas = $angkas ? $angkas->nilai : 0;
        
        $spd = collect(\DB::select(
            "SELECT 
                ISNULL(SUM(d.nilai), 0) AS nilai 
            FROM trhspd h
			JOIN trdspd d ON h.no_spd = d.no_spd
			JOIN (
				    -- Get Last Anggaran Kas
				    SELECT 
                        h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6, 
                        h.bulan_awal, h.bulan_akhir, h.jns_ang, 
                        MAX(jns_angkas) AS jns_angkas 
                    FROM trhspd h
				    JOIN trdspd d ON h.no_spd = d.no_spd
				    JOIN (
					        -- Get Last Anggaran
					        SELECT 
                                h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6, 
                                bulan_awal, bulan_akhir, MAX(jns_ang) AS jns_ang 
                            FROM trhspd h
					        JOIN trdspd d ON h.no_spd = d.no_spd
					        WHERE h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ? AND bulan_awal <= ?
					        GROUP BY h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6, bulan_awal, bulan_akhir
				        ) spd 
                    ON h.kd_skpd = spd.kd_skpd AND d.kd_sub_kegiatan = spd.kd_sub_kegiatan AND d.kd_rek6 = spd.kd_rek6
				        AND h.bulan_awal = spd.bulan_awal AND h.bulan_akhir = spd.bulan_akhir AND h.jns_ang = spd.jns_ang
				    GROUP BY h.kd_skpd, d.kd_sub_kegiatan, d.kd_rek6, h.bulan_awal, h.bulan_akhir, h.jns_ang
			    ) spd 
            ON h.kd_skpd = spd.kd_skpd AND d.kd_sub_kegiatan = spd.kd_sub_kegiatan AND d.kd_rek6 = spd.kd_rek6
			    AND h.bulan_awal = spd.bulan_awal AND h.bulan_akhir = spd.bulan_akhir AND h.jns_ang = spd.jns_ang AND h.jns_angkas = spd.jns_angkas",
            [$kd_skpd, $kd_sub_kegiatan, $kd_rek6, $fbulan]
        ))->first();
        $nilaiSpd = $spd->nilai;

        $realisasi = collect(\DB::select(
            "SELECT ISNULL(SUM(nilai), 0) AS nilai FROM (
				-- Realisasi SPP
				SELECT SUM(d.nilai) AS nilai FROM trhspp h
				JOIN trdspp d ON h.no_spp = d.no_spp
				WHERE jns_spp NOT IN (1, 2, 3) AND (sp2d_batal IS NULL OR sp2d_batal <> '1')
				AND h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ?

				UNION ALL

				-- Realisasi Penagihan
				SELECT SUM(d.nilai) AS nilai FROM trhtagih h
				JOIN trdtagih d ON h.no_bukti = d.no_bukti AND h.kd_skpd = d.kd_skpd
				WHERE NOT EXISTS (
					SELECT 1 FROM trhspp spp WHERE spp.kd_skpd = h.kd_skpd AND spp.no_tagih = h.no_bukti
				) AND h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ?

				UNION ALL

				-- Realisasi Transout CMS
				SELECT SUM(d.nilai) AS nilai FROM trhtransout_cmsbank h
				JOIN trdtransout_cmsbank d ON h.no_voucher = d.no_voucher AND h.kd_skpd = d.kd_skpd
				WHERE jns_spp IN (1, 2, 3) AND h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ?
				AND status_validasi = '0'

				UNION ALL

				-- Realisasi Transout
				SELECT SUM(d.nilai) AS nilai FROM trhtransout h
				JOIN trdtransout d ON h.no_bukti = d.no_bukti AND h.kd_skpd = d.kd_skpd
				WHERE (jns_spp IN (1, 2, 3) OR panjar IN (3)) AND h.kd_skpd = ? AND d.kd_sub_kegiatan = ? AND d.kd_rek6 = ?
			) realisasi",
            [
                $kd_skpd, $kd_sub_kegiatan, $kd_rek6,
                $kd_skpd, $kd_sub_kegiatan, $kd_rek6,
                $kd_skpd, $kd_sub_kegiatan, $kd_rek6,
                $kd_skpd, $kd_sub_kegiatan, $kd_rek6
            ]
        ))->first();
		$nilaiRealisasi = $realisasi->nilai;

		$data = ['nilai_spd' => $nilaiSpd, 'nilai_angkas' => $nilaiAngkas, 'nilai_realisasi' => $nilaiRealisasi];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
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
    public function destroy(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $kd_skpd = $request->kd_skpd;

        try {
            DB::beginTransaction();
            DB::delete("DELETE from trhtransout where no_bukti=? AND kd_skpd=?", [$no_bukti, $kd_skpd]);
           
            DB::delete("DELETE from trdtransout where no_bukti=? AND kd_skpd=?", [$no_bukti, $kd_skpd]);
    
            DB::delete("DELETE from trhbku where no_kas=? AND kd_skpd=?", [$no_bukti, $kd_skpd]);
            
            DB::delete("DELETE from trdbku where no_kas=? AND kd_skpd=?", [$no_bukti, $kd_skpd]);
    
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }
}
