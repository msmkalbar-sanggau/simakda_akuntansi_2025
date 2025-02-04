<?php

namespace App\Http\Controllers\Jurnal\InputJurnal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InputJurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $role = Auth::user()->role;

        if($role == 1 || $role == 3){
            $cek = DB::table('ms_skpd')->count();
            $daftar_skpd1 = DB::table('ms_skpd')->get();
        }else{
            $cek = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->count();
            $daftar_skpd1 = collect(DB::select("SELECT * from ms_skpd WHERE kd_skpd = ? ",[$kd_skpd]))->first();
        }

        $data = [
            'cekskpd' => $cek,
            'daftar_skpd' => $daftar_skpd1,
        ];
        return view('jurnal.input_jurnal.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kd_skpd = Auth::user()->kd_skpd;
        $role = Auth::user()->role;

        if($role == 1){
            $skpd1 = DB::table('ms_skpd')->get();
        }else{
            $skpd1 = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->get();
        }

        $data = [
            'skpd' => $skpd1,
        ];

        return view('jurnal.input_jurnal.create')->with($data);
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
            $cek = DB::table('trhju_pkd')
                ->where(['no_voucher' => $data['no_voucher'], 'kd_skpd' => $data['kd_skpd']])
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => '2'
                ]);
            } else {
                DB::table('trhju_pkd')
                    ->insert([
                        'no_voucher' => $data['no_voucher'],
                        'tgl_voucher' => $data['tgl_voucher'],
                        'kd_skpd' => $data['kd_skpd'],
                        'nm_skpd' => $data['nm_skpd'],
                        'ket' => $data['ket'],
                        'tgl_update' => date('Y-m-d H:i:s'),
                        'username' => Auth::user()->nama,
                        'tgl_real' => $data['tgl_real'],
                        'kd_unit' => '',
						'map_real' => '',
                        'total_d' => $data['total_debet'],
                        'total_k' => $data['total_kredit'],
                        'tabel' => '1',
                        'reev' => $data['reev'],
                        'kd_skpd_mutasi' => '',
                        'nm_skpd_mutasi' => '',
                    ]);

                $data['tampungan_data'] = json_decode($data['tampungan_data'], true);
                $daftar_data = $data['tampungan_data'];

                foreach ($daftar_data as $key => $value) {
                    $data_tampungan = [
                        'no_voucher' => $data['no_voucher'],
                        'kd_sub_kegiatan' => $daftar_data[$key]['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $daftar_data[$key]['nm_sub_kegiatan'],
                        'kd_rek6' => $daftar_data[$key]['kd_rek6'],
                        'nm_rek6' => $daftar_data[$key]['nm_rek6'],
                        'debet' => $daftar_data[$key]['debet'],
                        'kredit' => $daftar_data[$key]['kredit'],
                        'rk' => $daftar_data[$key]['rk'],
                        'jns' => $daftar_data[$key]['jns'],
                        'kd_unit' => $data['kd_skpd'],
                        'map_real' => $daftar_data[$key]['kd_rek6'],
                        'pos' => $daftar_data[$key]['pos'],
                        'urut' => $key+1,
                    ];
                    DB::table('trdju_pkd')->insert($data_tampungan);
                }

                DB::commit();
                return response()->json([
                    'message' => '1'
                ]);
            }
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
    public function show($no_voucher, $kd_skpd)
    {
        $no_voucher = Crypt::decrypt($no_voucher);
        $kd_skpd = Crypt::decrypt($kd_skpd);
        $data = [
            'dataJurnal' => DB::table('trhju_pkd')->where(['no_voucher' => $no_voucher, 'kd_skpd' => $kd_skpd])->first(),
        ];
        return view('jurnal.input_jurnal.show')->with($data);
    }

    public function load_data(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        if ($kd_skpd == 'all') {
            $data = DB::table('trhju_pkd')
                ->where(['tabel' => '1'])
                ->orderBy('tgl_voucher')
                ->orderBy('no_voucher')
                ->orderBy('kd_skpd')
                ->get();
        } else {
            $data = DB::table('trhju_pkd')
                ->where(['tabel' => '1', 'kd_skpd' => $kd_skpd])
                ->orderBy('tgl_voucher')
                ->orderBy('no_voucher')
                ->get();
        }

        return DataTables::of($data)->addIndexColumn()->addColumn('aksi', function ($row) {
            $btn = '<a href="' . route("input_jurnal.edit", ['no_voucher' => Crypt::encrypt($row->no_voucher), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-success btn-xs" style="margin-right:4px" title="Edit Data"><i class="fas fa-edit"></i></a>';
            $btn .= '<a href="' . route("input_jurnal.show", ['no_voucher' => Crypt::encrypt($row->no_voucher), 'kd_skpd' => Crypt::encrypt($row->kd_skpd)]) . '" class="btn btn-info btn-xs" style="margin-right:4px" title="Lihat Data"><i class="fas fa-info-circle"></i></a>';
            $btn .= '<a href="javascript:void(0);" onclick="hapusJurnal(\'' . $row->no_voucher . '\',\'' . $row->kd_skpd . '\');" class="btn btn-danger btn-xs" style="margin-right:4px" title="Hapus Data"><i class="fas fa-trash-alt"></i></a>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function show_load_data(Request $request)
    {
        $noVoucher = $request->no_voucher;
        $kdSKpd = $request->kd_skpd;
        $data = DB::table('trhju_pkd as a')->join('trdju_pkd as b ', function ($join) {
                    $join->on('a.kd_skpd', '=', 'b.kd_unit');
                    $join->on('a.no_voucher', '=', 'b.no_voucher');
                })->where(['b.no_voucher' => $noVoucher, 'kd_skpd' => $kdSKpd])->get();
        return DataTables::of($data)->addIndexColumn()->make(true);
    }

    public function load_kd_kegiatan(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $jns = $request->jns;

        $data = DB::select("SELECT distinct kd_sub_kegiatan, nm_sub_kegiatan From trdrka where kd_skpd = ? and left(kd_rek6, 1) = ?", [$kd_skpd, $jns]);
        return response()->json($data);
    }

    public function load_kd_rekening(Request $request)
    {
        $kd_skpd = $request->kd_skpd;
        $kd_kegiatan = $request->kd_kegiatan;
        $kd_rek = $request->kd_rek;
        $jns = $request->jns;

        if ($kd_rek != '') {
            $notIn = " and a.kd_rek6 not in ('$kd_rek') ";
        } else {
            $notIn  = "";
        }

        if ($jns == '4' || $jns == '5' || $jns == '6') {
            $data = DB::select("SELECT distinct a.kd_rek6, a.nm_rek6 from trdrka as a Inner join ms_rek6 as b
                    on a.kd_rek6 = b.kd_rek6 where a.kd_sub_kegiatan = '$kd_kegiatan' and a.kd_skpd = '$kd_skpd' $notIn order by kd_rek6");
        } else if ($jns == '0') {
            $data = DB::select("SELECT top 1 '000000000000' as kd_rek6,'Perubahan SAL' as nm_rek6 FROM ms_rek6");
        } else {
            // $data = DB::select("SELECT DISTINCT kd_rek6, nm_rek6,kd_rek2020,nm_rek2020 From ms_rek6 a left join map_rek on kd_rek6 = kd_rek2021 where left(kd_rek6, 1) = '$jns' $notIn order by kd_rek6 OFFSET 0 ROWS FETCH NEXT 700 ROWS ONLY");
            $data = DB::select("SELECT DISTINCT kd_rek6, nm_rek6,kd_rek2020,nm_rek2020 From ms_rek6 a left join map_rek on kd_rek6 = kd_rek2021 where left(kd_rek6, 1) = '$jns' $notIn order by kd_rek6");
        }

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($no_voucher, $kd_skpd)
    {
        $no_voucher = Crypt::decrypt($no_voucher);
        $kd_skpd = Crypt::decrypt($kd_skpd);
        $role = Auth::user()->role;

        if($role == 1){
            $skpd1 = DB::table('ms_skpd')->get();
        }else{
            $skpd1 = DB::table('ms_skpd')->where(['kd_skpd' => $kd_skpd])->get();
        }
        $data = [
            'skpd' => $skpd1,
            'jurnal' => DB::table('trhju_pkd')->where(['kd_skpd' => $kd_skpd, 'no_voucher' => $no_voucher])->first(),
            'detail_jurnal' => DB::table('trhju_pkd as a')->join('trdju_pkd as b', function ($join) {
                $join->on('a.no_voucher', '=', 'b.no_voucher');
                $join->on('a.kd_skpd', '=', 'b.kd_unit');
            })->where(['kd_skpd' => $kd_skpd, 'a.no_voucher' => $no_voucher])->get(),
        ];

        return view('jurnal.input_jurnal.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->data;
        try {
            DB::beginTransaction();
            $cek = DB::table('trhju_pkd')
                ->where(['no_voucher' => $data['no_voucher'], 'kd_skpd' => $data['kd_skpd']])
                ->count();

            if ($cek > 0 && $data['no_voucher'] != $data['no_voucher_lama']) {
                return response()->json([
                    'message' => '2'
                ]);
            } else {
                DB::table('trhju_pkd')
                    ->where(['no_voucher' => $data['no_voucher_lama'], 'kd_skpd' => $data['kd_skpd']])
                    ->delete();

                DB::table('trhju_pkd')
                    ->insert([
                        'no_voucher' => $data['no_voucher'],
                        'tgl_voucher' => $data['tgl_voucher'],
                        'kd_skpd' => $data['kd_skpd'],
                        'nm_skpd' => $data['nm_skpd'],
                        'ket' => $data['ket'],
                        'tgl_update' => date('Y-m-d H:i:s'),
                        'username' => Auth::user()->nama,
                        'tgl_real' => $data['tgl_real'],
                        'kd_unit' => '',
						'map_real' => '',
                        'total_d' => $data['total_debet'],
                        'total_k' => $data['total_kredit'],
                        'tabel' => '1',
                        'reev' => $data['reev'],
                        'kd_skpd_mutasi' => '',
                        'nm_skpd_mutasi' => '',
                    ]);

                DB::table('trdju_pkd')
                    ->where(['no_voucher' => $data['no_voucher_lama'], 'kd_unit' => $data['kd_skpd']])
                    ->delete();

                $data['tampungan_data'] = json_decode($data['tampungan_data'], true);
                $daftar_data = $data['tampungan_data'];

                foreach ($daftar_data as $key => $value) {
                    $data_tampungan = [
                        'no_voucher' => $data['no_voucher'],
                        'kd_sub_kegiatan' => $daftar_data[$key]['kd_sub_kegiatan'],
                        'nm_sub_kegiatan' => $daftar_data[$key]['nm_sub_kegiatan'],
                        'kd_rek6' => $daftar_data[$key]['kd_rek6'],
                        'nm_rek6' => $daftar_data[$key]['nm_rek6'],
                        'debet' => $daftar_data[$key]['debet'],
                        'kredit' => $daftar_data[$key]['kredit'],
                        'rk' => $daftar_data[$key]['rk'],
                        'jns' => $daftar_data[$key]['jns'],
                        'kd_unit' => $data['kd_skpd'],
                        'map_real' => $daftar_data[$key]['kd_rek6'],
                        'pos' => $daftar_data[$key]['pos'],
                        'urut' => $key+1,
                    ];
                    DB::table('trdju_pkd')->insert($data_tampungan);
                }

                DB::commit();
                return response()->json([
                    'message' => '1'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => '0',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $no_voucher = $request->no_voucher;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();
        try {
            DB::delete("DELETE from trdju_pkd where no_voucher=? AND kd_unit=?", [$no_voucher, $kd_skpd]);

            DB::delete("DELETE from trhju_pkd where no_voucher=? AND kd_skpd=?", [$no_voucher, $kd_skpd]);

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
