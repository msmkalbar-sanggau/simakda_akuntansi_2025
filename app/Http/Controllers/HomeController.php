<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $jns_ang = collect(\DB::select("SELECT max(jns_ang) as jns_ang from trdrka"))->first();
        $date = date('Y-m-d');
        $thn = tahun_anggaran();
        $cekRole = Auth::user()->role;
        $cekUserSKPD = Auth::user()->kd_skpd;
        //dd($cekRole);
        if ($cekRole == '6') {
            $cekRea = DB::select(
                "SELECT group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align, SUM(trdrka.nilai) AS anggaran, SUM(jurnal.realisasi) AS realisasi
                FROM map_lra_rinci_jenis
                LEFT JOIN (
                    SELECT * FROM trdrka WHERE kd_skpd = ? and  jns_ang = ?
                ) trdrka ON LEFT(trdrka.kd_rek6, LEN(kd_rek)) = map_lra_rinci_jenis.kd_rek
                LEFT JOIN
                (
                    SELECT
                    trdju_pkd.kd_unit,
                    trdju_pkd.kd_sub_kegiatan,
                    trdju_pkd.map_real,
                    CASE
                        WHEN LEFT(trdju_pkd.map_real, 1) = '4' THEN SUM(kredit) - SUM(debet)
                        WHEN LEFT(trdju_pkd.map_real, 1) = '5' THEN SUM(debet) - SUM(kredit)
                        WHEN LEFT(trdju_pkd.map_real, 2) = '61' THEN SUM(kredit) - SUM(debet)
                        WHEN LEFT(trdju_pkd.map_real, 2) = '62' THEN SUM(debet) - SUM(kredit)
                        ELSE 0
                    END AS realisasi
                    FROM trhju_pkd
                    JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                    WHERE left(trdju_pkd.kd_rek6,1) IN ('4', '5', '6') AND trhju_pkd.tgl_voucher <= ? AND YEAR(trhju_pkd.tgl_voucher) = ?
                    AND trdju_pkd.kd_unit = ?
                    GROUP BY trdju_pkd.kd_unit, trdju_pkd.kd_sub_kegiatan, trdju_pkd.map_real
                ) jurnal
                ON trdrka.kd_skpd = jurnal.kd_unit AND trdrka.kd_sub_kegiatan = jurnal.kd_sub_kegiatan
                AND trdrka.kd_rek6 = jurnal.map_real
                GROUP BY group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align
                ORDER BY group_id, group_name",
                [
                    $cekUserSKPD, $jns_ang->jns_ang, $date, $thn, $cekUserSKPD
                ]
            );
        } else {
        $cekRea = DB::select(
                "SELECT group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align, SUM(trdrka.nilai) AS anggaran, SUM(jurnal.realisasi) AS realisasi
                FROM map_lra_rinci_jenis
                LEFT JOIN (
                    SELECT * FROM trdrka WHERE jns_ang = ?
                ) trdrka ON LEFT(trdrka.kd_rek6, LEN(kd_rek)) = map_lra_rinci_jenis.kd_rek
                LEFT JOIN
                (
                    SELECT
                    trdju_pkd.kd_unit,
                    trdju_pkd.kd_sub_kegiatan,
                    trdju_pkd.map_real,
                    CASE
                        WHEN LEFT(trdju_pkd.map_real, 1) = '4' THEN SUM(kredit) - SUM(debet)
                        WHEN LEFT(trdju_pkd.map_real, 1) = '5' THEN SUM(debet) - SUM(kredit)
                        WHEN LEFT(trdju_pkd.map_real, 2) = '61' THEN SUM(kredit) - SUM(debet)
                        WHEN LEFT(trdju_pkd.map_real, 2) = '62' THEN SUM(debet) - SUM(kredit)
                        ELSE 0
                    END AS realisasi
                    FROM trhju_pkd
                    JOIN trdju_pkd ON trhju_pkd.no_voucher = trdju_pkd.no_voucher AND trhju_pkd.kd_skpd = trdju_pkd.kd_unit
                    WHERE left(trdju_pkd.map_real, 1) IN ('4', '5', '6') AND trhju_pkd.tgl_voucher <= ? AND YEAR(trhju_pkd.tgl_voucher) = ?
                    GROUP BY trdju_pkd.kd_unit, trdju_pkd.kd_sub_kegiatan, trdju_pkd.map_real
                ) jurnal
                ON trdrka.kd_skpd = jurnal.kd_unit AND trdrka.kd_sub_kegiatan = jurnal.kd_sub_kegiatan
                AND trdrka.kd_rek6 = jurnal.map_real
                GROUP BY group_id, kd_rek, group_name, padding, is_bold, show_kd_rek, right_align
                ORDER BY group_id, group_name",
                [
                    $jns_ang->jns_ang, $date, $thn
                ]
            );
        }
        $cekPaguPen = 0;
        $cekPaguAng = 0;
        $cekReaPen = 0;
        $cekReaAng = 0;
        foreach ($cekRea as $key => $value) {
            if ($value->kd_rek == '4') {
                $cekPaguPen = $value->anggaran;
                $cekReaPen = $value->realisasi;
            }
            if ($value->kd_rek == '5') {
                $cekPaguAng = $value->anggaran;
                $cekReaAng = $value->realisasi;
            }
        }
        $data = [
            'pagu_pendapatan' => abs($cekPaguPen),
            'pagu_belanja' => abs($cekPaguAng),
            'rea_pendapatan' => abs($cekReaPen),
            'rea_belanja' => abs($cekReaAng),
        ];
        return view('home')->with($data);
    }

      // Ubah Password :: calvin
    public function ubahPassword($id)
    {
        $id = Crypt::decryptString($id);
        $data = [
            'user' => DB::table('pengguna1')->where(['id' => $id])->first(),
            'kd_skpd' => DB::table('ms_skpd')->orderBy('kd_skpd')->get()
        ];

        return view('fungsi.ubah_password.index')->with($data);
    }

    public function simpanUbahPassword(Request $request)
    {
        $data = $request->data;

        if ($data['password'] != $data['password2']) {
            return response()->json([
                'message' => '2'
            ]);
        }

        DB::beginTransaction();
        try {
            DB::table('pengguna1')->where(['id' => $data['id'], 'username' => $data['username']])->update([
                'password' => Hash::make($data['password'])
            ]);
            DB::commit();
            return response()->json([
                'message' => '1'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '0'
            ]);
        }
    }
    //End
}
